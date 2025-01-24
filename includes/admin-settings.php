<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add admin menu
function pool_calculator_add_admin_menu() {
    add_menu_page(
        'Pool Calculator',
        'Pool Calculator',
        'manage_options',
        'pool-calculator',
        'pool_calculator_admin_page',
        'dashicons-admin-generic',
        20
    );
}
add_action('admin_menu', 'pool_calculator_add_admin_menu');

// Enqueue WordPress Media Uploader script
function pool_calculator_enqueue_media_uploader($hook) {
    if ($hook !== 'toplevel_page_pool-calculator') {
        return;
    }

    wp_enqueue_media(); // Enqueue the media uploader
    wp_enqueue_script(
        'pool-calculator-admin-script',
        plugins_url('../assets/js/pool-calculator-admin.js', __FILE__), // Ensure this path matches your plugin structure
        ['jquery'],
        false,
        true
    );
    wp_enqueue_style(
        'pool-calculator-admin-style',
        plugins_url('../assets/css/pool-calculator-admin.css', __FILE__), // Ensure this path matches your plugin structure
        [],
        false
    );
}
add_action('admin_enqueue_scripts', 'pool_calculator_enqueue_media_uploader');

// Admin page content
function pool_calculator_admin_page() {
    $steps = get_option('pool_calculator_steps', []);
    ?>
    <div class="wrap">
        <h1>Pool Calculator Settings</h1>
        <form id="pool-calculator-settings-form" method="post" action="options.php">
            <?php
            settings_fields('pool_calculator_settings_group');
            do_settings_sections('pool-calculator');
            ?>

            <div id="steps-container">
                <?php if (!empty($steps)) : ?>
                    <?php foreach ($steps as $stepIndex => $step) : ?>
                        <div class="step" data-step-index="<?php echo esc_attr($stepIndex); ?>">
                            <h3>
                                Step <span><?php echo esc_html($stepIndex + 1); ?></span>
                                <button type="button" class="delete-step-button button-link-delete">Delete Step</button>
                            </h3>
                            <input type="text" name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][title]" value="<?php echo esc_attr($step['title'] ?? ''); ?>" placeholder="Step Title" class="regular-text" />
                            <textarea name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][description]" placeholder="Step Description" class="large-text"><?php echo esc_textarea($step['description'] ?? ''); ?></textarea>
                            <button class="button add-selection">Add Selection</button>
                            <div class="selections">
                                <?php if (!empty($step['selections'])) : ?>
                                    <?php foreach ($step['selections'] as $selectionIndex => $selection) : ?>
                                        <div class="selection">
                                            <h4>Selection <span><?php echo esc_html($selectionIndex + 1); ?></span></h4>
                                            <input type="text" name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][selections][<?php echo esc_attr($selectionIndex); ?>][title]" value="<?php echo esc_attr($selection['title'] ?? ''); ?>" placeholder="Selection Title" class="regular-text" />
                                            <input type="number" name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][selections][<?php echo esc_attr($selectionIndex); ?>][price]" value="<?php echo esc_attr($selection['price'] ?? ''); ?>" placeholder="Price" class="small-text" />
                                            <textarea name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][selections][<?php echo esc_attr($selectionIndex); ?>][description]" placeholder="Description" class="large-text"><?php echo esc_textarea($selection['description'] ?? ''); ?></textarea>
                                            <input type="hidden" name="pool_calculator_steps[<?php echo esc_attr($stepIndex); ?>][selections][<?php echo esc_attr($selectionIndex); ?>][image]" value="<?php echo esc_attr($selection['image'] ?? ''); ?>" class="image-url" />
                                            <button type="button" class="upload-image-button button">Upload Image</button>
                                            <img src="<?php echo esc_url($selection['image'] ?? ''); ?>" alt="Preview" class="image-preview" style="max-width: 100px; margin-top: 10px; <?php echo empty($selection['image']) ? 'display: none;' : ''; ?>" />
                                            <button type="button" class="remove-image-button button-link-delete" style="<?php echo empty($selection['image']) ? 'display: none;' : ''; ?>">Remove Image</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php submit_button(); ?>
        </form>
        <button id="add-step" class="button button-primary">Add Step</button>
    </div>

    <script>
        (function ($) {
            // Add media uploader
            $(document).on('click', '.upload-image-button', function (e) {
                e.preventDefault();

                const button = $(this);
                const input = button.siblings('.image-url');
                const preview = button.siblings('.image-preview');
                const removeButton = button.siblings('.remove-image-button');

                const mediaUploader = wp.media({
                    title: 'Select or Upload an Image',
                    button: {
                        text: 'Use this Image',
                    },
                    multiple: false,
                }).on('select', function () {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    preview.attr('src', attachment.url).show();
                    removeButton.show();
                }).open();
            });

            // Remove image
            $(document).on('click', '.remove-image-button', function (e) {
                e.preventDefault();

                const button = $(this);
                const input = button.siblings('.image-url');
                const preview = button.siblings('.image-preview');

                input.val('');
                preview.hide();
                button.hide();
            });

            // Add new step
            $(document).on('click', '#add-step', function (e) {
                e.preventDefault();
                const stepCount = $('.step').length;
                const newStep = `
                    <div class="step" data-step-index="${stepCount}">
                        <h3>
                            Step <span>${stepCount + 1}</span>
                            <button type="button" class="delete-step-button button-link-delete">Delete Step</button>
                        </h3>
                        <input type="text" name="pool_calculator_steps[${stepCount}][title]" placeholder="Step Title" class="regular-text" />
                        <textarea name="pool_calculator_steps[${stepCount}][description]" placeholder="Step Description" class="large-text"></textarea>
                        <button class="button add-selection">Add Selection</button>
                        <div class="selections"></div>
                    </div>`;
                $('#steps-container').append(newStep);
            });

            // Add new selection
            $(document).on('click', '.add-selection', function (e) {
                e.preventDefault();
                const step = $(this).closest('.step');
                const stepIndex = step.data('step-index');
                const selectionCount = step.find('.selection').length;
                const newSelection = `
                    <div class="selection">
                        <h4>Selection <span>${selectionCount + 1}</span></h4>
                        <input type="text" name="pool_calculator_steps[${stepIndex}][selections][${selectionCount}][title]" placeholder="Selection Title" class="regular-text" />
                        <input type="number" name="pool_calculator_steps[${stepIndex}][selections][${selectionCount}][price]" placeholder="Price" class="small-text" />
                        <textarea name="pool_calculator_steps[${stepIndex}][selections][${selectionCount}][description]" placeholder="Description" class="large-text"></textarea>
                        <input type="hidden" name="pool_calculator_steps[${stepIndex}][selections][${selectionCount}][image]" class="image-url" />
                        <button type="button" class="upload-image-button button">Upload Image</button>
                        <img src="" alt="Preview" class="image-preview" style="max-width: 100px; margin-top: 10px; display: none;" />
                        <button type="button" class="remove-image-button button-link-delete" style="display: none;">Remove Image</button>
                    </div>`;
                step.find('.selections').append(newSelection);
            });

            // Delete step
            $(document).on('click', '.delete-step-button', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this step?')) {
                    $(this).closest('.step').remove();
                }
            });
        })(jQuery);
    </script>
    <?php
}

// Register settings
function pool_calculator_register_settings() {
    register_setting('pool_calculator_settings_group', 'pool_calculator_steps');
}
add_action('admin_init', 'pool_calculator_register_settings');
