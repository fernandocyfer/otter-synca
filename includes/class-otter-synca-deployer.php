<?php
/**
 * Secure deployment service shared by manual and webhook deployments.
 *
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Otter_Synca_Deployer {

    const LOCK_OPTION = 'otter_synca_deploy_lock';
    const LOCK_TTL = 600;
    const MAX_ARCHIVE_SIZE = 104857600;
    const MAX_EXTRACTED_SIZE = 262144000;
    const MAX_ARCHIVE_FILES = 10000;
    const ENCRYPTED_PREFIX = 'otter:v1:';

    /**
     * Encrypt a stored credential using the site's authentication salt.
     *
     * @param string $value Plain credential.
     * @return string
     */
    public static function encrypt_secret($value) {
        $value = (string) $value;
        if ('' === $value || 0 === strpos($value, self::ENCRYPTED_PREFIX)) {
            return $value;
        }

        if (!function_exists('openssl_encrypt')) {
            return $value;
        }

        try {
            $nonce = random_bytes(12);
        } catch (Exception $exception) {
            return $value;
        }

        $tag = '';
        $key = hash('sha256', wp_salt('auth') . '|otter-synca|', true);
        $ciphertext = openssl_encrypt(
            $value,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        if (false === $ciphertext || 16 !== strlen($tag)) {
            return $value;
        }

        return self::ENCRYPTED_PREFIX . base64_encode($nonce . $tag . $ciphertext);
    }

    /**
     * Decrypt a credential loaded from the options table.
     *
     * @param mixed $value Stored value.
     * @return string
     */
    public static function decrypt_secret($value) {
        $value = (string) $value;
        if (0 !== strpos($value, self::ENCRYPTED_PREFIX)) {
            return $value;
        }

        if (!function_exists('openssl_decrypt')) {
            return '';
        }

        $encoded = substr($value, strlen(self::ENCRYPTED_PREFIX));
        $payload = base64_decode($encoded, true);
        if (false === $payload || strlen($payload) < 29) {
            return '';
        }

        $nonce = substr($payload, 0, 12);
        $tag = substr($payload, 12, 16);
        $ciphertext = substr($payload, 28);
        $key = hash('sha256', wp_salt('auth') . '|otter-synca|', true);
        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        return false === $plaintext ? '' : $plaintext;
    }

    /**
     * Resolve a branch to an immutable Git commit SHA.
     *
     * @param string $repository Repository in owner/name format.
     * @param string $branch     Branch name.
     * @param string $token      GitHub access token.
     * @return string
     * @throws RuntimeException When the branch cannot be resolved.
     */
    public static function resolve_commit($repository, $branch, $token) {
        self::validate_repository($repository);
        self::validate_branch($branch);

        if (empty($token)) {
            throw new RuntimeException(__('GitHub token is required.', 'otter-synca'));
        }

        list($owner, $name) = explode('/', $repository, 2);
        $url = sprintf(
            'https://api.github.com/repos/%1$s/%2$s/branches/%3$s',
            rawurlencode($owner),
            rawurlencode($name),
            rawurlencode($branch)
        );

        $response = wp_safe_remote_get($url, self::request_args($token, 30));

        if (is_wp_error($response)) {
            throw new RuntimeException($response->get_error_message());
        }

        $status = wp_remote_retrieve_response_code($response);
        if (200 !== $status) {
            throw new RuntimeException(self::github_error_message($status));
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $sha = isset($data['commit']['sha']) ? $data['commit']['sha'] : '';

        if (!preg_match('/^[a-f0-9]{40}$/i', $sha)) {
            throw new RuntimeException(__('GitHub returned an invalid commit identifier.', 'otter-synca'));
        }

        return strtolower($sha);
    }

    /**
     * Download and deploy an immutable GitHub commit.
     *
     * @param string $repository Repository in owner/name format.
     * @param string $commit_sha Full Git commit SHA.
     * @param string $token      GitHub access token.
     * @param string $deploy_type plugin or theme.
     * @param string $target_slug Destination directory name.
     * @return void
     * @throws RuntimeException When deployment fails.
     */
    public static function deploy($repository, $commit_sha, $token, $deploy_type, $target_slug) {
        self::validate_repository($repository);
        self::validate_commit_sha($commit_sha);
        self::validate_deploy_type($deploy_type);
        self::validate_target_slug($target_slug);

        if (empty($token)) {
            throw new RuntimeException(__('GitHub token is required.', 'otter-synca'));
        }

        self::acquire_lock();

        $archive_path = '';
        $extract_path = '';

        try {
            $archive_path = self::download_archive($repository, $commit_sha, $token);
            $extract_path = self::extract_archive($archive_path);
            $source_dir = self::get_archive_source($extract_path);
            self::replace_target($source_dir, $deploy_type, $target_slug);
        } finally {
            if ($archive_path && file_exists($archive_path)) {
                wp_delete_file($archive_path);
            }

            if ($extract_path) {
                self::remove_directory($extract_path);
            }

            self::release_lock();
        }
    }

    /**
     * Validate repository format.
     *
     * @param string $repository Repository value.
     * @return void
     * @throws InvalidArgumentException When invalid.
     */
    public static function validate_repository($repository) {
        if (!is_string($repository) || !preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})\/[A-Za-z0-9._-]{1,100}$/', $repository)) {
            throw new InvalidArgumentException(__('Invalid repository format. Use owner/repository.', 'otter-synca'));
        }
    }

    /**
     * Validate a Git branch name.
     *
     * @param string $branch Branch name.
     * @return void
     * @throws InvalidArgumentException When invalid.
     */
    public static function validate_branch($branch) {
        $invalid = !is_string($branch)
            || '' === $branch
            || strlen($branch) > 255
            || false !== strpos($branch, '..')
            || '/' === substr($branch, 0, 1)
            || '/' === substr($branch, -1)
            || !preg_match('/^[A-Za-z0-9._\/-]+$/', $branch);

        if ($invalid) {
            throw new InvalidArgumentException(__('Invalid branch name.', 'otter-synca'));
        }
    }

    /**
     * Validate destination slug.
     *
     * @param string $slug Destination slug.
     * @return void
     * @throws InvalidArgumentException When invalid.
     */
    public static function validate_target_slug($slug) {
        if (!is_string($slug) || !preg_match('/^[a-z0-9][a-z0-9_-]{0,99}$/', $slug)) {
            throw new InvalidArgumentException(__('Invalid target slug. Use lowercase letters, numbers, hyphens, or underscores.', 'otter-synca'));
        }
    }

    /**
     * Validate deployment type.
     *
     * @param string $deploy_type Deployment type.
     * @return void
     * @throws InvalidArgumentException When invalid.
     */
    public static function validate_deploy_type($deploy_type) {
        if (!in_array($deploy_type, array('plugin', 'theme'), true)) {
            throw new InvalidArgumentException(__('Invalid deployment type.', 'otter-synca'));
        }
    }

    /**
     * Validate a full Git SHA.
     *
     * @param string $commit_sha Commit SHA.
     * @return void
     * @throws InvalidArgumentException When invalid.
     */
    private static function validate_commit_sha($commit_sha) {
        if (!is_string($commit_sha) || !preg_match('/^[a-f0-9]{40}$/i', $commit_sha)) {
            throw new InvalidArgumentException(__('Invalid commit identifier.', 'otter-synca'));
        }
    }

    /**
     * Build common GitHub request arguments.
     *
     * @param string $token   GitHub token.
     * @param int    $timeout Timeout in seconds.
     * @return array
     */
    private static function request_args($token, $timeout) {
        return array(
            'timeout'     => $timeout,
            'redirection' => 5,
            'limit_response_size' => 1048576,
            'headers'     => array(
                'Authorization'        => 'Bearer ' . $token,
                'Accept'               => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent'           => 'OtterSynca/' . (defined('OTTER_SYNCA_VERSION') ? OTTER_SYNCA_VERSION : '1.0'),
            ),
        );
    }

    /**
     * Download a GitHub archive directly to a unique temporary file.
     *
     * @param string $repository Repository.
     * @param string $commit_sha Commit SHA.
     * @param string $token      GitHub token.
     * @return string
     * @throws RuntimeException When download fails.
     */
    private static function download_archive($repository, $commit_sha, $token) {
        if (!function_exists('wp_tempnam')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $archive_path = wp_tempnam('otter-synca-' . $commit_sha . '.zip');
        if (!$archive_path) {
            throw new RuntimeException(__('Could not create a temporary archive file.', 'otter-synca'));
        }

        list($owner, $name) = explode('/', $repository, 2);
        $url = sprintf(
            'https://api.github.com/repos/%1$s/%2$s/zipball/%3$s',
            rawurlencode($owner),
            rawurlencode($name),
            rawurlencode($commit_sha)
        );

        $args = self::request_args($token, 120);
        $args['stream'] = true;
        $args['filename'] = $archive_path;
        $args['limit_response_size'] = self::MAX_ARCHIVE_SIZE + 1;

        $response = wp_safe_remote_get($url, $args);

        if (is_wp_error($response)) {
            wp_delete_file($archive_path);
            throw new RuntimeException($response->get_error_message());
        }

        $status = wp_remote_retrieve_response_code($response);
        if (200 !== $status) {
            wp_delete_file($archive_path);
            throw new RuntimeException(self::github_error_message($status));
        }

        $size = file_exists($archive_path) ? filesize($archive_path) : 0;
        if (!$size || $size > self::MAX_ARCHIVE_SIZE) {
            wp_delete_file($archive_path);
            throw new RuntimeException(__('The repository archive is empty or exceeds the 100 MB limit.', 'otter-synca'));
        }

        $handle = fopen($archive_path, 'rb');
        $signature = $handle ? fread($handle, 4) : '';
        if ($handle) {
            fclose($handle);
        }

        if (0 !== strpos($signature, 'PK')) {
            wp_delete_file($archive_path);
            throw new RuntimeException(__('GitHub did not return a valid ZIP archive.', 'otter-synca'));
        }

        return $archive_path;
    }

    /**
     * Validate every ZIP entry before extraction to prevent path traversal.
     *
     * @param string $archive_path ZIP file path.
     * @return string Extraction directory.
     * @throws RuntimeException When the archive is unsafe.
     */
    private static function extract_archive($archive_path) {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException(__('The PHP ZIP extension is required for deployments.', 'otter-synca'));
        }

        $extract_path = trailingslashit(get_temp_dir()) . 'otter-synca-' . wp_generate_uuid4();
        if (!wp_mkdir_p($extract_path)) {
            throw new RuntimeException(__('Could not create a temporary extraction directory.', 'otter-synca'));
        }

        $zip = new ZipArchive();
        if (true !== $zip->open($archive_path)) {
            self::remove_directory($extract_path);
            throw new RuntimeException(__('Failed to open the downloaded ZIP archive.', 'otter-synca'));
        }

        try {
            if ($zip->numFiles > self::MAX_ARCHIVE_FILES) {
                throw new RuntimeException(__('The repository archive contains too many files.', 'otter-synca'));
            }

            $extracted_size = 0;
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = $zip->statIndex($index);
                $name = isset($entry['name']) ? $entry['name'] : '';
                $extracted_size += isset($entry['size']) ? (int) $entry['size'] : 0;

                if (!self::is_safe_archive_entry($zip, $index, $name)) {
                    throw new RuntimeException(__('The repository archive contains an unsafe path or symbolic link.', 'otter-synca'));
                }

                if ($extracted_size > self::MAX_EXTRACTED_SIZE) {
                    throw new RuntimeException(__('The extracted repository exceeds the 250 MB limit.', 'otter-synca'));
                }
            }

            if (!$zip->extractTo($extract_path)) {
                throw new RuntimeException(__('Failed to extract the repository archive.', 'otter-synca'));
            }
        } catch (Exception $exception) {
            $zip->close();
            self::remove_directory($extract_path);
            throw $exception;
        }

        $zip->close();
        return $extract_path;
    }

    /**
     * Determine whether a ZIP entry is safe to extract.
     *
     * @param ZipArchive $zip   Archive instance.
     * @param int        $index Entry index.
     * @param string     $name  Entry name.
     * @return bool
     */
    private static function is_safe_archive_entry($zip, $index, $name) {
        if (
            '' === $name
            || strlen($name) > 4096
            || false !== strpos($name, "\0")
            || false !== strpos($name, '\\')
            || preg_match('#^(?:/|[A-Za-z]:)#', $name)
            || preg_match('#(?:^|/)\.\.(?:/|$)#', $name)
        ) {
            return false;
        }

        if (method_exists($zip, 'getExternalAttributesIndex')) {
            $operations = 0;
            $attributes = 0;
            if ($zip->getExternalAttributesIndex($index, $operations, $attributes)) {
                $file_type = ($attributes >> 16) & 0170000;
                if (0120000 === $file_type) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Locate the single root directory GitHub places in its ZIP archives.
     *
     * @param string $extract_path Extraction path.
     * @return string
     * @throws RuntimeException When archive structure is invalid.
     */
    private static function get_archive_source($extract_path) {
        $entries = array_values(array_diff(scandir($extract_path), array('.', '..')));

        if (1 !== count($entries)) {
            throw new RuntimeException(__('The repository archive has an unexpected structure.', 'otter-synca'));
        }

        $source = realpath($extract_path . DIRECTORY_SEPARATOR . $entries[0]);
        $root = realpath($extract_path);

        if (!$source || !$root || !is_dir($source) || 0 !== strpos($source, $root . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException(__('The repository archive root is invalid.', 'otter-synca'));
        }

        return $source;
    }

    /**
     * Stage files and replace the target directory with rollback on failure.
     *
     * @param string $source_dir  Extracted source.
     * @param string $deploy_type plugin or theme.
     * @param string $target_slug Target slug.
     * @return void
     * @throws RuntimeException When replacement fails.
     */
    private static function replace_target($source_dir, $deploy_type, $target_slug) {
        $base_dir = 'plugin' === $deploy_type ? WP_PLUGIN_DIR : get_theme_root();
        $base_real = realpath($base_dir);

        if (!$base_real || !is_dir($base_real) || !is_writable($base_real)) {
            throw new RuntimeException(__('The deployment directory is not writable.', 'otter-synca'));
        }

        $target_dir = $base_real . DIRECTORY_SEPARATOR . $target_slug;
        $suffix = str_replace('-', '', wp_generate_uuid4());
        $stage_dir = $base_real . DIRECTORY_SEPARATOR . '.otter-synca-stage-' . $suffix;
        $backup_dir = $base_real . DIRECTORY_SEPARATOR . '.otter-synca-backup-' . $suffix;

        $target_moved = false;
        try {
            self::copy_directory($source_dir, $stage_dir);

            if (file_exists($target_dir)) {
                if (is_link($target_dir) || !is_dir($target_dir)) {
                    throw new RuntimeException(__('The deployment target is not a valid directory.', 'otter-synca'));
                }

                if (!rename($target_dir, $backup_dir)) {
                    throw new RuntimeException(__('Could not create a rollback copy of the current version.', 'otter-synca'));
                }
                $target_moved = true;
            }

            if (!rename($stage_dir, $target_dir)) {
                if ($target_moved) {
                    rename($backup_dir, $target_dir);
                }
                throw new RuntimeException(__('Could not activate the downloaded version.', 'otter-synca'));
            }

            if ($target_moved) {
                self::remove_directory($backup_dir);
            }
        } catch (Exception $exception) {
            self::remove_directory($stage_dir);
            throw $exception;
        }
    }

    /**
     * Copy a directory without following symbolic links.
     *
     * @param string $source Source directory.
     * @param string $dest   Destination directory.
     * @return void
     * @throws RuntimeException When copying fails.
     */
    private static function copy_directory($source, $dest) {
        if (!wp_mkdir_p($dest)) {
            throw new RuntimeException(__('Could not create the deployment staging directory.', 'otter-synca'));
        }

        $iterator = new FilesystemIterator($source, FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $item) {
            if ($item->isLink()) {
                throw new RuntimeException(__('Symbolic links are not allowed in deployments.', 'otter-synca'));
            }

            $destination = $dest . DIRECTORY_SEPARATOR . $item->getFilename();
            if ($item->isDir()) {
                self::copy_directory($item->getPathname(), $destination);
            } elseif (!$item->isFile() || !copy($item->getPathname(), $destination)) {
                throw new RuntimeException(__('Failed to copy a repository file.', 'otter-synca'));
            }
        }
    }

    /**
     * Remove a directory recursively without following links.
     *
     * @param string $dir Directory path.
     * @return void
     */
    private static function remove_directory($dir) {
        if (!$dir || (!file_exists($dir) && !is_link($dir))) {
            return;
        }

        if (is_link($dir) || is_file($dir)) {
            wp_delete_file($dir);
            return;
        }

        try {
            $iterator = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
        } catch (UnexpectedValueException $exception) {
            return;
        }

        foreach ($iterator as $item) {
            self::remove_directory($item->getPathname());
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    /**
     * Acquire a cross-request deployment lock.
     *
     * @return void
     * @throws RuntimeException When another deploy is active.
     */
    private static function acquire_lock() {
        $now = time();
        if (add_option(self::LOCK_OPTION, $now, '', false)) {
            return;
        }

        $created = (int) get_option(self::LOCK_OPTION, 0);
        if ($created && ($now - $created) > self::LOCK_TTL) {
            delete_option(self::LOCK_OPTION);
            if (add_option(self::LOCK_OPTION, $now, '', false)) {
                return;
            }
        }

        throw new RuntimeException(__('Another deployment is already in progress. Try again in a few minutes.', 'otter-synca'));
    }

    /**
     * Release deployment lock.
     *
     * @return void
     */
    private static function release_lock() {
        delete_option(self::LOCK_OPTION);
    }

    /**
     * Convert GitHub response status into a safe user-facing message.
     *
     * @param int $status HTTP status.
     * @return string
     */
    private static function github_error_message($status) {
        if (401 === $status || 403 === $status) {
            return __('GitHub denied the request. Verify the token and repository permissions.', 'otter-synca');
        }

        if (404 === $status) {
            return __('Repository, branch, or commit not found on GitHub.', 'otter-synca');
        }

        return sprintf(
            /* translators: %d: GitHub HTTP response status. */
            __('GitHub request failed with HTTP status %d.', 'otter-synca'),
            (int) $status
        );
    }
}
