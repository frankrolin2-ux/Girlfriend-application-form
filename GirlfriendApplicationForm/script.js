const characterFields = [
    { input: 'ideal_date', counter: 'ideal-date-count', limit: 240 },
    { input: 'green_flag', counter: 'green-flag-count', limit: 100 },
];

function updateCharacterCount(inputId, counterId, limit) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);

    if (!input || !counter) {
        return;
    }

    counter.textContent = `${input.value.length} / ${limit}`;
}

function connectCharacterCounters() {
    characterFields.forEach(({ input, counter, limit }) => {
        const field = document.getElementById(input);

        if (!field) {
            return;
        }

        updateCharacterCount(input, counter, limit);
        field.addEventListener('input', () => updateCharacterCount(input, counter, limit));
    });
}

function connectFormValidation() {
    const form = document.querySelector('form');

    if (!form) {
        return;
    }

    form.addEventListener('submit', (event) => {
        const age = document.getElementById('age');

        if (age && age.value && Number(age.value) < 18) {
            age.setCustomValidity('You must be 18 or older to apply.');
        } else if (age) {
            age.setCustomValidity('');
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
        }
    });
}

connectCharacterCounters();
connectFormValidation();