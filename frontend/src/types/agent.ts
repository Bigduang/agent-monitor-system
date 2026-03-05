// Agent 状态类型定义

export interface Agent {
  id: string
  name: string
  model: string
  lastActive: number
  isOnline: boolean
}

export interface AgentsStatusResponse {
  agents: Agent[]
}
