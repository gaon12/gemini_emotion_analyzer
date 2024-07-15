document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('emotionForm');
    const resultCard = document.getElementById('resultCard');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const emotionTitle = document.getElementById('emotionTitle');
    const emotionMessage = document.getElementById('emotionMessage');
    const emotionQuote = document.getElementById('emotionQuote');
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const submitButton = document.getElementById('submit-btn');
    let isRequestInProgress = false; // 플래그 변수

    // Load dark mode setting based on system preferences if not set
    if (localStorage.getItem('darkMode') === null) {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            localStorage.setItem('darkMode', 'enabled');
        } else {
            localStorage.setItem('darkMode', 'disabled');
        }
    }

    // Apply dark mode setting
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeSwitch.checked = true;
    }

    darkModeSwitch.addEventListener('change', function() {
        if (darkModeSwitch.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (isRequestInProgress) {
            return; // 요청이 이미 진행 중이면 반환
        }

        isRequestInProgress = true; // 요청 시작
        submitButton.disabled = true; // 제출 버튼 비활성화

        const emotionText = document.getElementById('emotionInput').value;
        const sanitizedText = emotionText.replace(/[<>]/g, function(tag) {
            const tagsToReplace = {
                '<': '&lt;',
                '>': '&gt;'
            };
            return tagsToReplace[tag] || tag;
        });

        resultCard.classList.add('d-none');
        loadingSpinner.classList.remove('d-none');

        fetch('/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ text: sanitizedText })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            emotionTitle.innerText = data.emotion;
            emotionMessage.innerText = data.message;
            emotionQuote.innerText = data.quote;
            updateColors(data.color);
            resultCard.classList.remove('d-none');

            // 부드러운 스크롤
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
            isRequestInProgress = false; // 요청 종료
            submitButton.disabled = false; // 제출 버튼 활성화
        });
    });

    function updateColors(backgroundColor) {
        document.body.style.backgroundColor = backgroundColor;

        // 글자 색상을 배경색에 따라 변경
        const brightness = calculateBrightness(backgroundColor);
        if (brightness < 128) {
            document.body.style.color = '#f8f9fa'; // 밝은 글자 색상
        } else {
            document.body.style.color = '#343a40'; // 어두운 글자 색상
        }
    }

    function calculateBrightness(hexColor) {
        // HEX 색상을 RGB로 변환
        const r = parseInt(hexColor.substr(1, 2), 16);
        const g = parseInt(hexColor.substr(3, 2), 16);
        const b = parseInt(hexColor.substr(5, 2), 16);
        // 밝기 계산
        return (r * 299 + g * 587 + b * 114) / 1000;
    }
});

function copyToClipboard(elementId) {
    const element = document.querySelector(elementId);
    navigator.clipboard.writeText(element.innerText).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Copied',
            text: 'Copied!'
        });
    }, function(err) {
        console.error('Async: Could not copy text: ', err);
    });
}
