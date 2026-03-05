<?php

// Simple router for GitHub API without Laravel dependencies

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// GitHub API configuration - use environment variable or empty
$token = getenv('GITHUB_TOKEN') ?: '';
$baseUrl = 'https://api.github.com';

$headers = [
    'Accept: application/vnd.github.v3+json',
    'User-Agent: Agent-Monitor-System/1.0'
];

if ($token) {
    $headers[] = 'Authorization: token ' . $token;
}

function makeRequest($url, $headers) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 30,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    $httpCode = 500;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('/^HTTP\/\d+\.\d+\s+(\d+)/', $header, $matches)) {
                $httpCode = (int)$matches[1];
            }
        }
    }
    
    return ['body' => $response ?: '{}', 'code' => $httpCode];
}

// Default GitHub repository from .env
$defaultOwner = 'Bigduang';
$defaultRepo = 'agent-monitor-system';

// Route: /api/github/issues (uses default repo)
if (preg_match('#^/api/github/issues#', $requestUri, $matches)) {
    $state = $_GET['state'] ?? 'open';
    
    $url = "$baseUrl/repos/$defaultOwner/$defaultRepo/issues?state=$state&per_page=30";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/pulls (uses default repo)
if (preg_match('#^/api/github/pulls#', $requestUri, $matches)) {
    $state = $_GET['state'] ?? 'open';
    
    $url = "$baseUrl/repos/$defaultOwner/$defaultRepo/pulls?state=$state&per_page=30";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}/issues
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)/issues#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    $state = $_GET['state'] ?? 'open';
    
    $url = "$baseUrl/repos/$owner/$repo/issues?state=$state&per_page=30";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}/pulls
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)/pulls#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    $state = $_GET['state'] ?? 'open';
    
    $url = "$baseUrl/repos/$owner/$repo/pulls?state=$state&per_page=30";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)$#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    
    $url = "$baseUrl/repos/$owner/$repo";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}/activity
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)/activity#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    
    $url = "$baseUrl/repos/$owner/$repo/events?per_page=30";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}/commits
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)/commits#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    $sha = $_GET['sha'] ?? '';
    
    $url = "$baseUrl/repos/$owner/$repo/commits";
    if ($sha) {
        $url .= "?sha=$sha&per_page=30";
    } else {
        $url .= "?per_page=30";
    }
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Route: /api/github/repos/{owner}/{repo}/branches
if (preg_match('#^/api/github/repos/([^/]+)/([^/]+)/branches#', $requestUri, $matches)) {
    $owner = $matches[1];
    $repo = $matches[2];
    
    $url = "$baseUrl/repos/$owner/$repo/branches?per_page=100";
    $result = makeRequest($url, $headers);
    
    header('Content-Type: application/json');
    http_response_code($result['code']);
    echo $result['body'];
    exit;
}

// Health check
if ($requestUri === '/api/health') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'timestamp' => date('c')]);
    exit;
}

// 404
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);
