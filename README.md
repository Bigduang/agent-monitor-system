# Agent 监控系统

## 项目目标

监控 3 个 OpenClaw Agent（前端工程师、后端工程师、总工程师）的工作状态，通过 GitHub 提交代码、Issue 和 PR，前端展示工作进度面板。

### 核心功能
- 实时展示 **3 个 Agent** 的工作状态
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
- LNMP 架构 (Linux + Nginx + MySQL + PHP)
- 无需 Docker

### 任务管理
- GitHub Issue (纯 GitHub 管理，无需本地数据库)

### 认证
- 内网项目，无需登录注册，直接访问 Dashboard

---

## 目录

- `frontend/` - 前端项目
- `backend/` - 后端项目
