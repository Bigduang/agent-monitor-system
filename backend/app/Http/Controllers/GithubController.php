<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubController extends Controller
{
    private string $token;
    private string $owner;
    private string $repo;

    public function __construct()
    {
        // GitHub Token from .env
        $this->token = config('services.github.token');
        $this->owner = config('services.github.owner');
        $this->repo = config('services.github.repo');
    }

    /**
     * Get Issues list
     */
    public function issues(Request $request)
    {
        try {
            $state = $request->get('state', 'all');
            $perPage = $request->get('per_page', 30);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get("https://api.github.com/repos/{$this->owner}/{$this->repo}/issues", [
                'state' => $state,
                'per_page' => $perPage,
                'sort' => 'updated',
                'direction' => 'desc',
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch issues',
                    'message' => $response->body(),
                ], $response->status());
            }

            $issues = $response->json();
            
            // Filter out pull requests (they appear in issues API)
            $issues = array_filter($issues, function ($issue) {
                return !isset($issue['pull_request']);
            });

            return response()->json([
                'success' => true,
                'data' => array_values($issues),
                'count' => count($issues),
            ]);
        } catch (\Exception $e) {
            Log::error('GitHub API Error (issues): ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Pull Requests list
     */
    public function pulls(Request $request)
    {
        try {
            $state = $request->get('state', 'all');
            $perPage = $request->get('per_page', 30);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get("https://api.github.com/repos/{$this->owner}/{$this->repo}/pulls", [
                'state' => $state,
                'per_page' => $perPage,
                'sort' => 'updated',
                'direction' => 'desc',
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch pull requests',
                    'message' => $response->body(),
                ], $response->status());
            }

            $pulls = $response->json();

            return response()->json([
                'success' => true,
                'data' => $pulls,
                'count' => count($pulls),
            ]);
        } catch (\Exception $e) {
            Log::error('GitHub API Error (pulls): ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Commits list
     */
    public function commits(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 30);
            $sha = $request->get('sha', ''); // branch name

            $params = [
                'per_page' => $perPage,
            ];
            
            if ($sha) {
                $params['sha'] = $sha;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get("https://api.github.com/repos/{$this->owner}/{$this->repo}/commits", $params);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch commits',
                    'message' => $response->body(),
                ], $response->status());
            }

            $commits = $response->json();

            return response()->json([
                'success' => true,
                'data' => $commits,
                'count' => count($commits),
            ]);
        } catch (\Exception $e) {
            Log::error('GitHub API Error (commits): ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
