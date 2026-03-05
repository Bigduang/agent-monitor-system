<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Mock data for agents
$agents = [
    [
        'id' => 'frontend001',
        'name' => '宫下玲奈',
        'model' => 'MiniMax-M2.5',
        'lastActive' => time() * 1000,
        'isOnline' => true
    ],
    [
        'id' => 'backend001',
        'name' => '田中柠檬',
        'model' => 'MiniMax-M2.5',
        'lastActive' => (time() - 120) * 1000,
        'isOnline' => true
    ],
    [
        'id' => 'manager001',
        'name' => '总工程师',
        'model' => 'MiniMax-M2.5',
        'lastActive' => (time() - 300) * 1000,
        'isOnline' => false
    ]
];

if ($path === '/api/agents' || $path === '/api/agents/status') {
    echo json_encode([
        'agents' => $agents
    ]);
} elseif ($path === '/') {
    echo json_encode(['status' => 'ok', 'message' => 'Agent Monitor API']);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
