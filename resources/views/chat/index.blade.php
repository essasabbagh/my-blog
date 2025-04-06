<!DOCTYPE html>
<html>
<head>
    <title>Ollama Chat</title>
    <style>
        .chat-container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .user-message {
            background-color: #e0f7fa;
            text-align: right;
        }
        .assistant-message {
            background-color: #f0f0f0;
            text-align: left;
        }
        .tool-message {
            background-color: #fff3cd;
            text-align: left;
            font-style: italic;
        }
        .input-area {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <h1>Ollama Chat</h1>
        <div id="chat-messages">
            @foreach($messages as $message)
                <div class="message {{ $message['role'] }}-message">
                    @if($message['role'] === 'tool')
                        <strong>Tool Call Result:</strong> {{ $message['content'] }}
                    @else
                        {{ $message['content'] }}
                    @endif
                </div>
            @endforeach
        </div>

        <div class="input-area">
            <form id="chat-form" action="{{ route('chat.send') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea name="prompt" placeholder="Type your message here..." rows="3" cols="50"></textarea><br><br>

                {{-- Vision Support --}}
                <input type="file" name="image"><br><br>

                {{-- Chat Completion with tools (checkbox to enable) --}}
                <input type="checkbox" id="enable_tools" name="tools"> <label for="enable_tools">Enable Tools</label><br><br>

                {{-- Streamable responses (checkbox to enable) --}}
                <input type="checkbox" id="enable_stream" name="stream"> <label for="enable_stream">Enable Streaming</label><br><br>

                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');

        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const enableStream = document.getElementById('enable_stream').checked;

            if (enableStream) {
                // Clear the input field
                const promptTextarea = this.querySelector('textarea[name="prompt"]');
                const userMessage = promptTextarea.value;
                promptTextarea.value = '';

                // Add user message immediately
                addUserMessage(userMessage);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token')
                    }
                }).then(response => {
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let buffer = '';

                    function processStream() {
                        reader.read().then(({ done, value }) => {
                            if (done) {
                                return;
                            }
                            buffer += decoder.decode(value);
                            const lines = buffer.split('\n\n');
                            buffer = lines.pop(); // Keep the incomplete line

                            lines.forEach(line => {
                                if (line.startsWith('data:')) {
                                    const content = line.substring(5).trim();
                                    if (content) {
                                        addAssistantMessage(content);
                                    }
                                }
                            });
                            processStream();
                        });
                    }
                    processStream();
                });
            } else {
                this.submit(); // Default form submission
            }
        });

        function addUserMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', 'user-message');
            messageDiv.textContent = message;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
        }

        function addAssistantMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', 'assistant-message');
            messageDiv.textContent = message;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
        }
    </script>
</body>
</html>
