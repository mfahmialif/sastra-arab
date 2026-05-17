<?php

use App\Http\Controllers\SpaSeoController;
use Illuminate\Support\Facades\Route;

Route::get('/{path?}', SpaSeoController::class)
    ->where('path', '^(?!api|sanctum|storage|assets|fonts|img|build|vite\.svg).*$');
