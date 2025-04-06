<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Cloudstudio\Ollama\Facades\Ollama;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post(
    '/ollama',
    function (Request $request) {

        // get message from request
        $message = $request->input('message');

        $result = Ollama::agent('You are a weather expert...')
            ->prompt($message)
            ->model('llama3.2')
            ->options(['temperature' => 0.8])
            ->stream(false)
            ->ask();

        // Return only the 'response' field
        return response()->json([
            'reply' => $result['response'],
        ]);
    }
);
