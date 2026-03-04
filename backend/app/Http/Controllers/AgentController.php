<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentController extends Controller
{
    /**
     * 获取 Agent 状态
     * GET /api/agents/status
     */
    public function status()
    {
        try {
            // 调用 OpenClaw sessions_list API
            // 本地部署时使用内部 API
            $response = Http::timeout(10)->get('http://localhost:3000/api/sessions/list');
            
            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch agent status',
                    'message' => $response->body()
                ], 500);
            }
            
            $sessions = $response->json();
            
            // Agent 映射配置
            $agentMap = [
                'frontend001' => [
                    'id' => 'frontend001',
                    'name' => '宫下玲奈',
                    'role' => '前端工程师'
                ],
                'backend001' => [
                    'id' => 'backend001',
                    'name' => '田中柠檬',
                    'role' => '后端工程师'
                ],
                'chief001' => [
                    'id' => 'chief001',
                    'name' => '总工程师',
                    'role' => '总工程师'
                ]
            ];
            
            $agents = [];
            $onlineThreshold = 5 * 60 * 1000; // 5分钟 = 300000ms
            
            // 处理 sessions 数据
            if (isset($sessions['sessions']) && is_array($sessions['sessions'])) {
                foreach ($sessions['sessions'] as $session) {
                    $sessionId = $session['sessionId'] ?? $session['id'] ?? null;
                    
                    if (!$sessionId || !isset($agentMap[$sessionId])) {
                        continue;
                    }
                    
                    $agent = $agentMap[$sessionId];
                    $updatedAt = $session['updatedAt'] ?? $session['updated_at'] ?? 0;
                    $now = round(microtime(true) * 1000);
                    
                    $agents[] = [
                        'id' => $agent['id'],
                        'name' => $agent['name'],
                        'role' => $agent['role'],
                        'model' => $session['model'] ?? 'unknown',
                        'lastActive' => $updatedAt,
                        'isOnline' => ($now - $updatedAt) < $onlineThreshold,
                        'updatedAt' => $updatedAt
                    ];
                }
            }
            
            // 填充未上线的 Agent
            foreach ($agentMap as $id => $info) {
                $found = false;
                foreach ($agents as $agent) {
                    if ($agent['id'] === $id) {
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $agents[] = [
                        'id' => $info['id'],
                        'name' => $info['name'],
                        'role' => $info['role'],
                        'model' => 'offline',
                        'lastActive' => null,
                        'isOnline' => false,
                        'updatedAt' => null
                    ];
                }
            }
            
            return response()->json([
                'agents' => $agents
            ]);
            
        } catch (\Exception $e) {
            Log::error('Agent status error: ' . $e->getMessage());
            
            // 返回离线状态
            return response()->json([
                'agents' => [
                    [
                        'id' => 'frontend001',
                        'name' => '宫下玲奈',
                        'role' => '前端工程师',
                        'model' => 'offline',
                        'lastActive' => null,
                        'isOnline' => false,
                        'updatedAt' => null
                    ],
                    [
                        'id' => 'backend001',
                        'name' => '田中柠檬',
                        'role' => '后端工程师',
                        'model' => 'offline',
                        'lastActive' => null,
                        'isOnline' => false,
                        'updatedAt' => null
                    ],
                    [
                        'id' => 'chief001',
                        'name' => '总工程师',
                        'role' => '总工程师',
                        'model' => 'offline',
                        'lastActive' => null,
                        'isOnline' => false,
                        'updatedAt' => null
                    ]
                ],
                'error' => 'Failed to connect to OpenClaw API'
            ], 200);
        }
    }
}
