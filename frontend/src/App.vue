<script setup lang="ts">
import { onMounted, ref } from 'vue'
import AgentsStatus from './components/AgentsStatus.vue'
import GitHubPanel from './components/GitHubPanel.vue'
import './cyber-style.css'

// 粒子系统
const particles = ref<{ id: number; left: string; delay: string; duration: string }[]>([])

function generateParticles() {
  const particleCount = 30
  const newParticles = []
  for (let i = 0; i < particleCount; i++) {
    newParticles.push({
      id: i,
      left: Math.random() * 100 + '%',
      delay: Math.random() * 15 + 's',
      duration: (15 + Math.random() * 10) + 's'
    })
  }
  particles.value = newParticles
}

onMounted(() => {
  generateParticles()
})
</script>

<template>
  <div class="app-container">
    <!-- 动态网格背景 -->
    <div class="cyber-grid-bg"></div>
    
    <!-- 粒子效果 -->
    <div class="particles-container">
      <div 
        v-for="particle in particles" 
        :key="particle.id"
        class="particle"
        :style="{
          left: particle.left,
          animationDelay: particle.delay,
          animationDuration: particle.duration
        }"
      ></div>
    </div>
    
    <!-- 主内容 -->
    <div class="content-wrapper">
      <header class="cyber-header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <h1 class="cyber-title">
            <span class="text-glow-blue">Agent</span> 监控系统
          </h1>
          <p class="header-subtitle">实时监控 • 智能分析 • 赛博朋克</p>
        </div>
      </header>
      
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <AgentsStatus />
          <div class="mt-8">
            <GitHubPanel />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  position: relative;
}

.content-wrapper {
  position: relative;
  z-index: 1;
}

.cyber-header {
  background: rgba(18, 18, 26, 0.8);
  border-bottom: 1px solid rgba(0, 212, 255, 0.2);
  backdrop-filter: blur(10px);
  position: relative;
  overflow: hidden;
}

.cyber-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--cyber-blue), var(--cyber-purple), var(--cyber-pink), transparent);
  animation: headerLine 3s linear infinite;
}

@keyframes headerLine {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.cyber-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.3), transparent);
}

.header-subtitle {
  margin-top: 8px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  letter-spacing: 3px;
  text-transform: uppercase;
}
</style>
