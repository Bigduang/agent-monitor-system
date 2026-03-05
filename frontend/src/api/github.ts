import type { GitHubIssuesResponse, GitHubPullsResponse } from '../types/github'

// API 基础 URL - 可以通过环境变量配置
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

// 是否使用 Mock 数据（当后端 API 不可用时）
const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true' || true

// Mock 数据
const MOCK_ISSUES: GitHubIssuesResponse = {
  issues: [
    {
      id: 1,
      number: 15,
      title: "[前端] 步骤4: 展示 GitHub Issues 和 PRs",
      state: 'open',
      html_url: 'https://github.com/Bigduang/agent-monitor-system/issues/15',
      created_at: '2026-03-05T00:00:00Z',
      updated_at: '2026-03-05T08:00:00Z',
      user: {
        login: 'agent-frontend',
        avatar_url: 'https://avatars.githubusercontent.com/u/1?v=4'
      },
      labels: [{ name: 'frontend', color: '0075ca' }],
      comments: 2
    },
    {
      id: 2,
      number: 14,
      title: "[后端] 步骤3: 实现 GitHub API",
      state: 'open',
      html_url: 'https://github.com/Bigduang/agent-monitor-system/issues/14',
      created_at: '2026-03-04T00:00:00Z',
      updated_at: '2026-03-04T12:00:00Z',
      user: {
        login: 'agent-backend',
        avatar_url: 'https://avatars.githubusercontent.com/u/2?v=4'
      },
      labels: [{ name: 'backend', color: 'a2eeef' }],
      comments: 1
    },
    {
      id: 3,
      number: 10,
      title: "[前端] 步骤2: Agent 状态展示",
      state: 'closed',
      html_url: 'https://github.com/Bigduang/agent-monitor-system/issues/10',
      created_at: '2026-03-02T00:00:00Z',
      updated_at: '2026-03-03T18:00:00Z',
      user: {
        login: 'agent-frontend',
        avatar_url: 'https://avatars.githubusercontent.com/u/1?v=4'
      },
      labels: [{ name: 'frontend', color: '0075ca' }, { name: 'done', color: '0e8a16' }],
      comments: 5
    }
  ]
}

const MOCK_PULLS: GitHubPullsResponse = {
  pulls: [
    {
      id: 1,
      number: 16,
      title: "feat: 添加 Agent 状态展示面板",
      state: 'open',
      html_url: 'https://github.com/Bigduang/agent-monitor-system/pull/16',
      created_at: '2026-03-05T06:00:00Z',
      updated_at: '2026-03-05T08:30:00Z',
      user: {
        login: 'agent-frontend',
        avatar_url: 'https://avatars.githubusercontent.com/u/1?v=4'
      },
      labels: [{ name: 'enhancement', color: '84b6eb' }],
      merged: false
    },
    {
      id: 2,
      number: 12,
      title: "feat: 初始化项目结构",
      state: 'merged',
      html_url: 'https://github.com/Bigduang/agent-monitor-system/pull/12',
      created_at: '2026-03-01T00:00:00Z',
      updated_at: '2026-03-02T20:00:00Z',
      user: {
        login: 'agent-manager',
        avatar_url: 'https://avatars.githubusercontent.com/u/3?v=4'
      },
      labels: [{ name: 'feature', color: 'a2eeef' }, { name: 'merged', color: '6e5494' }],
      merged: true
    }
  ]
}

/**
 * 获取 GitHub Issues 列表
 */
export async function fetchGitHubIssues(
  owner: string = 'Bigduang', 
  repo: string = 'agent-monitor-system'
): Promise<GitHubIssuesResponse> {
  if (USE_MOCK) {
    console.log('[GitHub API] Using mock data for issues')
    return MOCK_ISSUES
  }
  
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/issues`)
  
  if (!response.ok) {
    console.warn('[GitHub API] Failed to fetch issues, using mock data')
    return MOCK_ISSUES
  }
  
  return response.json()
}

/**
 * 获取 GitHub Pull Requests 列表
 */
export async function fetchGitHubPulls(
  owner: string = 'Bigduang', 
  repo: string = 'agent-monitor-system'
): Promise<GitHubPullsResponse> {
  if (USE_MOCK) {
    console.log('[GitHub API] Using mock data for pulls')
    return MOCK_PULLS
  }
  
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/pulls`)
  
  if (!response.ok) {
    console.warn('[GitHub API] Failed to fetch pulls, using mock data')
    return MOCK_PULLS
  }
  
  return response.json()
}
