<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Shortcode to display the pool calculator
function pool_calculator_shortcode() {
    // Retrieve saved steps
    $steps = get_option('pool_calculator_steps', []);

    // Check if there are any steps defined
    if (empty($steps)) {
        return '<p>No steps defined. Please configure the calculator in the admin panel.</p>';
    }

    ob_start(); // Start output buffering
    ?>
    <div id="pool-calculator">
        <!-- Progress Bar -->
        <div id="progress-bar-container">
            <div id="progress-bar-fill"></div>
        </div>

        <!-- Form with Steps -->
        <form id="pool-calculator-form">
            <?php foreach ($steps as $stepIndex => $step) : ?>
                <div class="step" data-step-index="<?php echo esc_attr($stepIndex); ?>" style="display: <?php echo $stepIndex === 0 ? 'block' : 'none'; ?>;">
                    <!-- Step Title and Description -->
                    <div class="step-header">
                        <h2 class="step-title"><?php echo esc_html($step['title']); ?></h2>
                        <p class="step-description"><?php echo esc_html($step['description']); ?></p>
                    </div>

                    <fieldset>
                    <legend></legend> <!-- Empty legend to preserve accessibility if needed -->
                        <div class="selections-container">
                            <?php if (!empty($step['selections'])) : ?>
                                <?php foreach ($step['selections'] as $selectionIndex => $selection) : ?>
                                    <label class="selection-item">
                                        <input 
                                            type="<?php echo isset($step['allow_multiple']) && $step['allow_multiple'] ? 'checkbox' : 'radio'; ?>" 
                                            name="step_<?php echo esc_attr($stepIndex); ?>[]" 
                                            value="<?php echo esc_attr($selection['price']); ?>">
                                        <?php if (!empty($selection['image'])) : ?>
                                            <img src="<?php echo esc_url($selection['image']); ?>" alt="<?php echo esc_attr($selection['title']); ?>" class="selection-image" />
                                        <?php endif; ?>
                                        <span class="selection-title"><?php echo esc_html($selection['title']); ?></span>
                                        <p class="selection-description"><?php echo esc_html($selection['description']); ?></p>
                                    </label>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p>No selections available for this step.</p>
                            <?php endif; ?>
                        </div>
                        <div class="navigation-buttons">
                            <?php if ($stepIndex > 0) : ?>
                                <button type="button" class="prev-step">Previous</button>
                            <?php endif; ?>
                            <?php if ($stepIndex < count($steps) - 1) : ?>
                                <button type="button" class="next-step">Next</button>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                </div>
            <?php endforeach; ?>
        </form>

        <!-- Result Display -->
        <!-- Result Display -->
<div id="pool-calculator-result" style="display: none;">
    <h3 id="final-cost">Your Pool Starts From $<span id="total-cost">0</span></h3>
</div>
    </div>

    

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const steps = document.querySelectorAll('.step');
        const resultDiv = document.getElementById('pool-calculator-result');
        const totalCostSpan = document.getElementById('total-cost');
        const progressBarFill = document.getElementById('progress-bar-fill');

        let currentStep = 0;

        function updateProgressBar() {
            const progress = ((currentStep + 1) / steps.length) * 100;
            progressBarFill.style.width = `${progress}%`;
        }

        function showStep(index) {
            steps.forEach((step, i) => {
                step.style.display = i === index ? 'block' : 'none';
            });
            updateProgressBar();
        }

        function calculateCost() {
            let totalCost = 0;
            const inputs = document.querySelectorAll('input:checked');
            inputs.forEach(input => {
                totalCost += parseFloat(input.value);
            });

            // Round to the nearest dollar and format with commas
            const formattedCost = Math.round(totalCost).toLocaleString('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
            });

            totalCostSpan.textContent = formattedCost.replace('$', ''); // Remove the dollar symbol for the span
        }

        document.querySelectorAll('.next-step').forEach(button => {
            button.addEventListener('click', function () {
                currentStep++;
                showStep(currentStep);
            });
        });

        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', function () {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        steps[steps.length - 1].querySelectorAll('input').forEach(input => {
            input.addEventListener('change', function () {
                calculateCost();
                resultDiv.style.display = 'block';
            });
        });

        // Initialize
        showStep(currentStep);
    });
</script>

    <?php
    return ob_get_clean();
}
add_shortcode('pool_calculator', 'pool_calculator_shortcode');
