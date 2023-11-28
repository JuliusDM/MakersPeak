if (typeof isTranslating === 'undefined') {
    var isTranslating = false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('languageToggle').addEventListener('click', function() {
        if (isTranslating) return;
        isTranslating = true;

        var elementsToTranslate = document.querySelectorAll('.translatable');
        var targetLanguage = this.innerText.includes('Swedish') ? 'sv' : 'en';

        Promise.all(Array.from(elementsToTranslate).map(element => {
            return new Promise((resolve) => {
                var textToTranslate = element.innerText;
                translateText(textToTranslate, targetLanguage, function(translatedText) {
                    element.innerText = translatedText;
                    resolve();
                });
            });
        })).then(() => {
            this.innerText = targetLanguage === 'sv' ? 'Switch to English' : 'Switch to Swedish';
            isTranslating = false;
        });
    });
});

function translateText(text, targetLanguage, callback) {
    var url = '/translate.php'; // URL to your PHP script
    var data = {
        'text': text,
        'targetLanguage': targetLanguage
    };

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        var decodedText = decodeHTMLEntities(data.translatedText);
        callback(decodedText);
    })
    .catch(error => {
        console.error('Error:', error);
        isTranslating = false;
    });
}

function decodeHTMLEntities(text) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = text;
    return textArea.value;
}
