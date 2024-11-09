<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Scan RFID and check if user exists
Route::get('/rfid/{rfid}', [StudentController::class, 'scanRFID']);

// Check user's current points
Route::get('/points/{rfid}', [StudentController::class, 'checkPoints']);

// Get leaderboard and user's rank
Route::get('/leaderboard/{rfid}', [StudentController::class, 'leaderboard']);

// Add or subtract points from a user
Route::post('/points/{rfid}', [StudentController::class, 'updatePoints']);

//add student if kineme
Route::post('/students', [StudentController::class, 'saveStudent']);
