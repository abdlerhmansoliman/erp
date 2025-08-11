<script setup>
import api from '@/plugins/axios'
import { ref, watch, onMounted, computed } from 'vue'
import EasyDataTable from 'vue3-easy-data-table'
import { useRouter } from 'vue-router'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  fields: { type: Array, required: true },
  endpoint: { type: String, required: true },
  itemLabel: { type: String, required: true },
  editRoute: { type: String, required: true },
  createRoute: { type: String, required: true }
})

const { t, locale } = useI18n()
const { confirmDelete } = useConfirmDialog()
const toast = useToast()
const router = useRouter()

// Column visibility state
const visibleColumns = ref(props.fields.map(f => f.value))

const headers = computed(() => {
  locale.value
  return props.fields
    .filter(f => visibleColumns.value.includes(f.value))
    .map(f => ({
      text: t(f.label || f.value),
      value: f.value,
      sortable: f.sortable ?? true
    }))
})

const items = ref([])
const total = ref(0)
const loading = ref(false)
const currentPage = ref(1)
const rowsPerPage = ref(10)
const search = ref('')
const selectedItems = ref([]) // For bulk delete

async function fetchData() {
  loading.value = true
  try {
    const response = await api.get(props.endpoint, {
      params: {
        page: currentPage.value,
        perPage: rowsPerPage.value,
        search: search.value
      }
    })
    items.value = response.data.data || []
    total.value = response.data.total || 0
  } catch {
    items.value = []
    total.value = 0
  } finally {
    loading.value = false
  }
}

function onSearch() {
  currentPage.value = 1
  fetchData()
}

async function deleteItem(item) {
  if (!item?.id) {
    toast.error(t('error.noId'))
    return
  }
  const confirmed = await confirmDelete(`${t(props.itemLabel)}: ${item.name}`)
  if (!confirmed) return
  try {
    await api.delete(`${props.endpoint}/${item.id}`)
    toast.success(t('deleted successfully'))
    fetchData()
  } catch {
    toast.error(t('error.deleteFailed'))
  }
}

async function bulkDelete() {
  if (selectedItems.value.length === 0) {
    toast.error(t('error.noSelection'))
    return
  }
  const confirmed = await confirmDelete(t('deleteSelected'))
  if (!confirmed) return
  try {
    await Promise.all(
      selectedItems.value.map(id => api.delete(`${props.endpoint}/${id}`))
    )
    toast.success(t('deleted successfully'))
    selectedItems.value = []
    fetchData()
  } catch {
    toast.error(t('error.deleteFailed'))
  }
}

function goToEdit(item) {
  router.push({ name: props.editRoute, params: { id: item.id } })
}

// ✅ Expose for parent use
defineExpose({
  deleteItem,
  bulkDelete,
  goToEdit,
  fetchData
})

watch([currentPage, rowsPerPage], fetchData)
onMounted(fetchData)
</script>

<template>
  <div>
    <!-- Top Bar -->
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
      <!-- Search -->
      <input
        v-model="search"
        type="text"
        :placeholder="t('search')"
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
      />

      <div class="flex items-center gap-2">
        <!-- Column toggle -->
        <select
          multiple
          v-model="visibleColumns"
          class="border px-2 py-1 rounded"
        >
          <option
            v-for="field in props.fields"
            :key="field.value"
            :value="field.value"
          >
            {{ t(field.label || field.value) }}
          </option>
        </select>

        <!-- Bulk delete -->
        <button
          class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
          @click="bulkDelete"
        >
          {{ t('deleteSelected') }}
        </button>

        <!-- Add new -->
        <router-link
          :to="props.createRoute"
          class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
        >
          {{ t('add') }}
        </router-link>
      </div>
    </div>

    <!-- Table -->
    <EasyDataTable
      :headers="headers"
      :items="items"
      :loading="loading"
      :rows-per-page="rowsPerPage"
      :current-page="currentPage"
      :total-items="total"
      show-index
      show-checkbox
      v-model:checked-rows="selectedItems"
      @update:current-page="currentPage = $event"
      @update:rows-per-page="rowsPerPage = $event"
    >
      <!-- Forward slots -->
      <template v-for="field in props.fields" v-slot:[`item-${field.value}`]="slotProps">
        <slot :name="`item-${field.value}`" v-bind="slotProps">
          {{ slotProps.item[field.value] }}
        </slot>
      </template>

      <template #empty-message>
        <div class="text-center p-4">
          {{ t('there is no data') }}
        </div>
      </template>
    </EasyDataTable>
  </div>
</template>
