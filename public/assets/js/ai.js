document.addEventListener('DOMContentLoaded', () => {
    const aiFloatingBtn = document.getElementById('ai-floatingBtn');
    const aiPopupOverlay = document.getElementById('ai-popupOverlay');
    const aiCloseBtn = document.getElementById('ai-closeBtn');
    const aiStartButton = document.getElementById('ai-startButton');
    const aiNewQuestionButton = document.getElementById('ai-newQuestionButton');
    const aiDismissButton = document.getElementById('ai-dismissButton');
    const aiResponseElement = document.getElementById('ai-response');

    const aiRecognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    const aiSynthesis = window.speechSynthesis;

    aiRecognition.lang = 'en-US';
    aiRecognition.continuous = true;
    aiRecognition.interimResults = false;

    let aiFemaleVoice = null;
    let aiConversationActive = false;

    // Wait for voices to be loaded
    aiSynthesis.onvoiceschanged = () => {
        const voices = aiSynthesis.getVoices();

        // Find a female voice
        aiFemaleVoice = voices.find(voice => voice.name.toLowerCase().includes('female'));

        if (aiFemaleVoice) {
            // Enable the button when voices are loaded
            aiStartButton.disabled = false;
        }
    };

    aiFloatingBtn.addEventListener('click', () => {
        aiPopupOverlay.style.display = 'flex';
    });

    aiCloseBtn.addEventListener('click', () => {
        aiPopupOverlay.style.display = 'none';
    });

    aiStartButton.addEventListener('click', () => {
        aiResponseElement.textContent = 'Listening...';
        aiResponseElement.style.color = 'white';
        aiResponseElement.style.textAlign = 'center';
        aiResponseElement.style.display = 'block';
        
        // Hide the ai-infoBox
        const aiInfoBox = document.getElementById('ai-infoBox');
        aiInfoBox.style.display = 'none';

        // Start the conversation
        startAiConversation();
    });

    aiNewQuestionButton.addEventListener('click', () => {
        aiResponseElement.textContent = 'Listening...';
        aiResponseElement.style.color = 'white';
        aiResponseElement.style.textAlign = 'center';
        aiResponseElement.style.display = 'block';

        // Hide the ai-infoBox
        const aiInfoBox = document.getElementById('ai-infoBox');
        aiInfoBox.style.display = 'none';

        // Start the conversation
        startAiConversation();
        aiNewQuestionButton.style.display = 'none';
    });

    aiDismissButton.addEventListener('click', () => {
        endAiConversation();
    });

    const startAiConversation = () => {
        aiRecognition.start();
        aiStartButton.textContent = 'Listening...';
        aiNewQuestionButton.style.display = 'none';
        aiDismissButton.style.display = 'inline-block';
        aiStartButton.style.display = 'none';
        aiConversationActive = true;
    };

    const endAiConversation = () => {
        aiRecognition.stop();
        aiResponseElement.textContent = 'Shree:🔊';
        aiResponseElement.style.color = 'white'; // Set the text color to white
        aiResponseElement.style.textAlign = 'center'; // Center the text
        aiResponseElement.style.display = 'block'; // Ensure it's displayed as a block
        aiDismissButton.style.display = 'none';
        aiNewQuestionButton.style.display = 'inline-block';
        aiConversationActive = false;
    };

    aiRecognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase();

        if (aiConversationActive) {
            sendToAiServer(transcript);
        }
    };

    aiRecognition.onend = () => {
        aiStartButton.textContent = 'Start Listening';

        // Restart the conversation loop if still active
        if (aiConversationActive) {
            aiNewQuestionButton.style.display = 'none';
            startAiConversation();
        }
    };

    const sendToAiServer = (transcript) => {
        fetch('process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `transcript=${encodeURIComponent(transcript)}`,
        })
        .then(response => response.json())
        .then(data => {
            aiResponseElement.textContent = data.response;
            aiSpeak(data.response);
            endAiConversation(); // End conversation after each response
        })
        .catch(error => {
            console.error('Error sending transcript to server:', error);
        });
    };

    const aiSpeak = (text) => {
        const utterance = new SpeechSynthesisUtterance(text);
        if (aiFemaleVoice) {
            utterance.voice = aiFemaleVoice;
        }
        aiSynthesis.speak(utterance);
    };
});
