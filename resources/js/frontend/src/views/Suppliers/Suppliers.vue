<script setup>
import ReusableDataTable from '@/components/BaseCrudTable.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Define headers for suppliers
const supplierHeaders = computed(() => [
  { text: t('name'), value: 'name', sortable: true },
  { text: t('email'), value: 'email', sortable: true },
  { text: t('phone'), value: 'phone', sortable: true },
  { text: t('address'), value: 'address', sortable: true },
]
)
// Handle custom events if needed

</script>

<template>
  <div>
    <ReusableDataTable
      endpoint="/suppliers"
      :headers="supplierHeaders"
      resource-name="supplier"
      edit-route-name="SupplierEdit"
      create-route="/suppliers/Create"
      search-placeholder="البحث..."
      empty-message="لا توجد بيانات متاحة"
      delete-confirmation-key="name"

    >
      <!-- Custom create button text if needed -->
      <template #create-button-text>
        Add Supplier
      </template>
    </ReusableDataTable>
  </div>
</template>