<script setup lang="ts">
import type { InvoiceStatus } from '~/types/invoice'
import { statusLabels, statusTagTypes } from '~/utils/status'

defineProps<{
  status: InvoiceStatus
}>()
</script>

<template>
  <el-tag :type="statusTagTypes[status]" effect="light" round>
    {{ statusLabels[status] }}
  </el-tag>
</template>
