<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { z } from 'zod'
import type { Invoice } from '~/types/invoice'
import type { ApiValidationError } from '~/types/invoice'
import { applyApiFieldErrors } from '~/utils/api-form'

const props = defineProps<{
  invoice: Invoice
  disabled: boolean
}>()

const emit = defineEmits<{
  saved: [invoice: Invoice]
}>()

const { update } = useInvoices()

const schema = toTypedSchema(
  z
    .object({
      net_amount: z.coerce.number().positive('Net amount must be greater than 0'),
      vat_amount: z.coerce.number().min(0, 'VAT must be zero or more'),
      due_date: z.string().min(1, 'Due date is required'),
    })
    .superRefine((values, ctx) => {
      if (values.due_date < props.invoice.issue_date) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: 'Due date must be on or after issue date',
          path: ['due_date'],
        })
      }
    }),
)

const { handleSubmit, isSubmitting, setFieldError, resetForm, errors, defineField } = useForm({
  validationSchema: schema,
  initialValues: {
    net_amount: Number(props.invoice.net_amount),
    vat_amount: Number(props.invoice.vat_amount),
    due_date: props.invoice.due_date,
  },
})

const [netAmount] = defineField('net_amount')
const [vatAmount] = defineField('vat_amount')
const [dueDate] = defineField('due_date')

watch(
  () => props.invoice,
  (invoice) => {
    resetForm({
      values: {
        net_amount: Number(invoice.net_amount),
        vat_amount: Number(invoice.vat_amount),
        due_date: invoice.due_date,
      },
    })
  },
)

const grossAmount = computed(() => {
  const net = Number(netAmount.value) || 0
  const vat = Number(vatAmount.value) || 0

  return (net + vat).toFixed(2)
})

const submitError = ref<string | null>(null)

const onSubmit = handleSubmit(async (formValues) => {
  submitError.value = null

  try {
    const saved = await update(props.invoice.id, formValues)
    emit('saved', saved)
  } catch (error: unknown) {
    const apiError = (error as { data?: ApiValidationError }).data

    if (applyApiFieldErrors(apiError, setFieldError)) {
      return
    }

    submitError.value = apiError?.message || 'Failed to save invoice'
  }
})
</script>

<template>
  <el-form label-position="top" @submit.prevent="onSubmit">
    <fieldset :disabled="disabled || isSubmitting" class="form-fieldset">
      <el-row :gutter="16">
        <el-col :span="12" :xs="24">
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
        <el-col :span="12" :xs="24">
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
      </el-row>

      <el-form-item label="Gross amount">
        <el-input-number
          :model-value="Number(grossAmount)"
          :precision="2"
          disabled
          controls-position="right"
          style="width: 100%"
        />
      </el-form-item>

      <el-form-item label="Due date" :error="errors.due_date">
        <el-date-picker
          v-model="dueDate"
          type="date"
          value-format="YYYY-MM-DD"
          placeholder="Select due date"
          style="width: 100%"
        />
      </el-form-item>

      <el-alert
        v-if="submitError"
        :title="submitError"
        type="error"
        show-icon
        :closable="false"
        style="margin-bottom: 16px"
      />

      <el-form-item>
        <el-button type="primary" native-type="submit" :loading="isSubmitting" :disabled="disabled">
          Save changes
        </el-button>
      </el-form-item>
    </fieldset>
  </el-form>
</template>

<style scoped>
.form-fieldset {
  border: 0;
  margin: 0;
  padding: 0;
}

.form-fieldset:disabled {
  opacity: 0.65;
}
</style>
