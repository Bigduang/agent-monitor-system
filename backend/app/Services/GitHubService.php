<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GitHubService
{
    protected string $baseUrl = 'https://api.github.com';
    
    protected ?string $token;
    
    protected int $timeout = 30;
    
    public function __construct()
    {
        $this->token = config('services.github.token');
    }
    
    /**
     * 发送请求到 GitHub API
     */
    protected function request(string $method, string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'Agent-Monitor-System/1.0',
        ];
        
        if ($this->token) {
            $headers['Authorization'] = 'token ' . $this->token;
        }
        
        $response = Http::withHeaders($headers)
            ->timeout($this->timeout)
            ->$method($url, $params);
        
        if ($response->failed()) {
            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Request failed',
                'status' => $response->status(),
            ];
        }
        
        return [
            'success' => true,
            'data' => $response->json(),
        ];
    }
    
    /**
     * 获取仓库信息
     */
    public function getRepo(string $owner, string $repo): array
    {
        $cacheKey = "github:repo:{$owner}:{$repo}";
        
        return Cache::remember($cacheKey, 300, function () use ($owner, $repo) {
            return $this->request('GET', "/repos/{$owner}/{$repo}");
        });
    }
    
    /**
     * 获取仓库活动
     */
    public function getActivity(string $owner, string $repo): array
    {
        return $this->request('GET', "/repos/{$owner}/{$repo}/events", [
            'per_page' => 30,
        ]);
    }
    
    /**
     * 获取 Issue 列表
     */
    public function getIssues(string $owner, string $repo, string $state = 'open'): array
    {
        return $this->request('GET', "/repos/{$owner}/{$repo}/issues", [
            'state' => $state,
            'per_page' => 30,
        ]);
    }
    
    /**
     * 获取 PR 列表
     */
    public function getPulls(string $owner, string $repo, string $state = 'open'): array
    {
        return $this->request('GET', "/repos/{$owner}/{$repo}/pulls", [
            'state' => $state,
            'per_page' => 30,
        ]);
    }
    
    /**
     * 获取提交历史
     */
    public function getCommits(string $owner, string $repo, string $sha = ''): array
    {
        $endpoint = "/repos/{$owner}/{$repo}/commits";
        if ($sha) {
            $endpoint .= "?sha={$sha}&per_page=30";
        }
        
        return $this->request('GET', $endpoint);
    }
    
    /**
     * 获取分支列表
     */
    public function getBranches(string $owner, string $repo): array
    {
        return $this->request('GET', "/repos/{$owner}/{$repo}/branches", [
            'per_page' => 100,
        ]);
    }
    
    /**
     * 获取用户信息
     */
    public function getUser(string $username): array
    {
        return $this->request('GET', "/users/{$username}");
    }
}
