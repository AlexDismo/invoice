<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { z } from 'zod'
import type { Invoice } from '~/types/invoice'
import type { ApiValidationError } from '~/types/invoice'
import { applyApiFieldErrors } from '~/utils/api-form'

const emit = defineEmits<{
  created: [invoice: Invoice]
}>()

const { create } = useInvoices()

const schema = toTypedSchema(
  z
    .object({
      number: z.string().trim().min(1, 'Number is required'),
      supplier_name: z.string().trim().min(1, 'Supplier name is required'),
      supplier_tax_id: z.string().trim().min(1, 'Tax ID is required'),
      net_amount: z.coerce.number().positive('Net amount must be greater than 0'),
      vat_amount: z.coerce.number().min(0, 'VAT must be zero or more'),
      currency: z.string().trim().length(3, 'Use 3-letter currency code'),
      issue_date: z.string().min(1, 'Issue date is required'),
      due_date: z.string().min(1, 'Due date is required'),
    })
    .superRefine((values, ctx) => {
      if (values.issue_date && values.due_date && values.due_date < values.issue_date) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: 'Due date must be on or after issue date',
          path: ['due_date'],
        })
      }
    }),
)

const { handleSubmit, isSubmitting, setFieldError, errors, defineField } = useForm({
  validationSchema: schema,
  initialValues: {
    number: '',
    supplier_name: '',
    supplier_tax_id: '',
    net_amount: 0,
    vat_amount: 0,
    currency: 'UAH',
    issue_date: '',
    due_date: '',
  },
})

const [number] = defineField('number')
const [supplierName] = defineField('supplier_name')
const [supplierTaxId] = defineField('supplier_tax_id')
const [netAmount] = defineField('net_amount')
const [vatAmount] = defineField('vat_amount')
const [currency] = defineField('currency')
const [issueDate] = defineField('issue_date')
const [dueDate] = defineField('due_date')

const grossAmount = computed(() => {
  const net = Number(netAmount.value) || 0
  const vat = Number(vatAmount.value) || 0

  return (net + vat).toFixed(2)
})

const submitError = ref<string | null>(null)

const onSubmit = handleSubmit(async (formValues) => {
  submitError.value = null

  try {
    const saved = await create({
      ...formValues,
      gross_amount: Number(grossAmount.value),
    })
    emit('created', saved)
  } catch (error: unknown) {
    const apiError = (error as { data?: ApiValidationError }).data

    if (applyApiFieldErrors(apiError, setFieldError)) {
      return
    }

    submitError.value = apiError?.message || 'Failed to create invoice'
  }
})
</script>

<template>
  <el-form label-position="top" @submit.prevent="onSubmit">
    <el-row :gutter="16">
      <el-col :span="12" :xs="24">
        <el-form-item label="Invoice number" :error="errors.number">
          <el-input v-model="number" placeholder="INV-2026-004" />
        </el-form-item>
      </el-col>
      <el-col :span="12" :xs="24">
        <el-form-item label="Currency" :error="errors.currency">
          <el-input v-model="currency" maxlength="3" placeholder="UAH" />
        </el-form-item>
      </el-col>
    </el-row>

    <el-row :gutter="16">
      <el-col :span="12" :xs="24">
        <el-form-item label="Supplier name" :error="errors.supplier_name">
          <el-input v-model="supplierName" placeholder="Company name" />
        </el-form-item>
      </el-col>
      <el-col :span="12" :xs="24">
        <el-form-item label="Supplier tax ID" :error="errors.supplier_tax_id">
          <el-input v-model="supplierTaxId" placeholder="1234567890" />
        </el-form-item>
      </el-col>
    </el-row>

    <el-row :gutter="16">
      <el-col :span="8" :xs="24">
        <el-form-item label="Net amount" :error="errors.net_amount">
          <el-input-number
            v-model="netAmount"
            :min="0"
            :step="0.01"
            :precision="2"
            controls-position="right"
            style="width: 100%"
          />
        </el-form-item>
      </el-col>
      <el-col :span="8" :xs="24">
        <el-form-item label="VAT amount" :error="errors.vat_amount">
          <el-input-number
            v-model="vatAmount"
            :min="0"
            :step="0.01"
            :precision="2"
            controls-position="right"
            style="width: 100%"
          />
        </el-form-item>
      </el-col>
      <el-col :span="8" :xs="24">
        <el-form-item label="Gross amount">
          <el-input-number
            :model-value="Number(grossAmount)"
            :precision="2"
            disabled
            controls-position="right"
            style="width: 100%"
          />
        </el-form-item>
      </el-col>
    </el-row>

    <el-row :gutter="16">
      <el-col :span="12" :xs="24">
        <el-form-item label="Issue date" :error="errors.issue_date">
          <el-date-picker
            v-model="issueDate"
            type="date"
            value-format="YYYY-MM-DD"
            placeholder="Select issue date"
            style="width: 100%"
          />
        </el-form-item>
      </el-col>
      <el-col :span="12" :xs="24">
        <el-form-item label="Due date" :error="errors.due_date">
          <el-date-picker
            v-model="dueDate"
            type="date"
            value-format="YYYY-MM-DD"
            placeholder="Select due date"
            style="width: 100%"
          />
        </el-form-item>
      </el-col>
    </el-row>

    <el-alert v-if="submitError" :title="submitError" type="error" show-icon :closable="false" style="margin-bottom: 16px" />

    <el-form-item>
      <el-button type="primary" native-type="submit" :loading="isSubmitting">
        Create invoice
      </el-button>
    </el-form-item>
  </el-form>
</template>
