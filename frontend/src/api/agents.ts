import type { AgentsStatusResponse } from '../types/agent'

// API 基础 URL - 内网地址
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://192.168.8.96:8000/api'

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
