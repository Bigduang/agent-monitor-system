<?php

namespace App\Http\Controllers;

use App\Services\GitHubService;
use Illuminate\Http\JsonResponse;

class GitHubController extends Controller
{
    protected GitHubService $github;
    
    public function __construct(GitHubService $github)
    {
        $this->github = $github;
    }
    
    /**
     * 获取仓库信息
     */
    public function getRepo(string $owner, string $repo): JsonResponse
    {
        $result = $this->github->getRepo($owner, $repo);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 获取仓库活动
     */
    public function getActivity(string $owner, string $repo): JsonResponse
    {
        $result = $this->github->getActivity($owner, $repo);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 获取 Issue 列表
     */
    public function getIssues(string $owner, string $repo, string $state = 'open'): JsonResponse
    {
        $result = $this->github->getIssues($owner, $repo, $state);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 获取 PR 列表
     */
    public function getPulls(string $owner, string $repo, string $state = 'open'): JsonResponse
    {
        $result = $this->github->getPulls($owner, $repo, $state);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 获取提交历史
     */
    public function getCommits(string $owner, string $repo, string $sha = ''): JsonResponse
    {
        $result = $this->github->getCommits($owner, $repo, $sha);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 获取分支列表
     */
    public function getBranches(string $owner, string $repo): JsonResponse
    {
        $result = $this->github->getBranches($owner, $repo);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'],
            ], $result['status'] ?? 400);
        }
        
        return response()->json($result['data']);
    }
    
    /**
     * 兼容路由：获取默认仓库的 Issue 列表
     * GET /api/github/issues
     */
    public function getIssuesCompat(string $state = 'open'): JsonResponse
    {
        return $this->getIssues('Bigduang', 'agent-monitor-system', $state);
    }
    
    /**
     * 兼容路由：获取默认仓库的 PR 列表
     * GET /api/github/pulls
     */
    public function getPullsCompat(string $state = 'open'): JsonResponse
    {
        return $this->getPulls('Bigduang', 'agent-monitor-system', $state);
    }
}
