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
    ? { text: 'OPEN', class: 'cyber-label success' }
    : { text: 'CLOSED', class: 'cyber-label' }
}

function getPRStateLabel(pr: GitHubPullRequest): { text: string; class: string } {
  if (pr.merged) {
    return { text: 'MERGED', class: 'cyber-label danger' }
  }
  return pr.state === 'open'
    ? { text: 'OPEN', class: 'cyber-label success' }
    : { text: 'CLOSED', class: 'cyber-label' }
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <div class="github-panel">
    <div class="flex items-center justify-between mb-6">
      <h2 class="cyber-title">
        <span class="text-glow-pink">🐙</span> GitHub Issues & PRs
      </h2>
      <button 
        @click="loadData" 
        class="cyber-button"
        :disabled="loading"
      >
        <span v-if="loading" class="blink">⟳</span>
        <span v-else>🔄</span>
        刷新
      </button>
    </div>

    <!-- Tab 切换 -->
    <div class="flex gap-3 mb-6">
      <button
        @click="activeTab = 'issues'"
        class="cyber-tab"
        :class="{ active: activeTab === 'issues' }"
      >
        📋 Issues ({{ issues.length }})
      </button>
      <button
        @click="activeTab = 'pulls'"
        class="cyber-tab"
        :class="{ active: activeTab === 'pulls' }"
      >
        🔀 PRs ({{ pulls.length }})
      </button>
    </div>

    <!-- 加载状态 -->
    <div v-if="loading && !error" class="cyber-card loading-scanline">
      <div class="cyber-loading">
        <div class="cyber-loading-dot"></div>
        <div class="cyber-loading-dot"></div>
        <div class="cyber-loading-dot"></div>
      </div>
      <p class="text-center text-gray-400 mt-4">正在连接 GitHub API...</p>
    </div>

    <!-- 错误状态 -->
    <div v-else-if="error" class="cyber-card border-red-500">
      <p class="text-red-400 text-center">⚠️ {{ error }}</p>
    </div>

    <!-- Issues 列表 -->
    <div v-else-if="activeTab === 'issues'" class="space-y-3">
      <div 
        v-for="(issue, index) in issues" 
        :key="issue.id"
        class="cyber-card fade-in"
        :style="{ animationDelay: (index * 0.05) + 's' }"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
              <a 
                :href="issue.html_url" 
                target="_blank"
                class="cyber-link font-semibold text-lg"
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
            <div class="flex items-center gap-4 text-sm text-gray-400">
              <span>👤 {{ issue.user.login }}</span>
              <span>📅 {{ formatDate(issue.created_at) }}</span>
              <span>💬 {{ issue.comments }} 条评论</span>
            </div>
            <div v-if="issue.labels.length" class="flex gap-2 mt-3 flex-wrap">
              <span 
                v-for="label in issue.labels" 
                :key="label.name"
                class="cyber-label"
                :style="{ 
                  backgroundColor: 'rgba(' + parseInt(label.color.slice(0,2), 16) + ',' + parseInt(label.color.slice(2,4), 16) + ',' + parseInt(label.color.slice(4,6), 16) + ', 0.2)', 
                  borderColor: '#' + label.color,
                  color: '#' + label.color
                }"
              >
                {{ label.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="issues.length === 0" class="cyber-card text-center py-8">
        <p class="text-gray-400">📭 暂无 Issues</p>
      </div>
    </div>

    <!-- PRs 列表 -->
    <div v-else class="space-y-3">
      <div 
        v-for="(pr, index) in pulls" 
        :key="pr.id"
        class="cyber-card fade-in"
        :style="{ animationDelay: (index * 0.05) + 's' }"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
              <a 
                :href="pr.html_url" 
                target="_blank"
                class="cyber-link font-semibold text-lg"
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
            <div class="flex items-center gap-4 text-sm text-gray-400">
              <span>👤 {{ pr.user.login }}</span>
              <span>📅 {{ formatDate(pr.created_at) }}</span>
            </div>
            <div v-if="pr.labels.length" class="flex gap-2 mt-3 flex-wrap">
              <span 
                v-for="label in pr.labels" 
                :key="label.name"
                class="cyber-label"
                :style="{ 
                  backgroundColor: 'rgba(' + parseInt(label.color.slice(0,2), 16) + ',' + parseInt(label.color.slice(2,4), 16) + ',' + parseInt(label.color.slice(4,6), 16) + ', 0.2)', 
                  borderColor: '#' + label.color,
                  color: '#' + label.color
                }"
              >
                {{ label.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="pulls.length === 0" class="cyber-card text-center py-8">
        <p class="text-gray-400">📭 暂无 PRs</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.github-panel {
  position: relative;
}

.border-red-500 {
  border-color: rgba(255, 0, 0, 0.5);
}
</style>
