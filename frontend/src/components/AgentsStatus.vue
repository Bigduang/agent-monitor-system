<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { fetchAgentsStatus } from '../api/agents'
import type { Agent } from '../types/agent'

const agents = ref<Agent[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

// 定时刷新间隔（毫秒）
const REFRESH_INTERVAL = 30000 // 30秒
let timer: ReturnType<typeof setInterval> | null = null

async function loadAgentsStatus() {
  try {
    loading.value = true
    error.value = null
    const data = await fetchAgentsStatus()
    agents.value = data.agents
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Failed to load agents status'
    console.error('Failed to fetch agents status:', e)
  } finally {
    loading.value = false
  }
}

function formatLastActive(timestamp: number): string {
  const date = new Date(timestamp)
  return date.toLocaleTimeString('zh-CN', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

onMounted(() => {
  loadAgentsStatus()
  // 设置定时刷新
  timer = setInterval(loadAgentsStatus, REFRESH_INTERVAL)
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})
</script>

<template>
  <div class="agents-status">
    <h2 class="text-xl font-bold mb-4">🤖 Agent 状态</h2>
    
    <div v-if="loading && agents.length === 0" class="text-gray-500">
      加载中...
    </div>
    
    <div v-else-if="error" class="text-red-500">
      {{ error }}
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div 
        v-for="agent in agents" 
        :key="agent.id"
        class="border rounded-lg p-4 shadow-sm"
        :class="agent.isOnline ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50'"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="font-semibold">{{ agent.name }}</span>
          <span 
            class="px-2 py-1 text-xs rounded-full"
            :class="agent.isOnline ? 'bg-green-500 text-white' : 'bg-gray-400 text-white'"
          >
            {{ agent.isOnline ? '在线' : '离线' }}
          </span>
        </div>
        <div class="text-sm text-gray-600">
          <p>ID: {{ agent.id }}</p>
          <p>模型: {{ agent.model }}</p>
          <p>最后活跃: {{ formatLastActive(agent.lastActive) }}</p>
        </div>
      </div>
    </div>
    
    <div v-if="!loading && agents.length > 0" class="mt-4 text-sm text-gray-500">
      <button @click="loadAgentsStatus" class="text-blue-500 hover:underline">
        🔄 刷新
      </button>
    </div>
  </div>
</template>
