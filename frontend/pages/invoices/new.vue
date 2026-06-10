<script setup lang="ts">
import { ElMessage } from 'element-plus'
import type { Invoice } from '~/types/invoice'

function onCreated(invoice: Invoice) {
  ElMessage.success('Invoice created')
  navigateTo(`/invoices/${invoice.id}`)
}
</script>

<template>
  <div class="page-block">
    <el-button text @click="navigateTo('/invoices')">← Back to list</el-button>

    <div class="page-heading">
      <h1 class="page-title">New invoice</h1>
      <p class="page-subtitle">Create a supplier invoice with validated amounts and dates</p>
    </div>

    <el-card header="Invoice details">
      <InvoiceCreateForm @created="onCreated" />
    </el-card>
  </div>
</template>

<style scoped>
.page-block {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.page-heading {
  margin-bottom: 4px;
}
</style>
