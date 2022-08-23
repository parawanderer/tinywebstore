const form = document.getElementById('registrationForm');

const passwordInput = document.getElementById('password');
const passwordRepeatInput = document.getElementById('repeatPassword');
const zipInput = document.getElementById("zipCode");
const submitButton = document.getElementById('registerUserButton');

function updateFormSubmitButtonStatus() {
    if (!form.checkValidity()) {
        submitButton.disabled = true;
    } else {
        submitButton.disabled = false;
    }
}

const REQUIRED_DIGITS = 1;
const REQUIRED_ALPHABETIC = 1;
const REQUIRED_SYMBOLS = 1;

function isValidPassword(password) {
    if (password.length < 10) return false;

    let digitCount = 0;
    let charCount = 0;
    let symbolCount = 0;

    for (let i = 0; i < password.length; ++i) {
        let c = password[i];

        if (c >= '0' && c <= '9') {
            digitCount++;
        } else if ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z')) {
            charCount++;
        } else {
            symbolCount++;
        }
    }

    return (digitCount >= REQUIRED_DIGITS) && (charCount >= REQUIRED_ALPHABETIC) && (symbolCount >= REQUIRED_SYMBOLS);
};

function zipcodeValidator(zip) {
    if (zip.length !== 4) return false;

    for (let i = 0; i < zip.length; ++i) {
        let c = zip[i];
        if (c < '0' || c > '9')
            return false;
    }
    return true;
}

zipInput.addEventListener('input', function (event) { 
    const value = event.target.value?.trim();

    if (!value) {
        const ERROR = 'Please provide a zip code';
        zipInput.setCustomValidity(ERROR);
        zipInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else if (!zipcodeValidator(value)) {
        const ERROR = 'Zip code must be valid';
        zipInput.setCustomValidity(ERROR);
        zipInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else {
        zipInput.setCustomValidity("");
    }

    zipInput.reportValidity();
});

passwordInput.addEventListener("input", function (event) {
    const value = event.target.value?.trim();

    if (!value) {
        const ERROR = 'Password must not be empty';
        passwordInput.setCustomValidity(ERROR);
        passwordInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else if (!isValidPassword(value)) {
        const ERROR = 'Password must be at least 10 character long and contain at least 1 letter, digit and symbol';
        passwordInput.setCustomValidity(ERROR);
        passwordInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else {
        passwordInput.setCustomValidity("");
    }
});

passwordRepeatInput.addEventListener('input', function (event) {
    const value = event.target.value?.trim();

    if (!value) {
        const ERROR = 'Password must not be empty';
        passwordRepeatInput.setCustomValidity(ERROR);
        passwordRepeatInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else if (value !== passwordInput.value?.trim()) {
        const ERROR = 'Passwords must match';
        passwordRepeatInput.setCustomValidity(ERROR);
        passwordRepeatInput.parentElement.getElementsByClassName('invalid-feedback')[0].innerText = ERROR;
    } else {
        passwordRepeatInput.setCustomValidity("");
    }

    passwordRepeatInput.reportValidity();
});

form.addEventListener('submit', function (event) {

    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    form.classList.add('was-validated');

});