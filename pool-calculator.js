document.addEventListener("DOMContentLoaded", function () {
    const stepsJsonInput = document.getElementById("pool-calculator-steps-json");
    const stepsData = JSON.parse(stepsJsonInput.value || "[]");
    const adminContainer = document.getElementById("pool-calculator-admin");

    function renderSteps() {
        adminContainer.innerHTML = "";

        stepsData.forEach((step, index) => {
            const stepDiv = document.createElement("div");
            stepDiv.classList.add("admin-step");
            stepDiv.innerHTML = `
                <h3>Step ${index + 1}</h3>
                <label>Step Title:</label>
                <input type="text" class="step-title" value="${step.title || ""}" placeholder="Enter Step Title" />
                <label>Step Description:</label>
                <textarea class="step-description" placeholder="Enter Step Description">${step.description || ""}</textarea>
                <label>Allow Multiple Selections:</label>
                <input type="checkbox" class="allow-multiple" ${step.allow_multiple ? "checked" : ""} />
                <button type="button" class="button button-secondary add-selection">Add Selection</button>
                <div class="selections">
                    ${step.selections
                        .map(
                            (selection) => `
                        <div class="selection">
                            <label>Title:</label>
                            <input type="text" class="selection-title" value="${selection.title || ""}" placeholder="Enter Selection Title" />
                            <label>Price:</label>
                            <input type="number" class="selection-price" value="${selection.price || 0}" placeholder="Enter Price" />
                            <label>Image URL:</label>
                            <input type="text" class="selection-image" value="${selection.image || ""}" placeholder="Enter Image URL" />
                            <button type="button" class="button button-link-delete remove-selection">Remove</button>
                        </div>
                    `
                        )
                        .join("")}
                </div>
                <button type="button" class="button button-link-delete remove-step">Remove Step</button>
            `;
            adminContainer.appendChild(stepDiv);
        });

        const addStepButton = document.createElement("button");
        addStepButton.type = "button";
        addStepButton.className = "button button-primary";
        addStepButton.textContent = "Add Step";
        addStepButton.addEventListener("click", () => {
            stepsData.push({
                title: "",
                description: "",
                allow_multiple: false,
                selections: [],
            });
            renderSteps();
        });
        adminContainer.appendChild(addStepButton);

        // Event Listeners
        adminContainer.querySelectorAll(".remove-step").forEach((button, stepIndex) => {
            button.addEventListener("click", () => {
                stepsData.splice(stepIndex, 1);
                renderSteps();
            });
        });

        adminContainer.querySelectorAll(".add-selection").forEach((button, stepIndex) => {
            button.addEventListener("click", () => {
                stepsData[stepIndex].selections.push({
                    title: "",
                    price: 0,
                    image: "",
                });
                renderSteps();
            });
        });

        adminContainer.querySelectorAll(".remove-selection").forEach((button, selectionIndex) => {
            button.addEventListener("click", (event) => {
                const stepIndex = Array.from(adminContainer.children).indexOf(
                    button.closest(".admin-step")
                );
                stepsData[stepIndex].selections.splice(selectionIndex, 1);
                renderSteps();
            });
        });

        // Update data bindings
        adminContainer.querySelectorAll(".step-title").forEach((input, index) => {
            input.addEventListener("input", () => {
                stepsData[index].title = input.value;
            });
        });

        adminContainer.querySelectorAll(".step-description").forEach((textarea, index) => {
            textarea.addEventListener("input", () => {
                stepsData[index].description = textarea.value;
            });
        });

        adminContainer.querySelectorAll(".allow-multiple").forEach((checkbox, index) => {
            checkbox.addEventListener("change", () => {
                stepsData[index].allow_multiple = checkbox.checked;
            });
        });

        adminContainer.querySelectorAll(".selection-title").forEach((input, index) => {
            const stepIndex = Array.from(adminContainer.children).indexOf(
                input.closest(".admin-step")
            );
            input.addEventListener("input", () => {
                stepsData[stepIndex].selections[index].title = input.value;
            });
        });

        adminContainer.querySelectorAll(".selection-price").forEach((input, index) => {
            const stepIndex = Array.from(adminContainer.children).indexOf(
                input.closest(".admin-step")
            );
            input.addEventListener("input", () => {
                stepsData[stepIndex].selections[index].price = parseFloat(
                    input.value
                );
            });
        });

        adminContainer.querySelectorAll(".selection-image").forEach((input, index) => {
            const stepIndex = Array.from(adminContainer.children).indexOf(
                input.closest(".admin-step")
            );
            input.addEventListener("input", () => {
                stepsData[stepIndex].selections[index].image = input.value;
            });
        });
    }

    renderSteps();
});
