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
    <h2 class="cyber-title mb-6">
      <span class="text-glow">🤖</span> Agent 状态
    </h2>
    
    <!-- 加载状态 -->
    <div v-if="loading && agents.length === 0" class="cyber-card">
      <div class="cyber-loading">
        <div class="cyber-loading-dot"></div>
        <div class="cyber-loading-dot"></div>
        <div class="cyber-loading-dot"></div>
      </div>
      <p class="text-center text-gray-400 mt-4">系统初始化中...</p>
    </div>
    
    <div v-else-if="error" class="cyber-card border-red-500">
      <p class="text-red-400 text-center">⚠️ {{ error }}</p>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div 
        v-for="(agent, index) in agents" 
        :key="agent.id"
        class="cyber-card fade-in"
        :style="{ animationDelay: (index * 0.1) + 's' }"
        :class="agent.isOnline ? 'online-card' : 'offline-card'"
      >
        <div class="flex items-center justify-between mb-3">
          <span class="font-semibold text-white text-lg">{{ agent.name }}</span>
          <div class="flex items-center gap-2">
            <span 
              class="status-indicator"
              :class="agent.isOnline ? 'online' : 'offline'"
            ></span>
            <span 
              class="cyber-label"
              :class="agent.isOnline ? 'success' : ''"
            >
              {{ agent.isOnline ? '🟢 在线' : '⚪ 离线' }}
            </span>
          </div>
        </div>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between items-center data-flow">
            <span class="text-gray-400">ID:</span>
            <span class="text-cyber-blue">{{ agent.id }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-400">模型:</span>
            <span class="text-purple-400">{{ agent.model }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-400">最后活跃:</span>
            <span class="text-green-400">{{ formatLastActive(agent.lastActive) }}</span>
          </div>
        </div>
        
        <!-- 卡片底部装饰 -->
        <div class="card-decor"></div>
      </div>
    </div>
    
    <div v-if="!loading && agents.length > 0" class="mt-6 text-center">
      <button @click="loadAgentsStatus" class="cyber-button">
        🔄 刷新数据
      </button>
      <p class="text-xs text-gray-500 mt-3">自动刷新周期: 30秒</p>
    </div>
  </div>
</template>

<style scoped>
.agents-status {
  position: relative;
}

.online-card {
  border-color: rgba(0, 255, 136, 0.3);
}

.online-card:hover {
  border-color: var(--cyber-green);
  box-shadow: var(--glow-green), inset 0 0 20px rgba(0, 255, 136, 0.05);
}

.offline-card {
  border-color: rgba(102, 102, 102, 0.3);
  opacity: 0.7;
}

.offline-card:hover {
  border-color: rgba(255, 255, 255, 0.3);
  opacity: 1;
}

.text-cyber-blue {
  color: var(--cyber-blue);
}

.card-decor {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--cyber-blue), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.cyber-card:hover .card-decor {
  opacity: 1;
}
</style>
