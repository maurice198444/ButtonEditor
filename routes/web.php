<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::inertia('/', 'Home', ['user' => 'Maurice']);

Route::inertia('/about', 'About');
