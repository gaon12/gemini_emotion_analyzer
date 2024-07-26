document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('emotionForm');
    const resultCard = document.getElementById('resultCard');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const emotionTitle = document.getElementById('emotionTitle');
    const emotionMessage = document.getElementById('emotionMessage');
    const emotionQuote = document.getElementById('emotionQuote');
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const submitButton = document.getElementById('submit-btn');
    const clearInputButton = document.getElementById('clearInput');
    const emotionInput = document.getElementById('emotionInput');
    let isRequestInProgress = false;

    // Initialize dark mode setting
    const initDarkMode = () => {
        if (localStorage.getItem('darkMode') === null) {
            localStorage.setItem('darkMode', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'enabled' : 'disabled');
        }

        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            darkModeSwitch.checked = true;
        }
    };

    initDarkMode();

    darkModeSwitch.addEventListener('change', function() {
        if (darkModeSwitch.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    clearInputButton.addEventListener('click', () => {
        emotionInput.value = '';
        clearInputButton.style.display = 'none';
    });

    emotionInput.addEventListener('input', () => {
        clearInputButton.style.display = emotionInput.value ? 'block' : 'none';
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (isRequestInProgress) return;

        isRequestInProgress = true;
        submitButton.disabled = true;

        const emotionText = emotionInput.value.trim();
        if (!emotionText) {
            isRequestInProgress = false;
            submitButton.disabled = false;
            return;
        }

        const sanitizedText = emotionText.replace(/[<>]/g, tag => ({ '<': '&lt;', '>': '&gt;' }[tag] || tag));

        resultCard.classList.add('d-none');
        loadingSpinner.classList.remove('d-none');

        fetch('/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: sanitizedText })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            emotionTitle.innerText = data.emotion;
            emotionMessage.innerText = data.message;
            emotionQuote.innerText = data.quote;
            updateColors(data.color);
            resultCard.classList.remove('d-none');
            resultCard.scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        })
        .finally(() => {
            loadingSpinner.classList.add('d-none');
            isRequestInProgress = false;
            submitButton.disabled = false;
        });
    });

    const updateColors = (backgroundColor) => {
        document.body.style.backgroundColor = backgroundColor;
        document.body.style.color = calculateBrightness(backgroundColor) < 128 ? '#f8f9fa' : '#343a40';
    };

    const calculateBrightness = (hexColor) => {
        const [r, g, b] = [1, 3, 5].map(offset => parseInt(hexColor.substr(offset, 2), 16));
        return (r * 299 + g * 587 + b * 114) / 1000;
    };
});

function copyToClipboard(elementId) {
    const element = document.querySelector(elementId);
    navigator.clipboard.writeText(element.innerText).then(() => {
        Swal.fire({ icon: 'success', title: 'Copied', text: 'Copied!' });
    }).catch(err => {
        console.error('Async: Could not copy text: ', err);
    });
}
