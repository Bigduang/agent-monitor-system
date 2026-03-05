<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GithubController;

// GitHub API Routes
Route::get('/github/issues', [GithubController::class, 'issues']);
Route::get('/github/pulls', [GithubController::class, 'pulls']);
Route::get('/github/commits', [GithubController::class, 'commits']);
