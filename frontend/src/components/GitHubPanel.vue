<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchGitHubIssues, fetchGitHubPulls } from '../api/github'
import type { GitHubIssue, GitHubPullRequest } from '../types/github'

const issues = ref<GitHubIssue[]>([])
const pulls = ref<GitHubPullRequest[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const activeTab = ref<'issues' | 'pulls'>('issues')

async function loadData() {
  try {
    loading.value = true
    error.value = null
    
    const [issuesRes, pullsRes] = await Promise.all([
      fetchGitHubIssues(),
      fetchGitHubPulls()
    ])
    
    issues.value = issuesRes.issues
    pulls.value = pullsRes.pulls
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

function getIssueStateLabel(state: 'open' | 'closed'): { text: string; class: string } {
  return state === 'open' 
    ? { text: 'OPEN', class: 'bg-green-100 text-green-800' }
    : { text: 'CLOSED', class: 'bg-gray-100 text-gray-800' }
}

function getPRStateLabel(pr: GitHubPullRequest): { text: string; class: string } {
  if (pr.merged) {
    return { text: 'MERGED', class: 'bg-purple-100 text-purple-800' }
  }
  return pr.state === 'open'
    ? { text: 'OPEN', class: 'bg-green-100 text-green-800' }
    : { text: 'CLOSED', class: 'bg-gray-100 text-gray-800' }
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <div class="github-panel">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold">🐙 GitHub Issues & PRs</h2>
      <button 
        @click="loadData" 
        class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        🔄 刷新
      </button>
    </div>

    <!-- Tab 切换 -->
    <div class="flex gap-2 mb-4">
      <button
        @click="activeTab = 'issues'"
        class="px-4 py-2 rounded-lg font-medium transition-colors"
        :class="activeTab === 'issues' 
          ? 'bg-blue-500 text-white' 
          : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
      >
        📋 Issues ({{ issues.length }})
      </button>
      <button
        @click="activeTab = 'pulls'"
        class="px-4 py-2 rounded-lg font-medium transition-colors"
        :class="activeTab === 'pulls' 
          ? 'bg-blue-500 text-white' 
          : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
      >
        🔀 PRs ({{ pulls.length }})
      </button>
    </div>

    <!-- 加载状态 -->
    <div v-if="loading && !error" class="text-gray-500 py-8 text-center">
      加载中...
    </div>

    <!-- 错误状态 -->
    <div v-else-if="error" class="text-red-500 py-8 text-center">
      {{ error }}
    </div>

    <!-- Issues 列表 -->
    <div v-else-if="activeTab === 'issues'" class="space-y-3">
      <div 
        v-for="issue in issues" 
        :key="issue.id"
        class="border rounded-lg p-4 bg-white shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <a 
                :href="issue.html_url" 
                target="_blank"
                class="font-semibold text-blue-600 hover:underline"
              >
                #{{ issue.number }} {{ issue.title }}
              </a>
              <span 
                class="px-2 py-0.5 text-xs rounded-full"
                :class="getIssueStateLabel(issue.state).class"
              >
                {{ getIssueStateLabel(issue.state).text }}
              </span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-500">
              <span>👤 {{ issue.user.login }}</span>
              <span>📅 {{ formatDate(issue.created_at) }}</span>
              <span>💬 {{ issue.comments }} 条评论</span>
            </div>
            <div v-if="issue.labels.length" class="flex gap-1 mt-2">
              <span 
                v-for="label in issue.labels" 
                :key="label.name"
                class="px-2 py-0.5 text-xs rounded-full"
                :style="{ backgroundColor: '#' + label.color, color: '#fff' }"
              >
                {{ label.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="issues.length === 0" class="text-gray-500 text-center py-8">
        暂无 Issues
      </div>
    </div>

    <!-- PRs 列表 -->
    <div v-else class="space-y-3">
      <div 
        v-for="pr in pulls" 
        :key="pr.id"
        class="border rounded-lg p-4 bg-white shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <a 
                :href="pr.html_url" 
                target="_blank"
                class="font-semibold text-blue-600 hover:underline"
              >
                #{{ pr.number }} {{ pr.title }}
              </a>
              <span 
                class="px-2 py-0.5 text-xs rounded-full"
                :class="getPRStateLabel(pr).class"
              >
                {{ getPRStateLabel(pr).text }}
              </span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-500">
              <span>👤 {{ pr.user.login }}</span>
              <span>📅 {{ formatDate(pr.created_at) }}</span>
            </div>
            <div v-if="pr.labels.length" class="flex gap-1 mt-2">
              <span 
                v-for="label in pr.labels" 
                :key="label.name"
                class="px-2 py-0.5 text-xs rounded-full"
                :style="{ backgroundColor: '#' + label.color, color: '#fff' }"
              >
                {{ label.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="pulls.length === 0" class="text-gray-500 text-center py-8">
        暂无 PRs
      </div>
    </div>
  </div>
</template>
