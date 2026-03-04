# Agent 监控系统开发步骤

## 第一阶段：环境搭建

### 步骤 1：初始化前后端项目

- [ ] 初始化前端项目 `npm create vite@latest frontend --template vue-ts`
- [ ] 初始化后端项目 `composer create-project laravel/laravel backend`
- [ ] 配置 pnpm
- [ ] 安装 TailwindCSS
- [ ] 配置 Git 仓库

---

## 第二阶段：后端开发

### 步骤 2：后端基础配置

- [ ] 配置路由
- [ ] 配置跨域
- [ ] 配置 GitHub API 调用

### 步骤 3：Agent 状态 API

- [ ] 实现 `GET /api/agents/status`
- [ ] 调用 OpenClaw sessions_list API
- [ ] 处理 updatedAt 判断在线状态

### 步骤 4：GitHub 集成 API

- [ ] 实现获取 Issue 列表
- [ ] 实现获取 PR 列表
- [ ] 实现获取提交记录

### 步骤 5：前端 API 对接

- [ ] 开发前端调用后端 API
- [ ] 实现定时刷新

---

## 第三阶段：前端开发

### 步骤 6：基础页面

- [ ] 创建 Dashboard 页面
- [ ] 创建 Agent 状态卡片组件
- [ ] 创建任务看板组件

### 步骤 7：数据展示

- [ ] 展示 Agent 在线状态
- [ ] 展示 GitHub Issue 进度
- [ ] 展示 PR 状态

### 步骤 8：美化 UI

- [ ] 配置 TailwindCSS 样式
- [ ] 响应式布局

---

## 第四阶段：部署

### 步骤 9：部署配置

- [ ] 配置 Nginx
- [ ] 配置 PHP-FPM
- [ ] 测试访问

---

## 开发顺序

```
后端先完成 API → 前端对接展示 → 最后部署
```
