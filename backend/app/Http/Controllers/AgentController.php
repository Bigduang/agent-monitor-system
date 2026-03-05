<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AgentController extends Controller
{
    /**
     * 获取 Agent 状态
     * GET /api/agents/status
     */
    public function status()
    {
        try {
            // 调用 OpenClaw gateway status CLI 命令获取会话状态
            // 使用 shell_exec 确保环境变量正确
            $openclawPath = '/home/zczd/.nvm/versions/node/v22.22.0/bin/openclaw';
            $output = shell_exec("$openclawPath gateway call status --json 2>&1");

            if (empty($output)) {
                throw new \Exception('OpenClaw command returned empty output');
            }

            $statusData = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse OpenClaw status: ' . json_last_error_msg() . ' - Output: ' . substr($output, 0, 200));
            }

            // 从 status 数据中提取 sessions.recent
            $sessions = $statusData['sessions']['recent'] ?? [];
            
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
            
            // 处理 sessions 数据 - 新格式使用 agentId 字段
            foreach ($sessions as $session) {
                $agentId = $session['agentId'] ?? null;
                
                if (!$agentId || !isset($agentMap[$agentId])) {
                    continue;
                }
                
                $agent = $agentMap[$agentId];
                $updatedAt = $session['updatedAt'] ?? 0;
                $now = round(microtime(true) * 1000);
                
                // 检查是否已经添加过这个 agent（只保留最新的）
                $exists = false;
                foreach ($agents as $existingAgent) {
                    if ($existingAgent['id'] === $agentId) {
                        $exists = true;
                        // 如果新的更活跃，则更新
                        if ($updatedAt > $existingAgent['updatedAt']) {
                            $agents = array_filter($agents, fn($a) => $a['id'] !== $agentId);
                            $agents = array_values($agents);
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
                        break;
                    }
                }
                
                if (!$exists) {
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
