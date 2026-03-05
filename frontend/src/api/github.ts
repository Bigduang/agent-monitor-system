import type { GitHubRepo, GitHubIssue, GitHubPullRequest, GitHubCommit, GitHubBranch } from '../types/github'

// API 基础 URL - 默认使用真实后端 API
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

/**
 * 获取仓库信息
 */
export async function fetchRepo(owner: string, repo: string): Promise<GitHubRepo> {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch repo: ${response.statusText}`)
  }
  
  return response.json()
}

/**
 * 获取仓库活动
 */
export async function fetchRepoActivity(owner: string, repo: string) {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/activity`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch repo activity: ${response.statusText}`)
  }
  
  return response.json()
}

/**
 * 获取 Issue 列表
 */
export async function fetchIssues(owner: string, repo: string): Promise<GitHubIssue[]> {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/issues`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch issues: ${response.statusText}`)
  }
  
  return response.json()
}

/**
 * 获取 PR 列表
 */
export async function fetchPullRequests(owner: string, repo: string): Promise<GitHubPullRequest[]> {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/pulls`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch pull requests: ${response.statusText}`)
  }
  
  return response.json()
}

/**
 * 获取提交历史
 */
export async function fetchCommits(owner: string, repo: string): Promise<GitHubCommit[]> {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/commits`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch commits: ${response.statusText}`)
  }
  
  return response.json()
}

/**
 * 获取分支列表
 */
export async function fetchBranches(owner: string, repo: string): Promise<GitHubBranch[]> {
  const response = await fetch(`${API_BASE_URL}/github/repos/${owner}/${repo}/branches`)
  
  if (!response.ok) {
    throw new Error(`Failed to fetch branches: ${response.statusText}`)
  }
  
  return response.json()
}
