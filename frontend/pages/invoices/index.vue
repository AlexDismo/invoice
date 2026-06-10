<script setup lang="ts">
import { Plus, Refresh } from '@element-plus/icons-vue'
import type { Invoice } from '~/types/invoice'
import { formatDate, formatMoney } from '~/utils/format'

const { list } = useInvoices()

const { data: invoices, error, pending, refresh } = await useAsyncData<Invoice[]>('invoices', () => list())

function openInvoice(row: Invoice) {
  navigateTo(`/invoices/${row.id}`)
}
</script>

<template>
  <div class="page-block">
    <div class="page-header">
      <div class="page-heading">
        <h1 class="page-title">Invoices</h1>
        <p class="page-subtitle">Review supplier documents and payment deadlines</p>
      </div>
      <div class="page-actions">
        <el-button :icon="Refresh" @click="refresh()">Refresh</el-button>
        <el-button type="primary" :icon="Plus" @click="navigateTo('/invoices/new')">
          New invoice
        </el-button>
      </div>
    </div>

    <el-card v-loading="pending">
      <el-result
        v-if="error"
        icon="error"
        title="Failed to load invoices"
        sub-title="Please try again later."
      >
        <template #extra>
          <el-button type="primary" @click="refresh()">Try again</el-button>
        </template>
      </el-result>

      <el-empty v-else-if="!pending && !invoices?.length" description="No invoices yet">
        <el-button type="primary" :icon="Plus" @click="navigateTo('/invoices/new')">
          Create first invoice
        </el-button>
      </el-empty>

      <el-table
        v-else-if="invoices?.length"
        :data="invoices"
        stripe
        highlight-current-row
        style="width: 100%"
        @row-click="openInvoice"
      >
        <el-table-column prop="number" label="Number" min-width="140">
          <template #default="{ row }">
            <span class="cell-strong">{{ row.number }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="supplier_name" label="Supplier" min-width="220" show-overflow-tooltip />
        <el-table-column label="Gross" min-width="140">
          <template #default="{ row }">
            {{ formatMoney(row.gross_amount, row.currency) }}
          </template>
        </el-table-column>
        <el-table-column label="Status" min-width="120">
          <template #default="{ row }">
            <InvoiceStatusBadge :status="row.status" />
          </template>
        </el-table-column>
        <el-table-column label="Due date" min-width="160">
          <template #default="{ row }">
            {{ formatDate(row.due_date) }}
          </template>
        </el-table-column>
        <el-table-column label="" width="100" fixed="right">
          <template #default="{ row }">
            <el-button link type="primary" @click.stop="openInvoice(row)">View</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<style scoped>
.page-block {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.page-heading {
  margin-bottom: 4px;
}

.page-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.cell-strong {
  font-weight: 600;
  color: #0f172a;
}

:deep(.el-table__row) {
  cursor: pointer;
}
</style>
