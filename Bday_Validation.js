// Function to validate age field based on birthday
function validateAge() {
    const birthdayInput = document.getElementById('birthday');
    const ageInput = document.getElementById('age');

    if (birthdayInput && ageInput) {
        birthdayInput.addEventListener('change', function() {
            const birthdate = new Date(birthdayInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthdate.getFullYear();
            const month = today.getMonth() - birthdate.getMonth();

            // Adjust age if birthday hasn't occurred yet this year
            if (month < 0 || (month === 0 && today.getDate() < birthdate.getDate())) {
                age--;
            }

            ageInput.value = age;
        });
    }
}

// Initialize validation after DOM content is loaded
document.addEventListener('DOMContentLoaded', validateAge);
