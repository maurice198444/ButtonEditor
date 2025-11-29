<?php

use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::inertia('/', 'Home', ['user' => 'Maurice']);

Route::inertia('/about', 'About');


Route::get('/cards/{card}', [CardController::class, 'show']);
Route::post('/cards/{card}/versions', [CardController::class, 'storeVersion']);
Route::get('/cards/{card}/edit', [CardController::class, 'edit'])->name('cards.edit');
