<script setup>
import BaseCrudTable from '@/components/Table.vue'
import { useRouter } from 'vue-router'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
const { t, locale } = useI18n()

const { confirmDelete } = useConfirmDialog()
const toast = useToast()
const router = useRouter()

const headers = computed(() => {
  // This makes the computed re-run when locale changes
  locale.value

  return [
    { text: t('name'), value: 'name', sortable: true },
    { text: t('email'), value: 'email', sortable: true },
    { text: t('phone'), value: 'phone', sortable: true },
    { text: t('address'), value: 'address', sortable: true },
    { text: t('action'), value: 'controller', sortable: false },
  ]
})
async function deleteSupplier(item) {
  if (!item?.id) return
  const confirmed = await confirmDelete(`Supplier: ${item.name}`)
  if (!confirmed) return
  await api.delete(`/suppliers/${item.id}`)
  toast.success(t('deleted successfully'))
}
</script>

<template>
  <BaseCrudTable
    :headers="headers"
    endpoint="/suppliers"
  >
    <template #add-button>
      <router-link
        to="/suppliers/create"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
      >
        {{ t('add') }}
      </router-link>
    </template>

    <template #actions="{ item }">
      <div class="flex gap-2">
        <button @click="$router.push({ name: 'SupplierEdit', params: { id: item.id } })" class="bg-blue-500 px-3 py-1 text-white rounded">
          {{ t('edit') }}
        </button>
        <button @click="deleteSupplier(item)" class="bg-red-500 px-3 py-1 text-white rounded">
          {{ t('delete') }}
        </button>
      </div>
    </template>
  </BaseCrudTable>
</template>
