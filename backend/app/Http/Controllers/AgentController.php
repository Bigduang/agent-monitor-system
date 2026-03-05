<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
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
            // OpenClaw 数据目录
            $openClawHome = '/home/zczd/.openclaw';
            
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
            
            // 遍历每个 agent 读取 sessions.json
            foreach ($agentMap as $agentId => $info) {
                $sessionsFile = $openClawHome . '/agents/' . $agentId . '/sessions/sessions.json';
                
                $isOnline = false;
                $lastActive = null;
                $updatedAt = null;
                $model = 'offline';
                
                if (File::exists($sessionsFile)) {
                    $content = File::get($sessionsFile);
                    $sessions = json_decode($content, true);
                    
                    if ($sessions && is_array($sessions)) {
                        // 找到最新的 session
                        $maxUpdatedAt = 0;
                        $latestSession = null;
                        
                        foreach ($sessions as $key => $session) {
                            $updatedAt = $session['updatedAt'] ?? $session['updated_at'] ?? 0;
                            if ($updatedAt > $maxUpdatedAt) {
                                $maxUpdatedAt = $updatedAt;
                                $latestSession = $session;
                            }
                        }
                        
                        if ($latestSession) {
                            $updatedAt = $latestSession['updatedAt'] ?? $latestSession['updated_at'] ?? 0;
                            $now = round(microtime(true) * 1000);
                            $isOnline = ($now - $updatedAt) < $onlineThreshold;
                            $lastActive = $updatedAt;
                            $model = $latestSession['model'] ?? 'unknown';
                        }
                    }
                }
                
                $agents[] = [
                    'id' => $info['id'],
                    'name' => $info['name'],
                    'role' => $info['role'],
                    'model' => $model,
                    'lastActive' => $lastActive,
                    'isOnline' => $isOnline,
                    'updatedAt' => $updatedAt
                ];
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
                ]
            ], 200);
        }
    }
}
