document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');

    function createErrorElement(inputElement, message) {
        const existingError = inputElement.nextElementSibling;
        if (existingError && existingError.classList.contains('error-message')) {
            existingError.textContent = message;
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger small mt-1';
            errorDiv.textContent = message;
            inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
        }
    }
    function clearError(inputElement) {
        const errorElement = inputElement.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.remove();
        }
    }
    function validateName(name) {
        return name.trim().length >= 3 && /^[a-zA-Z\s]+$/.test(name);
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePassword(password) {
        return password.length >= 8 && 
               /[A-Z]/.test(password) && 
               /[a-z]/.test(password) && 
               /[0-9]/.test(password);
    }
    nameInput.addEventListener('input', function() {
        clearError(this);
        if (!validateName(this.value)) {
            createErrorElement(this, 'Name must be at least 3 characters long and contain only letters');
        }
    });

    emailInput.addEventListener('input', function() {
        clearError(this);
        if (!validateEmail(this.value)) {
            createErrorElement(this, 'Please enter a valid email address');
        }
    });
    passwordInput.addEventListener('input', function() {
        clearError(this);
        if (!validatePassword(this.value)) {
            createErrorElement(this, 'Password requirements:\n• At least 8 characters\n• At least one uppercase letter\n• At least one lowercase letter\n• At least one number');
        }
    });

    confirmPasswordInput.addEventListener('input', function() {
        clearError(this);
        if (this.value !== passwordInput.value) {
            createErrorElement(this, 'Passwords do not match');
        }
    });

});
