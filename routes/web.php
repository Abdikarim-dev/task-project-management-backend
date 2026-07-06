<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Task & Project Management API',
        'status' => 'ok',
        'docs' => 'See README.md for API endpoints.',
    ]);
});
