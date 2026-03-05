<script setup lang="ts">
import AgentsStatus from './components/AgentsStatus.vue'
import GitHubPanel from './components/GitHubPanel.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">
          📊 Agent 监控系统
        </h1>
      </div>
    </header>
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0 space-y-6">
        <AgentsStatus />
        <GitHubPanel />
      </div>
    </main>
  </div>
</template>
