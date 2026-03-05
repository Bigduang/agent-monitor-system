<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Agent {
  id: string
  name: string
  model: string
  lastActive: number
  isOnline: boolean
}

const agents = ref<Agent[]>([])
const loading = ref(true)
const error = ref('')

const API_URL = 'http://localhost:8000/api/agents'

const fetchAgents = async () => {
  try {
    loading.value = true
    const response = await fetch(API_URL)
    if (!response.ok) {
      throw new Error('Failed to fetch agents')
    }
    const data = await response.json()
    agents.value = data.agents
    error.value = ''
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Unknown error'
  } finally {
    loading.value = false
  }
}

const formatTime = (timestamp: number) => {
  const date = new Date(timestamp)
  return date.toLocaleTimeString('zh-CN', { 
    hour: '2-digit', 
    minute: '2-digit',
    second: '2-digit'
  })
}

const getTimeAgo = (timestamp: number) => {
  const seconds = Math.floor((Date.now() - timestamp) / 1000)
  if (seconds < 60) return `${seconds}秒前`
  if (seconds < 3600) return `${Math.floor(seconds / 60)}分钟前`
  return `${Math.floor(seconds / 3600)}小时前`
}

onMounted(() => {
  fetchAgents()
  // 每30秒刷新一次
  setInterval(fetchAgents, 30000)
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white">
    <!-- Header -->
    <header class="bg-white/10 backdrop-blur-md border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-pink-400 to-purple-400 bg-clip-text text-transparent">
          🎀 Agent 监控系统
        </h1>
        <p class="text-slate-400 mt-1">实时监控 Agent 工作状态</p>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
      <!-- Status Bar -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
          <span class="text-slate-400">在线 Agent:</span>
          <span class="text-2xl font-bold text-green-400">
            {{ agents.filter(a => a.isOnline).length }}
          </span>
          <span class="text-slate-500">/</span>
          <span class="text-2xl font-bold text-slate-400">
            {{ agents.length }}
          </span>
        </div>
        <button 
          @click="fetchAgents"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors flex items-center gap-2"
        >
          <span>🔄 刷新</span>
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading && agents.length === 0" class="flex justify-center py-20">
        <div class="animate-spin text-4xl">⏳</div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 text-center">
        <p class="text-red-400">❌ {{ error }}</p>
        <button 
          @click="fetchAgents"
          class="mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg"
        >
          重试
        </button>
      </div>

      <!-- Agent Cards Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="agent in agents" 
          :key="agent.id"
          class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-purple-500/50 transition-all hover:scale-105"
          :class="agent.isOnline ? 'shadow-lg shadow-green-500/20' : 'opacity-60'"
        >
          <!-- Agent Header -->
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div 
                class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                :class="agent.isOnline ? 'bg-green-500/20' : 'bg-slate-500/20'"
              >
                {{ agent.isOnline ? '🟢' : '⚫' }}
              </div>
              <div>
                <h3 class="text-xl font-bold">{{ agent.name }}</h3>
                <p class="text-sm text-slate-400">{{ agent.id }}</p>
              </div>
            </div>
            <span 
              class="px-3 py-1 rounded-full text-sm font-medium"
              :class="agent.isOnline 
                ? 'bg-green-500/20 text-green-400' 
                : 'bg-slate-500/20 text-slate-400'"
            >
              {{ agent.isOnline ? '在线' : '离线' }}
            </span>
          </div>

          <!-- Agent Details -->
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-t border-white/10">
              <span class="text-slate-400">🤖 模型</span>
              <span class="font-medium">{{ agent.model }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-t border-white/10">
              <span class="text-slate-400">🕐 最后活跃</span>
              <span class="font-medium">{{ getTimeAgo(agent.lastActive) }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-t border-white/10">
              <span class="text-slate-400">📅 更新时间</span>
              <span class="font-medium text-sm">{{ formatTime(agent.lastActive) }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-8 text-slate-500 text-sm">
      <p>🎀 Agent Monitor System - 由宫下玲奈开发</p>
    </footer>
  </div>
</template>

<style scoped>
</style>
