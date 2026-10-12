(function($) {
    'use strict';

    $(function() {
        const $admin = $('.otter-synca-admin');

        $('#otter-synca-deploy').on('click', function(event) {
            event.preventDefault();

            const $button = $(this);
            const $status = $('#otter-synca-deploy-status');

            if ($button.prop('disabled')) {
                return;
            }

            $button.addClass('otter-synca-loading').prop('disabled', true);
            setStatus($status, otter_synca_admin.deploying, '');

            $.ajax({
                url: otter_synca_admin.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'otter_synca_deploy',
                    nonce: otter_synca_admin.nonce
                }
            }).done(function(response) {
                const message = getResponseMessage(
                    response,
                    response.success ? otter_synca_admin.success : otter_synca_admin.error
                );
                const status = response.success ? 'success' : 'error';

                setStatus($status, message, status);
                updateLastDeployInfo(status, message);
            }).fail(function(xhr) {
                const response = xhr.responseJSON || {};
                const message = getResponseMessage(response, otter_synca_admin.error);

                setStatus($status, message, 'error');
                updateLastDeployInfo('error', message);
            }).always(function() {
                $button.removeClass('otter-synca-loading').prop('disabled', false);
            });
        });

        $admin.find('form').on('submit', function(event) {
            let valid = true;

            $(this).find('[required]:enabled').each(function() {
                const $field = $(this);
                const fieldValid = this.checkValidity() && Boolean($field.val());
                $field.toggleClass('error', !fieldValid);
                valid = valid && fieldValid;
            });

            if (!valid) {
                event.preventDefault();
                window.alert(otter_synca_admin.required_fields_message);
            }
        });

        $admin.find('input, select').on('focus', function() {
            $(this).removeClass('error');
        });

        $('#otter_synca_repository').on('blur', function() {
            const $field = $(this);
            const value = $field.val().trim();
            const pattern = /^[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})\/[A-Za-z0-9._-]{1,100}$/;

            if (value && !pattern.test(value)) {
                $field.addClass('error');
                window.alert(otter_synca_admin.invalid_repository_message);
            }
        });

        addVisibilityToggle(
            $('#otter_synca_github_token'),
            'otter-synca-toggle-token',
            otter_synca_admin.show_token,
            otter_synca_admin.hide_token
        );
        addVisibilityToggle(
            $('#otter_synca_webhook_secret'),
            'otter-synca-toggle-webhook-secret',
            otter_synca_admin.show_secret,
            otter_synca_admin.hide_secret
        );

        $('#generate-webhook-secret').on('click', function() {
            const $field = $('#otter_synca_webhook_secret');
            if (!$field.length || !window.crypto || !window.crypto.getRandomValues) {
                showNotice(otter_synca_admin.webhook_error, 'error');
                return;
            }

            const bytes = new Uint8Array(32);
            window.crypto.getRandomValues(bytes);
            const secret = Array.from(bytes, function(byte) {
                return byte.toString(16).padStart(2, '0');
            }).join('');

            $field.val(secret).trigger('change');
            showNotice(otter_synca_admin.secret_generated, 'success');
        });

        $('.otter-synca-test-webhook').on('click', function() {
            const $button = $(this);
            const $result = $('.otter-synca-test-result');

            $button.prop('disabled', true).text(otter_synca_admin.validating_webhook);
            $result.removeClass('success error').empty().hide();

            $.ajax({
                url: otter_synca_admin.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'otter_synca_test_webhook',
                    nonce: $button.data('nonce')
                }
            }).done(function(response) {
                const status = response.success ? 'success' : 'error';
                const message = getResponseMessage(response, otter_synca_admin.webhook_error);
                $result.addClass(status).text(message).show();
            }).fail(function(xhr) {
                const message = getResponseMessage(xhr.responseJSON || {}, otter_synca_admin.webhook_error);
                $result.addClass('error').text(message).show();
            }).always(function() {
                $button.prop('disabled', false).text(otter_synca_admin.validate_webhook);
            });
        });

        function addVisibilityToggle($field, className, showLabel, hideLabel) {
            if (!$field.length || $field.siblings('.' + className).length) {
                return;
            }

            const $button = $('<button>', {
                type: 'button',
                class: 'button button-secondary ' + className,
                text: showLabel
            });

            $field.after($button);
            $button.on('click', function() {
                const hidden = $field.attr('type') === 'password';
                $field.attr('type', hidden ? 'text' : 'password');
                $button.text(hidden ? hideLabel : showLabel);
            });
        }

        function getResponseMessage(response, fallback) {
            if (response && response.data) {
                if (typeof response.data === 'string') {
                    return response.data;
                }
                if (response.data.message) {
                    return response.data.message;
                }
            }
            return fallback;
        }

        function setStatus($element, message, status) {
            $element
                .removeClass('success error')
                .addClass('show');

            if (status) {
                $element.addClass(status);
            }

            $element.empty().append($('<p>').text(message));
        }

        function showNotice(message, type) {
            const $notice = $('<div>', {
                class: 'notice notice-' + type + ' is-dismissible'
            }).append($('<p>').text(message));

            $('.wrap h1').first().after($notice);
            window.setTimeout(function() {
                $notice.fadeOut(function() {
                    $notice.remove();
                });
            }, 5000);
        }

        function updateLastDeployInfo(status, message) {
            const $card = $('#otter-synca-last-deploy');
            if (!$card.length) {
                return;
            }

            $card.find('[data-otter-synca-last-status]')
                .removeClass('otter-synca-status-success otter-synca-status-error')
                .addClass(status === 'success' ? 'otter-synca-status-success' : 'otter-synca-status-error')
                .text(status === 'success' ? otter_synca_admin.success_text : otter_synca_admin.error_text);
            $card.find('[data-otter-synca-last-message]').text(message);
            $card.find('[data-otter-synca-last-time]').text(
                otter_synca_admin.deployed_at_text + ' ' + new Date().toLocaleString()
            );
        }
    });
})(jQuery);
