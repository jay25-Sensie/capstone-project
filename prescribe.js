function generateDoses(selectElement) {
    const dosesContainer = selectElement.closest('tr').querySelector('.doses-container');
    const dosesCount = selectElement.value;
    dosesContainer.innerHTML = ''; // Clear existing inputs

    for (let i = 0; i < dosesCount; i++) {
        const doseInput = document.createElement('input');
        doseInput.type = 'time';
        doseInput.name = 'dose_timings[' + selectElement.closest('tr').rowIndex + '][' + i + ']'; // Ensure correct naming
        doseInput.className = 'form-control';
        dosesContainer.appendChild(doseInput);
    }
}

function addRow() {
    const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();
    
    const medicineCell = newRow.insertCell(0);
    const dosesCell = newRow.insertCell(1);
    const timingsCell = newRow.insertCell(2);
    
    medicineCell.innerHTML = `<input type="text" name="medicine_name[]" placeholder="Medicine name" class="form-control" required>`;
    dosesCell.innerHTML = `<select name="doses_per_day[]" class="form-control" onchange="generateDoses(this)">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>`;
    timingsCell.className = 'doses-container'; // Set class for timing container
}

function undoLastRow() {
    const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
    if (table.rows.length > 1) { // Prevent removing the last row if it's the only one
        table.deleteRow(table.rows.length - 1);
    }
}