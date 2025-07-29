<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">
      {{ isEditMode ? 'تعديل مورد' : 'إضافة مورد' }}
    </h1>

    <SupplierForm :supplier-id="supplierId" />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import SupplierForm from '@/components/SupplierForm.vue'

const route = useRoute()
const supplierId = route.params.id
const isEditMode = !!supplierId
</script>
