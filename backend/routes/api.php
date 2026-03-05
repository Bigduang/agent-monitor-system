<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GitHubController;
use App\Http\Controllers\AgentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Agent 状态
Route::get('/agents/status', [AgentController::class, 'status']);

// GitHub 集成

Route::prefix('github')->group(function () {
    // 获取仓库信息
    Route::get('/repos/{owner}/{repo}', [GitHubController::class, 'getRepo']);
    
    // 获取仓库活动
    Route::get('/repos/{owner}/{repo}/activity', [GitHubController::class, 'getActivity']);
    
    // 获取 Issue 列表
    Route::get('/repos/{owner}/{repo}/issues', [GitHubController::class, 'getIssues']);
    
    // 获取 PR 列表
    Route::get('/repos/{owner}/{repo}/pulls', [GitHubController::class, 'getPulls']);
    
    // 获取提交历史
    Route::get('/repos/{owner}/{repo}/commits', [GitHubController::class, 'getCommits']);
    
    // 获取分支列表
    Route::get('/repos/{owner}/{repo}/branches', [GitHubController::class, 'getBranches']);
});

// 健康检查
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});
