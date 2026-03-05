import type { AgentsStatusResponse } from '../types/agent'

// API 基础 URL - 可以通过环境变量配置
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

/**
 * 获取 Agent 状态列表
 */
export async function fetchAgentsStatus(): Promise<AgentsStatusResponse> {
  const response = await fetch(`${API_BASE_URL}/agents/status`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch agents status: ${response.statusText}`)
  }
  
  return response.json()
}
