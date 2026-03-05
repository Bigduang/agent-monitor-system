<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchIssues, fetchPullRequests } from '../api/github'
import type { GitHubIssue, GitHubPullRequest } from '../types/github'

const GITHUB_OWNER = 'Bigduang'
const GITHUB_REPO = 'agent-monitor-system'

const issues = ref<GitHubIssue[]>([])
const pullRequests = ref<GitHubPullRequest[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

async function loadGitHubData() {
  try {
    loading.value = true
    error.value = null
    
    // 调用 fetchIssues 和 fetchPullRequests，传递 owner 和 repo 参数
    const [issuesData, prsData] = await Promise.all([
      fetchIssues(GITHUB_OWNER, GITHUB_REPO),
      fetchPullRequests(GITHUB_OWNER, GITHUB_REPO)
    ])
    
    issues.value = issuesData
    pullRequests.value = prsData
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Failed to load GitHub data'
    console.error('Failed to fetch GitHub data:', e)
  } finally {
    loading.value = false
  }
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('zh-CN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  loadGitHubData()
})
</script>

<template>
  <div class="github-panel">
    <h2 class="text-xl font-bold mb-4">🐙 GitHub 仓库状态</h2>
    
    <div v-if="loading && issues.length === 0 && pullRequests.length === 0" class="text-gray-500">
      加载中...
    </div>
    
    <div v-else-if="error" class="text-red-500">
      {{ error }}
    </div>
    
    <div v-else class="space-y-6">
      <!-- Issues Section -->
      <div class="cyber-card p-4">
        <h3 class="text-lg font-semibold mb-3">📋 Issues ({{ issues.length }})</h3>
        <div v-if="issues.length === 0" class="text-gray-500 text-sm">
          暂无 Issues
        </div>
        <div v-else class="space-y-2">
          <div 
            v-for="issue in issues" 
            :key="issue.id"
            class="border-l-4 border-blue-500 pl-3 py-2"
          >
            <a :href="issue.html_url" target="_blank" class="font-medium hover:text-blue-600">
              #{{ issue.number }} {{ issue.title }}
            </a>
            <div class="text-sm text-gray-500">
              opened by {{ issue.user.login }} on {{ formatDate(issue.created_at) }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pull Requests Section -->
      <div class="cyber-card p-4">
        <h3 class="text-lg font-semibold mb-3">🔀 Pull Requests ({{ pullRequests.length }})</h3>
        <div v-if="pullRequests.length === 0" class="text-gray-500 text-sm">
          暂无 Pull Requests
        </div>
        <div v-else class="space-y-2">
          <div 
            v-for="pr in pullRequests" 
            :key="pr.id"
            class="border-l-4 border-green-500 pl-3 py-2"
          >
            <a :href="pr.html_url" target="_blank" class="font-medium hover:text-green-600">
              #{{ pr.number }} {{ pr.title }}
            </a>
            <div class="text-sm text-gray-500">
              {{ pr.head.ref }} → {{ pr.base.ref }} by {{ pr.user.login }} on {{ formatDate(pr.created_at) }}
            </div>
          </div>
        </div>
      </div>
      
      <div class="text-sm">
        <button @click="loadGitHubData" class="text-blue-500 hover:underline">
          🔄 刷新
        </button>
      </div>
    </div>
  </div>
</template>
