// GitHub 类型定义

export interface GitHubRepo {
  id: number
  name: string
  full_name: string
  description: string | null
  html_url: string
  stargazers_count: number
  forks_count: number
  open_issues_count: number
  language: string | null
  created_at: string
  updated_at: string
}

export interface GitHubIssue {
  id: number
  number: number
  title: string
  body: string | null
  state: string
  html_url: string
  user: {
    login: string
    avatar_url: string
  }
  created_at: string
  updated_at: string
  closed_at: string | null
}

export interface GitHubPullRequest {
  id: number
  number: number
  title: string
  body: string | null
  state: string
  html_url: string
  user: {
    login: string
    avatar_url: string
  }
  created_at: string
  updated_at: string
  merged_at: string | null
  head: {
    ref: string
    sha: string
  }
  base: {
    ref: string
    sha: string
  }
}

export interface GitHubCommit {
  sha: string
  commit: {
    message: string
    author: {
      name: string
      date: string
    }
  }
  author: {
    login: string
    avatar_url: string
  } | null
  html_url: string
}

export interface GitHubBranch {
  name: string
  commit: {
    sha: string
    url: string
  }
  protected: boolean
}
