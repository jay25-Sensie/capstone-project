function generateDoses(selectElement) {
    const dosesContainer = selectElement.closest('tr').querySelector('.doses-container');
    const dosesCount = selectElement.value;
    dosesContainer.innerHTML = ''; // Clear existing inputs

    for (let i = 0; i < dosesCount; i++) {
        const doseInput = document.createElement('input');
        doseInput.type = 'time';
        doseInput.name = 'dose_timings[' + selectElement.closest('tr').rowIndex + '][' + i + ']';
        doseInput.className = 'form-control';
        dosesContainer.appendChild(doseInput);

        // Add Meal Time select for each dose
        const mealTimeSelect = document.createElement('select');
        mealTimeSelect.name = 'meal_time[' + selectElement.closest('tr').rowIndex + '][' + i + ']';
        mealTimeSelect.className = 'form-control';
        mealTimeSelect.innerHTML = `
            <option value="0">Before Meal</option>
            <option value="1">After Meal</option>
        `;
        dosesContainer.appendChild(mealTimeSelect);
    }
}

function addRow() {
    const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();
    
    const medicineCell = newRow.insertCell(0);
    const dosesCell = newRow.insertCell(1);
    const timingsCell = newRow.insertCell(2);
    const mealTimeCell = newRow.insertCell(3); // Add a new cell for Meal Time

    medicineCell.innerHTML = `<input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control" required>`;
    dosesCell.innerHTML = `<select name="doses_per_day[]" class="form-control" onchange="generateDoses(this)">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>`;
    timingsCell.className = 'doses-container'; // Set class for timing container
    mealTimeCell.innerHTML = `<select name="meal_time[]" class="form-control">
        <option value="0">Before Meal</option>
        <option value="1">After Meal</option>
    </select>`; // Add the Meal Time select in the new row
}

function undoLastRow() {
    const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
    if (table.rows.length > 1) { // Prevent removing the last row if it's the only one
        table.deleteRow(table.rows.length - 1);
    }
}