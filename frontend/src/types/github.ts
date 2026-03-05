// GitHub Issue 和 PR 相关类型

export interface GitHubIssue {
  id: number
  number: number
  title: string
  state: 'open' | 'closed'
  html_url: string
  created_at: string
  updated_at: string
  user: {
    login: string
    avatar_url: string
  }
  labels: Array<{
    name: string
    color: string
  }>
  comments: number
}

export interface GitHubPullRequest {
  id: number
  number: number
  title: string
  state: 'open' | 'closed' | 'merged'
  html_url: string
  created_at: string
  updated_at: string
  user: {
    login: string
    avatar_url: string
  }
  labels: Array<{
    name: string
    color: string
  }>
  merged: boolean
}

export interface GitHubIssuesResponse {
  issues: GitHubIssue[]
}

export interface GitHubPullsResponse {
  pulls: GitHubPullRequest[]
}
