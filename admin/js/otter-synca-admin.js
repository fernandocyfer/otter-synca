(function($) {
    'use strict';

    $(document).ready(function() {
        const $deployButton = $('#otter-synca-deploy');
        const $deployStatus = $('#otter-synca-deploy-status');

        $deployButton.on('click', function(e) {
            e.preventDefault();

            // Validate required fields
            const requiredFields = [
                'otter_synca_github_token',
                'otter_synca_repository',
                'otter_synca_target_slug'
            ];

            let isValid = true;
            requiredFields.forEach(function(field) {
                const $field = $('#' + field);
                if (!$field.val()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });

            if (!isValid) {
                showStatus('error', 'Please fill in all required fields.');
                return;
            }

            // Disable button and show loading state
            $deployButton.addClass('loading').prop('disabled', true);
            showStatus('', otter_synca.deploying);

            // Make AJAX request
            $.ajax({
                url: otter_synca.ajax_url,
                type: 'POST',
                data: {
                    action: 'otter_synca_deploy',
                    nonce: otter_synca.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showStatus('success', response.data);
                        // Reload page after 2 seconds to show updated last deploy info
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showStatus('error', response.data);
                    }
                },
                error: function() {
                    showStatus('error', 'An error occurred while processing your request.');
                },
                complete: function() {
                    $deployButton.removeClass('loading').prop('disabled', false);
                }
            });
        });

        function showStatus(type, message) {
            $deployStatus
                .removeClass('success error')
                .addClass(type)
                .addClass('show')
                .text(message);
        }

        // Remove error class on input
        $('input, select').on('input change', function() {
            $(this).removeClass('error');
        });
    });
})(jQuery); 