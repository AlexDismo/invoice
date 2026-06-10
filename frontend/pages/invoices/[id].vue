<script setup lang="ts">
import type { Invoice } from '~/types/invoice'
import { ElMessage, ElMessageBox } from 'element-plus'
import { formatDate, formatDateTime, formatMoney } from '~/utils/format'

const route = useRoute()
const { get, changeStatus } = useInvoices()

const invoice = ref<Invoice | null>(null)
const loadError = ref<string | null>(null)
const pending = ref(true)
const statusChanging = ref(false)

async function loadInvoice() {
  pending.value = true
  loadError.value = null
  try {
    invoice.value = await get(String(route.params.id))
  } catch {
    loadError.value = 'Invoice not found or failed to load.'
    invoice.value = null
  } finally {
    pending.value = false
  }
}

await loadInvoice()

function onSaved(updated: Invoice) {
  invoice.value = updated
  ElMessage.success('Invoice updated')
}

async function onApprove() {
  await ElMessageBox.confirm('Approve this invoice?', 'Confirm', {
    confirmButtonText: 'Approve',
    cancelButtonText: 'Cancel',
    type: 'success',
  })

  statusChanging.value = true
  try {
    invoice.value = await changeStatus(invoice.value!.id, { status: 'approved' })
    ElMessage.success('Invoice approved')
  } catch {
    ElMessage.error('Failed to change status')
  } finally {
    statusChanging.value = false
  }
}

async function onReject() {
  await ElMessageBox.confirm('Reject this invoice?', 'Confirm', {
    confirmButtonText: 'Reject',
    cancelButtonText: 'Cancel',
    type: 'warning',
  })

  statusChanging.value = true
  try {
    invoice.value = await changeStatus(invoice.value!.id, { status: 'rejected' })
    ElMessage.warning('Invoice rejected')
  } catch {
    ElMessage.error('Failed to change status')
  } finally {
    statusChanging.value = false
  }
}
</script>

<template>
  <div class="page-block">
    <el-button text @click="navigateTo('/invoices')">← Back to list</el-button>

    <div v-loading="pending" class="detail-wrap">
      <el-result
        v-if="!pending && loadError"
        icon="warning"
        title="Invoice unavailable"
        :sub-title="loadError"
      />

      <template v-else-if="invoice">
        <div class="detail-header">
          <div>
            <h1 class="page-title">{{ invoice.number }}</h1>
            <p class="page-subtitle">{{ invoice.supplier_name }}</p>
          </div>
          <ClientOnly>
            <div class="header-right">
              <InvoiceStatusBadge :status="invoice.status" />
              <template v-if="invoice.status === 'pending'">
                <el-button
                  type="success"
                  size="small"
                  :loading="statusChanging"
                  @click="onApprove"
                >
                  Approve
                </el-button>
                <el-button
                  type="danger"
                  size="small"
                  :loading="statusChanging"
                  @click="onReject"
                >
                  Reject
                </el-button>
              </template>
            </div>
          </ClientOnly>
        </div>

        <el-row :gutter="20">
          <el-col :xs="24" :md="12">
            <el-card header="Details">
              <el-descriptions :column="1" border size="small">
                <el-descriptions-item label="Supplier tax ID">
                  {{ invoice.supplier_tax_id }}
                </el-descriptions-item>
                <el-descriptions-item label="Net amount">
                  {{ formatMoney(invoice.net_amount, invoice.currency) }}
                </el-descriptions-item>
                <el-descriptions-item label="VAT amount">
                  {{ formatMoney(invoice.vat_amount, invoice.currency) }}
                </el-descriptions-item>
                <el-descriptions-item label="Gross amount">
                  <strong>{{ formatMoney(invoice.gross_amount, invoice.currency) }}</strong>
                </el-descriptions-item>
                <el-descriptions-item label="Issue date">
                  {{ formatDate(invoice.issue_date) }}
                </el-descriptions-item>
                <el-descriptions-item label="Due date">
                  {{ formatDate(invoice.due_date) }}
                </el-descriptions-item>
                <el-descriptions-item label="Last updated">
                  {{ formatDateTime(invoice.updated_at) }}
                </el-descriptions-item>
              </el-descriptions>
            </el-card>
          </el-col>

          <el-col :xs="24" :md="12">
            <el-card header="Edit invoice">
              <el-alert
                v-if="invoice.status !== 'pending'"
                title="Only pending invoices can be edited."
                type="info"
                :closable="false"
                show-icon
                style="margin-bottom: 16px"
              />
              <InvoiceEditForm
                :invoice="invoice"
                :disabled="invoice.status !== 'pending'"
                @saved="onSaved"
              />
            </el-card>
          </el-col>
        </el-row>
      </template>
    </div>
  </div>
</template>

<style scoped>
.page-block {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.detail-wrap {
  min-height: 120px;
}

.detail-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

@media (max-width: 768px) {
  .el-col + .el-col {
    margin-top: 20px;
  }
}
</style>
