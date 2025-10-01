<script setup>
import { computed } from 'vue'
import ReusableDataTable from '@/components/BaseCrudTable.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()


const customerHeaders = computed(() => [
  { text: t('customer_name'), value: 'name', sortable: true },
  { text: t('customer_email'), value: 'email', sortable: true },
  { text:t('customer_phone'), value: 'phone', sortable: true },
  { text: t('customer_address'), value: 'address', sortable: true },
])

// Handle custom events if needed

</script>

<template>
  <div>
    <ReusableDataTable
      endpoint="/customers"
      :headers="customerHeaders"
      resource-name="customer"
      edit-route-name="CustomerEdit"
      create-route="/customers/Create"
      search-placeholder="البحث..."
      empty-message="لا توجد بيانات متاحة"
      delete-confirmation-key="name"
    >
      <!-- Custom create button text if needed -->
      <template #create-button-text>
        {{ t('add_customer') }}
      </template>
    </ReusableDataTable>
  </div>
</template>