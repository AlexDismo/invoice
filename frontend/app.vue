<script setup lang="ts">
import { Document } from '@element-plus/icons-vue'
</script>

<template>
  <el-container class="layout">
    <el-header class="layout-header">
      <div class="layout-inner header-row">
        <NuxtLink to="/invoices" class="brand">
          <div class="brand-icon">
            <el-icon :size="18"><Document /></el-icon>
          </div>
          <span class="brand-text">Invoice Desk</span>
        </NuxtLink>
      </div>
    </el-header>
    <el-main class="layout-main">
      <div class="layout-inner">
        <NuxtPage />
      </div>
    </el-main>
  </el-container>
</template>

<style scoped>
.layout {
  min-height: 100vh;
  background: #f4f6fb;
}

.layout-header {
  height: 68px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid #e8edf5;
}

.layout-inner {
  max-width: 1080px;
  margin: 0 auto;
  padding: 0 24px;
}

.header-row {
  height: 68px;
  display: flex;
  align-items: center;
}

.layout-main {
  padding: 28px 0 48px;
}

.brand {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: inherit;
}

.brand-icon {
  width: 38px;
  height: 38px;
  border-radius: 11px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

.brand-text {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
}
</style>
