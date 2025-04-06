<?php

namespace App\Http\Controllers;

use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Session::get('chat_messages', []);
        return view('chat.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'prompt' => 'required_without:image',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjust mime types and size as needed
        ]);

        $prompt = $request->input('prompt');
        $image = $request->file('image');
        $messages = Session::get('chat_messages', []);
        $model = 'llama3.2'; // Default model

        if ($image) {
            // $model = 'llava:7b';
            $model = 'llava:13b';
            $messages[] = ['role' => 'user', 'content' => $prompt];
            Session::put('chat_messages', $messages);

            $response = Ollama::model($model)
                ->prompt($prompt)
                ->image($image->path())
                ->ask();

            $messages[] = ['role' => 'assistant', 'content' => $response['response'] ?? ''];
            Session::put('chat_messages', $messages);
            return redirect()->route('chat.index');
        } elseif ($request->has('tools')) {
            $model = 'llama3.1';
            $messages[] = ['role' => 'user', 'content' => $prompt];
            Session::put('chat_messages', $messages);

            $response = Ollama::model($model)
                ->tools([
                    [
                        "type"     => "function",
                        "function" => [
                            "name"        => "get_current_weather",
                            "description" => "Get the current weather for a location",
                            "parameters"  => [
                                "type"       => "object",
                                "properties" => [
                                    "location" => [
                                        "type"        => "string",
                                        "description" => "The location to get the weather for, e.g. San Francisco, CA",
                                    ],
                                    "format"   => [
                                        "type"        => "string",
                                        "description" => "The format to return the weather in, e.g. 'celsius' or 'fahrenheit'",
                                        "enum"        => ["celsius", "fahrenheit"],
                                    ],
                                ],
                                "required"   => ["location", "format"],
                            ],
                        ],
                    ],
                ])
                ->chat($messages);

            // Handle tool calls (simplified example)
            if (isset($response['tool_calls'])) {
                foreach ($response['tool_calls'] as $toolCall) {
                    if ($toolCall['name'] === 'get_current_weather') {
                        $arguments = json_decode($toolCall['arguments'], true);
                        $weather = $this->getCurrentWeather($arguments['location'], $arguments['format']);
                        $messages[] = ['role' => 'tool', 'content' => json_encode(['result' => $weather]), 'tool_call_id' => $toolCall['id']];
                        $secondResponse = Ollama::model($model)->chat($messages);
                        $messages[] = ['role' => 'assistant', 'content' => $secondResponse['content'] ?? ''];
                    }
                }
            } else {
                $messages[] = ['role' => 'assistant', 'content' => $response['content'] ?? ''];
            }

            Session::put('chat_messages', $messages);
            return redirect()->route('chat.index');

        } elseif ($request->has('stream')) {
            $messages[] = ['role' => 'user', 'content' => $prompt];
            Session::put('chat_messages', $messages);

            return new StreamedResponse(function () use ($prompt, $model) {
                $stream = Ollama::agent('You are a helpful assistant.')
                    ->prompt($prompt)
                    ->model($model)
                    ->stream(true)
                    ->ask();

                foreach ($stream->getBody() as $chunk) {
                    $data = json_decode($chunk, true);
                    if (isset($data['response'])) {
                        echo "data: " . $data['response'] . "\n\n"; // SSE format
                        ob_flush();
                        flush();
                    }
                }
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
            ]);
        } else {
            $messages[] = ['role' => 'user', 'content' => $prompt];
            Session::put('chat_messages', $messages);

            $response = Ollama::agent('You are a helpful assistant.')
                ->prompt($prompt)
                ->model($model)
                ->ask();

            $messages[] = ['role' => 'assistant', 'content' => $response['response'] ?? ''];
            Session::put('chat_messages', $messages);
            return redirect()->route('chat.index');
        }
    }

    // Example function for handling tool calls
    private function getCurrentWeather($location, $format)
    {
        // In a real application, you would call an external weather API here.
        // For this example, we'll just return a dummy response.
        return "The weather in {$location} is currently sunny and 25 degrees ";
        // {$format === 'celsius' ? 'Celsius' : 'Fahrenheit'}.
    }
}
