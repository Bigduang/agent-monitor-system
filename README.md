# Agent 监控系统

## 项目目标

监控 3 个 OpenClaw Agent（前端工程师、后端工程师、总工程师）的工作状态，通过 GitHub 提交代码、Issue 和 PR，前端展示工作进度面板。

### 核心功能
- 实时展示 **3 个 Agent** 的工作状态（通过 OpenClaw API 获取）
- 实时展示各 Agent 工作进度
- GitHub Issue/PR 状态同步
- 项目整体进度看板
- 内网可直接访问

---

## 技术选型

### 前端
- Vue 3 + TypeScript
- Vite (构建工具)
- pnpm (包管理器)
- TailwindCSS (UI 框架)

### 后端
- PHP 8 + Laravel 11

### 部署
- LNMP 架构 (Linux + Nginx + PHP)
- 无需 MySQL、无需 Docker

### 认证
- 内网项目，无需登录注册，直接访问 Dashboard

---

## 后端 API 设计

### Agent 状态获取
- 通过 OpenClaw `sessions_list` API 获取 Agent 会话信息
- 从 `updatedAt` 字段判断最后活跃时间（如 5 分钟内 = 在线）
- 从 `model` 字段获取当前使用模型

```php
// 获取 Agent 状态
GET /api/agents/status

// 返回示例
{
  "agents": [
    {
      "id": "frontend001",
      "name": "宫下玲奈",
      "model": "MiniMax-M2.5",
      "lastActive": 1772629637351,
      "isOnline": true
    },
    {
      "id": "backend001", 
      "name": "田中柠檬",
      "model": "MiniMax-M2.5",
      "lastActive": 1772629600000,
      "isOnline": true
    }
  ]
}
```

### GitHub 集成
- 通过 GitHub Issue 管理任务（无需本地数据库）
- 通过 GitHub 提交/PR 记录获取工作 activity

---

## 目录

```
agent-monitor-system/
├── frontend/     # 前端项目 (Vue 3)
└── backend/      # 后端项目 (PHP 8 + Laravel 11)
```
