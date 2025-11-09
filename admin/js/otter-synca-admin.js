(function($) {
    'use strict';

    $(document).ready(function() {
        console.log('OtterSynca: Document ready');
        console.log('OtterSynca: Deploy button exists:', $('#otter-synca-deploy').length > 0);

        // Deploy button click handler
        $('#otter-synca-deploy').on('click', function(e) {
            console.log('OtterSynca: Deploy button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const $status = $('#otter-synca-deploy-status');

            console.log('OtterSynca: Button state:', {
                disabled: $button.prop('disabled'),
                isTrusted: e.isTrusted,
                eventType: e.type
            });

            // Verifica se o botão já está em processo de deploy
            if ($button.prop('disabled')) {
                console.log('OtterSynca: Button is disabled');
                return;
            }

            // Verifica se o deploy foi iniciado manualmente
            if (e.type !== 'click') {
                console.log('OtterSynca: Event not a click');
                $status
                    .addClass('error')
                    .html('<p>' + otter_synca_admin.error + ': ' + otter_synca_admin.manual_deploy_message + '</p>');
                return;
            }

            // Disable button and show loading state
            $button.addClass('otter-synca-loading').prop('disabled', true);
            $status.addClass('show').html('<p>' + otter_synca_admin.deploying + '</p>');

            // Clear previous status
            $status.removeClass('success error');

            console.log('OtterSynca: Making AJAX request');

            // Make AJAX request
            $.ajax({
                url: otter_synca_admin.ajax_url,
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    action: 'otter_synca_deploy',
                    nonce: otter_synca_admin.nonce,
                    manual_deploy: 'true',
                    event_type: e.type,
                    is_trusted: e.isTrusted
                },
                success: function(response) {
                    console.log('OtterSynca: AJAX success:', response);
                    if (response.success) {
                        $status
                            .addClass('success')
                            .html('<p>' + response.data + '</p>');
                        
                        // Update last deploy info in sidebar
                        updateLastDeployInfo({
                            status: 'success',
                            message: response.data,
                            timestamp: new Date().toLocaleString()
                        });
                    } else {
                        $status
                            .addClass('error')
                            .html('<p>' + (response.data || otter_synca_admin.error) + '</p>');
                        
                        // Update last deploy info in sidebar
                        updateLastDeployInfo({
                            status: 'error',
                            message: response.data || otter_synca_admin.error,
                            timestamp: new Date().toLocaleString()
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('OtterSynca: AJAX error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    $status
                        .addClass('error')
                        .html('<p>' + otter_synca_admin.error + ': ' + error + '</p>');
                    
                    // Update last deploy info in sidebar
                    updateLastDeployInfo({
                        status: 'error',
                        message: otter_synca_admin.error + ': ' + error,
                        timestamp: new Date().toLocaleString()
                    });
                },
                complete: function() {
                    console.log('OtterSynca: AJAX complete');
                    // Re-enable button and remove loading state
                    $button.removeClass('otter-synca-loading').prop('disabled', false);
                }
            });
        });

        // Form validation
        $('form').on('submit', function(e) {
            const $form = $(this);
            const $requiredFields = $form.find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                const $field = $(this);
                if (!$field.val()) {
                    isValid = false;
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert(otter_synca_admin.required_fields_message);
        }
        });

        // Field focus handler
        $('input, select').on('focus', function() {
            $(this).removeClass('error');
        });

        // Repository field validation
        $('#otter_synca_repository').on('blur', function() {
            const $field = $(this);
            const value = $field.val();
            const pattern = /^[a-zA-Z0-9-]+\/[a-zA-Z0-9-]+$/;

            if (value && !pattern.test(value)) {
                $field.addClass('error');
                alert(otter_synca_admin.invalid_repository_message);
            }
        });

        // Token field show/hide
        const $tokenField = $('#otter_synca_github_token');
        const $tokenWrapper = $tokenField.parent();
        
        // Adiciona botão de mostrar/esconder
        $tokenWrapper.append(
            '<button type="button" class="button button-secondary otter-synca-toggle-token" style="margin-left: 10px;">' +
            'Show Token</button>'
        );
        
        // Handler do botão
        $('.otter-synca-toggle-token').on('click', function() {
            const $button = $(this);
            const isPassword = $tokenField.attr('type') === 'password';
            
            $tokenField.attr('type', isPassword ? 'text' : 'password');
            $button.text(isPassword ? 'Hide Token' : 'Show Token');
        });

        // Webhook secret field show/hide
        const $webhookSecretField = $('#otter_synca_webhook_secret');
        // Apenas adiciona o evento de mostrar/esconder se necessário
        if (!$("#otter_synca_webhook_secret").parent().find('.otter-synca-toggle-webhook-secret').length) {
            $webhookSecretField.parent().append(
                '<button type="button" class="button button-secondary otter-synca-toggle-webhook-secret" style="margin-left: 10px;">Mostrar Secret</button>'
            );
        }
        // Handler do botão mostrar/esconder
        $('.otter-synca-toggle-webhook-secret').on('click', function() {
            const $button = $(this);
            const isPassword = $webhookSecretField.attr('type') === 'password';
            $webhookSecretField.attr('type', isPassword ? 'text' : 'password');
            $button.text(isPassword ? 'Esconder Secret' : 'Mostrar Secret');
        });
        // Handler do botão gerar secret (apenas adiciona evento ao botão já existente no HTML)
        $('#generate-webhook-secret').on('click', function() {
            const secret = generateSecureSecret();
            $webhookSecretField.val(secret);
            showMessage('Webhook secret gerado com sucesso!', 'success');
        });

        function generateSecureSecret() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < 32; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        function showMessage(message, type) {
            const $messageDiv = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.wrap h1').after($messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $messageDiv.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Test webhook functionality
        $('.otter-synca-test-webhook').on('click', function() {
            const $button = $(this);
            const $result = $('.otter-synca-test-result');
            const nonce = $button.data('nonce');
            
            $button.prop('disabled', true).text('Testando...');
            $result.hide();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'otter_synca_test_webhook',
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<span style="color: green;">✓ ' + response.data.message + '</span>').show();
                    } else {
                        $result.html('<span style="color: red;">✗ ' + response.data.message + '</span>').show();
                    }
                },
                error: function() {
                    $result.html('<span style="color: red;">✗ Erro ao testar webhook</span>').show();
                },
                complete: function() {
                    $button.prop('disabled', false).text('Testar Webhook');
                }
            });
        });

        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            const $tab = $(this);
            const tabName = $tab.attr('href').split('tab=')[1];

            // Save current tab in localStorage
            localStorage.setItem('otter_synca_current_tab', tabName);
        });

        // Restore last active tab
        const lastTab = localStorage.getItem('otter_synca_current_tab');
        if (lastTab) {
            $('.nav-tab[href*="tab=' + lastTab + '"]').click();
        }

        // Helper function to update last deploy info
        function updateLastDeployInfo(deployInfo) {
            console.log('OtterSynca: Updating last deploy info:', deployInfo);
            const $sidebar = $('.otter-synca-admin-sidebar');
            const $status = $sidebar.find('.otter-synca-status');
            const $message = $sidebar.find('p').first();
            const $timestamp = $sidebar.find('.description');

            // Update status
            $status
                .removeClass('otter-synca-status-success otter-synca-status-error')
                .addClass(deployInfo.status === 'success' ? 'otter-synca-status-success' : 'otter-synca-status-error')
                .text(deployInfo.status === 'success' ? otter_synca_admin.success_text : otter_synca_admin.error_text);

            // Update message
            $message.text(deployInfo.message);

            // Update timestamp
            $timestamp.text(otter_synca_admin.deployed_at_text + ' ' + deployInfo.timestamp);
        }
    });

})(jQuery); 