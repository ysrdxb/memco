@extends('inventory.layout')
@section('title', 'AI Assistant')
@section('content')

<style>
    .chat-container {
        height: 65vh;
        overflow-y: auto;
        padding: 20px;
        background: var(--color-surface);
        border-radius: 12px;
        border: 1px solid var(--color-border);
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .chat-message {
        max-width: 80%;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
    }
    
    .chat-message.user {
        align-self: flex-end;
        background: var(--color-primary);
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }
    
    .chat-message.assistant {
        align-self: flex-start;
        background: var(--color-card);
        color: var(--color-text-primary);
        border: 1px solid var(--color-border);
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .chat-message table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 14px;
        background: #fff;
    }
    
    .chat-message th, .chat-message td {
        border: 1px solid var(--color-border);
        padding: 8px 12px;
        text-align: left;
    }
    
    .chat-message th {
        background-color: rgba(0,0,0,0.03);
        font-weight: 600;
        color: var(--color-text-dark);
    }
    
    .chat-message tr:nth-child(even) {
        background-color: rgba(0,0,0,0.01);
    }

    .chat-message.system {
        align-self: center;
        background: transparent;
        color: var(--color-text-muted);
        font-size: 0.8rem;
        font-style: italic;
    }

    .chat-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .chat-input {
        width: 100%;
        border-radius: 12px;
        padding: 14px 20px;
        padding-right: 100px;
        border: 1px solid var(--color-border);
        outline: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s;
    }
    
    .chat-input:focus {
        border-color: #e63946;
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.15);
    }

    .chat-mic-btn {
        position: absolute;
        right: 54px;
        background: transparent;
        border: none;
        color: #6c757d;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .chat-mic-btn:hover {
        color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
    }
    
    .chat-mic-btn.recording {
        color: #fff;
        background: #dc3545;
        animation: pulse-red 1.5s infinite;
    }
    
    .chat-mic-btn.recording i {
        color: #fff; /* Ensure icon is explicitly white when recording */
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    .chat-send-btn {
        position: absolute;
        right: 12px;
        background: var(--color-primary, #001f3f);
        color: #fff;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .chat-action-btn {
        display: inline-block;
        background: rgba(0, 31, 63, 0.05);
        color: var(--color-primary, #001f3f);
        border: 1px solid rgba(0, 31, 63, 0.2);
        border-radius: 16px;
        padding: 6px 14px;
        margin: 6px 6px 6px 0;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .chat-action-btn:hover {
        background: var(--color-primary, #001f3f);
        color: #fff;
        border-color: var(--color-primary, #001f3f);
    }

    .chat-send-btn:hover {
        transform: scale(1.05);
    }

    .chat-send-btn:disabled {
        background: var(--color-border);
        cursor: not-allowed;
    }

    .typing-indicator {
        display: none;
        align-items: center;
        gap: 4px;
        padding: 12px 18px;
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        align-self: flex-start;
        width: max-content;
    }

    .chat-voice-btn {
        position: absolute;
        right: 96px;
        background: transparent;
        border: none;
        color: #6c757d;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .chat-voice-btn:hover {
        color: var(--color-primary, #001f3f);
        background: rgba(0, 31, 63, 0.1);
    }
    
    .chat-voice-btn.active {
        color: var(--color-primary, #001f3f);
    }
    
    .typing-indicator {
        display: none;
        align-items: center;
        margin-top: 10px;
    }
    
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #ced4da;
        border-radius: 50%;
        margin-right: 5px;
        animation: typing 1s infinite;
    }
    
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.5); opacity: 1; }
    }
</style>

<div class="container-fluid">
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="ik ik-cpu"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">AI Assistant</h3>
                <p class="header-sub-text-clean">Ask me anything about products, stocks, projects, or material requests.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-card p-4">
                <div class="chat-container" id="chat-container" style="height: 72vh;">
                    <div class="chat-message system">
                        Connected to Groq LLaMA-3. Start chatting below!
                    </div>
                </div>
                
                <div class="typing-indicator mb-3" id="typing-indicator">
                    <span></span><span></span><span></span>
                </div>

                <div class="chat-input-wrapper">
                    <input type="text" class="chat-input" id="chat-input" placeholder="e.g. What is the stock level for product XYZ?">
                    
                    <button class="chat-voice-btn" id="chat-voice-btn" title="Toggle Voice Mode">
                        <i class="fas fa-volume-mute"></i>
                    </button>
                    
                    <button class="chat-mic-btn" id="chat-mic-btn" title="Click to Speak">
                        <i class="fas fa-microphone"></i>
                    </button>
                    
                    <button class="chat-send-btn" id="chat-send-btn">
                        <i class="ik ik-send"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send-btn');
        const micBtn = document.getElementById('chat-mic-btn');
        const voiceBtn = document.getElementById('chat-voice-btn');
        const chatContainer = document.getElementById('chat-container');
        const typingIndicator = document.getElementById('typing-indicator');

        let messageHistory = [];
        let isRecording = false;
        let recognition = null;
        let isVoiceMode = false;
        let speechTimeout = null;
        let shouldAutoSubmit = false;
        let isContinuousConversation = false;
        
        function setVoiceMode(enabled) {
            isVoiceMode = enabled;
            if (isVoiceMode) {
                voiceBtn.classList.add('active');
                voiceBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            } else {
                voiceBtn.classList.remove('active');
                voiceBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                window.speechSynthesis.cancel();
            }
        }
        
        voiceBtn.addEventListener('click', function() {
            setVoiceMode(!isVoiceMode);
        });
        
        // Auto-disable voice if they start typing manually
        chatInput.addEventListener('input', function() {
            if (isVoiceMode) setVoiceMode(false);
        });
        
        let availableVoices = window.speechSynthesis ? window.speechSynthesis.getVoices() : [];
        if (window.speechSynthesis) {
            window.speechSynthesis.onvoiceschanged = function() {
                availableVoices = window.speechSynthesis.getVoices();
            };
        }

        function speakText(text) {
            if (!isVoiceMode || !window.speechSynthesis) {
                if (isContinuousConversation && !isRecording && recognition) {
                    setTimeout(() => recognition.start(), 500);
                }
                return;
            }
            
            // Clean text for speech: remove markdown tags, buttons, borders
            let cleanText = text.replace(/\[SELECT:\s*(.*?)\]/gi, '$1'); 
            cleanText = cleanText.replace(/\|/g, ' '); 
            cleanText = cleanText.replace(/[*#_`~-]/g, ''); 
            cleanText = cleanText.replace(/\n+/g, '. '); 
            
            const utterance = new SpeechSynthesisUtterance(cleanText);
            
            // Score voices to prioritize female and high-quality Google voices
            function scoreVoice(voice) {
                let score = 0;
                const name = voice.name.toLowerCase();
                const femaleKeywords = ['female', 'woman', 'girl', 'zira', 'hazel', 'susan', 'kalpana', 'neerja', 'aditi', 'swara', 'hoda', 'sana', 'google uk english female', 'google us english', 'google हिन्दी'];
                if (femaleKeywords.some(kw => name.includes(kw))) score += 10;
                if (name.includes('google')) score += 5;
                return score;
            }

            // Auto-detect Urdu/Arabic characters for accurate voice engine
            if (/[\u0600-\u06FF]/.test(cleanText)) {
                let urVoices = availableVoices.filter(v => v.lang.startsWith('ur') || v.lang.startsWith('hi') || v.lang.startsWith('ar'));
                urVoices.sort((a, b) => scoreVoice(b) - scoreVoice(a));
                
                if (urVoices.length > 0) {
                    utterance.voice = urVoices[0];
                } else {
                    utterance.lang = 'hi-IN'; // robust fallback for Chrome
                }
            } else {
                let enVoices = availableVoices.filter(v => v.lang.startsWith('en'));
                enVoices.sort((a, b) => scoreVoice(b) - scoreVoice(a));
                
                if (enVoices.length > 0) {
                    utterance.voice = enVoices[0];
                }
            }
            
            let hasEnded = false;
            
            utterance.onend = function() {
                hasEnded = true;
                if (isContinuousConversation && !isRecording && recognition) {
                    setTimeout(() => recognition.start(), 500);
                }
            };
            
            utterance.onerror = function(e) {
                hasEnded = true;
                if (isContinuousConversation && !isRecording && recognition) {
                    setTimeout(() => recognition.start(), 500);
                }
            };

            window.speechSynthesis.speak(utterance);
            
            // Failsafe: If onend never fires (e.g. browser silently drops unsupported lang)
            let estimatedDuration = (cleanText.length * 80) + 3000;
            setTimeout(() => {
                if (!hasEnded && isContinuousConversation && !isRecording && recognition) {
                    hasEnded = true;
                    window.speechSynthesis.cancel();
                    recognition.start();
                }
            }, estimatedDuration);
        }

        // Speech Recognition Setup
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (SpeechRecognition) {
            recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = 'en-AE'; // Using UAE English to better understand local names like Jabel Ali

            recognition.onstart = function() {
                isRecording = true;
                micBtn.classList.add('recording');
                chatInput.placeholder = "Listening... (Auto-submits after 5s silence)";
                if (speechTimeout) clearTimeout(speechTimeout);
                
                // Auto-enable voice mode when they use the mic
                if (!isVoiceMode) setVoiceMode(true);
            };

            recognition.onresult = function(event) {
                if (speechTimeout) clearTimeout(speechTimeout);

                let interimTranscript = '';
                let finalTranscript = '';
                
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript;
                    } else {
                        interimTranscript += event.results[i][0].transcript;
                    }
                }
                
                if (finalTranscript !== '') {
                    chatInput.value += finalTranscript + ' ';
                }
                
                // Set silence timeout to auto-submit after 8 seconds
                speechTimeout = setTimeout(() => {
                    if (isRecording) {
                        shouldAutoSubmit = true;
                        recognition.stop(); // This triggers onend which calls stopRecording
                    }
                }, 8000);
            };

            recognition.onerror = function(event) {
                console.error("Speech Recognition Error:", event.error);
                shouldAutoSubmit = false;
                stopRecording();
            };

            recognition.onend = function() {
                stopRecording(shouldAutoSubmit);
                shouldAutoSubmit = false;
            };
        } else {
            micBtn.style.display = 'none'; // Hide if not supported
        }

        async function stopRecording(autoSubmit = false) {
            if (!isRecording) return;
            isRecording = false;
            micBtn.classList.remove('recording');
            
            if (speechTimeout) clearTimeout(speechTimeout);
            
            const textToRefine = chatInput.value.trim();
            if (textToRefine) {
                chatInput.placeholder = "Refining speech with AI...";
                chatInput.disabled = true;
                micBtn.disabled = true;
                sendBtn.disabled = true;
                
                try {
                    const response = await fetch('{{ route("ai.refine-speech") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: textToRefine })
                    });
                    
                    const data = await response.json();
                    if (data.reply) {
                        chatInput.value = data.reply;
                    }
                } catch(e) {
                    console.error("Refinement error", e);
                }
                
                chatInput.disabled = false;
                micBtn.disabled = false;
                sendBtn.disabled = false;
                chatInput.focus();
                
                if (autoSubmit) {
                    sendMessage();
                }
            }
            
            chatInput.placeholder = "e.g. What is the stock level for product XYZ?";
        }

        micBtn.addEventListener('click', function() {
            if (!recognition) return;
            
            if (isRecording) {
                isContinuousConversation = false; // Manually exiting
                recognition.stop();
            } else {
                chatInput.value = ''; // clear input before talking
                isContinuousConversation = true; // Manually starting
                recognition.start();
            }
        });

        window.triggerChatAction = function(btn) {
            chatInput.value = btn.textContent;
            sendMessage();
        };

        function appendMessage(role, content) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `chat-message ${role}`;
            
            if(role === 'assistant') {
                let parsedContent = content.replace(/\[SELECT:\s*(.*?)\]/gi, function(match, text) {
                    return `<button class="chat-action-btn" onclick="triggerChatAction(this)">${text.trim()}</button>`;
                });
                msgDiv.innerHTML = marked.parse(parsedContent);
                speakText(content);
            } else {
                msgDiv.textContent = content;
            }
            
            chatContainer.appendChild(msgDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

    async function sendMessage() {
        let text = chatInput.value.trim();
        if(!text) return;

        // Auto-correct common speech-to-text transcription errors
        const corrections = {
            "the mark will ask": "damac villas",
            "the mark vilas": "damac villas",
            "the mark dubai": "damac dubai",
            "the mark": "damac",
            "demak": "damac",
            "vilas": "villas",
            "jable": "jabel",
            "jebel": "jabel",
            "regionalu": "jabel ali",
            "ancient": "any ten",
            "lpu": "lpo",
            "mrm": "mr",
            "material list": "material request",
            "purchase order": "lpo"
        };
        
        for (const [wrong, correct] of Object.entries(corrections)) {
            const regex = new RegExp("\\b" + wrong + "\\b", "gi");
            text = text.replace(regex, correct);
        }

        appendMessage('user', text);
        chatInput.value = '';
        chatInput.disabled = true;
        sendBtn.disabled = true;
        typingIndicator.style.display = 'flex';
        chatContainer.scrollTop = chatContainer.scrollHeight;

        try {
            const response = await fetch('{{ route("ai.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text, history: messageHistory })
            });

            if (!response.ok) {
                let errorText = await response.text();
                console.error("Server Error:", errorText);
                appendMessage('system', 'Server returned ' + response.status + '. Check console for details.');
                return;
            }

            const data = await response.json();
            
            if(data.error) {
                appendMessage('system', 'Error: ' + data.error);
            } else {
                appendMessage('assistant', data.reply);
                messageHistory.push({ role: 'user', content: text });
                messageHistory.push({ role: 'assistant', content: data.reply });
            }

        } catch (error) {
            console.error("Network Error:", error);
            appendMessage('system', 'Network error occurred while fetching response. Error: ' + error.message);
        } finally {
            typingIndicator.style.display = 'none';
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            sendMessage();
        }
    });
</script>
@endpush
@endsection
