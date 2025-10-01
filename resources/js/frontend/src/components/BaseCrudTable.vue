<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import EasyDataTable from 'vue3-easy-data-table';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();
const toast = useToast();
const router = useRouter();
const { confirmDelete } = useConfirmDialog();

// Props
const props = defineProps({
  endpoint: { type: String, required: true },
  headers: { type: Array, required: true },
  resourceName: { type: String, required: true },
  editRouteName: { type: String, default: null },
  createRoute: { type: String, default: null },
  showRouteName: { type: String, default: null },
  searchPlaceholder: { type: String, default: 'البحث...' },
  customActions: { type: Array, default: () => [] },
  showSearch: { type: Boolean, default: true },
  showEdit: { type: Boolean, default: true },
  showDelete: { type: Boolean, default: true },
  showCreate: { type: Boolean, default: true },
  showView: { type: Boolean, default: false },
  showSelect: { type: Boolean, default: true },
  emptyMessage: { type: String, default: 'لا توجد بيانات متاحة' },
  deleteConfirmationKey: { type: String, default: 'name' }
});

// Emits
const emit = defineEmits(['item-selected', 'custom-action', 'selection-changed']);

// Reactive data
const items = ref([]);
const total = ref(0);
const loading = ref(false);
const currentPage = ref(1);
const rowsPerPage = ref(10);
const search = ref('');
const selectedIds = ref([]);

// Computed headers with selection and actions
const computedHeaders = computed(() => {
  const baseHeaders = [...props.headers];

  if (props.showSelect) {
    baseHeaders.unshift({ text: '', value: 'select', sortable: false, width: '50px' });
  }

  const hasActions = props.showEdit || props.showDelete || props.showView || props.customActions.length > 0;
  if (hasActions && !baseHeaders.find(h => h.value === 'controller')) {
    baseHeaders.push({ text: 'Actions', value: 'controller', sortable: false });
  }

  return baseHeaders;
});

// Fetch data
async function fetchData() {
  loading.value = true;
  try {
    const response = await api.get(props.endpoint, {
      params: { page: currentPage.value, perPage: rowsPerPage.value, search: search.value }
    });
    console.log(response.data);
    const data = response.data;
    if (data.data && Array.isArray(data.data)) {
      // إضافة category_name حسب اللغة
      items.value = data.data.map(product => ({
        ...product,
        category_name: locale.value === 'ar'
                       ? product.category?.name_ar
                       : product.category?.name_en
      }));

      total.value = data.total;
      selectedIds.value = selectedIds.value.filter(id => items.value.some(i => i.id === id));
      emit('selection-changed', selectedIds.value);
    } else {
      items.value = [];
      total.value = 0;
      selectedIds.value = [];
      emit('selection-changed', selectedIds.value);
    }
  } catch (error) {
    console.error('Fetch error:', error);
    toast.error(`Error loading ${props.resourceName}`);
    items.value = [];
    total.value = 0;
    selectedIds.value = [];
    emit('selection-changed', selectedIds.value);
  } finally {
    loading.value = false;
  }
}

// Watch language change
watch(locale, () => {
  items.value = items.value.map(product => ({
    ...product,
    category_name: locale.value === 'ar'
                   ? product.category?.name_ar
                   : product.category?.name_en
  }));
});

// Search
function onSearch() {
  currentPage.value = 1;
  fetchData();
}

// Delete single item
const deleteItem = async (item) => {
  if (!item || !item.id) return toast.error(`Error: Could not find ${props.resourceName}`);
  const confirmationText = item[props.deleteConfirmationKey] || `${props.resourceName} #${item.id}`;
  const confirmed = await confirmDelete(`${props.resourceName}: ${confirmationText}`);
  if (!confirmed) return;

  try {
    await api.delete(`${props.endpoint}/${item.id}`);
    toast.success(`The ${props.resourceName} deleted successfully!`);
    fetchData();
  } catch (error) {
    console.error('Delete error:', error);
    toast.error(`Error deleting ${props.resourceName}.`);
  }
};

// Batch delete
const deleteSelected = async () => {
  if (selectedIds.value.length === 0) return;
  const confirmed = await confirmDelete(`Are you sure you want to delete ${selectedIds.value.length} items?`);
  if (!confirmed) return;
  try {
    await api.post(`${props.endpoint}/delete-multiple`, { ids: selectedIds.value });
    toast.success(`${selectedIds.value.length} ${props.resourceName} deleted successfully!`);
    selectedIds.value = [];
    fetchData();
  } catch (error) {
    toast.error(`Error deleting ${props.resourceName}`);
  }
};

// Edit, Create, View
function goToEdit(item) { if (props.editRouteName) router.push({ name: props.editRouteName, params: { id: item.id } }); else emit('item-selected', { action: 'edit', item }); }
function goToCreate() { if (props.createRoute) router.push(props.createRoute); else emit('item-selected', { action: 'create' }); }
function goToView(item) { if (props.showRouteName) router.push({ name: props.showRouteName, params: { id: item.id } }); else emit('item-selected', { action: 'view', item }); }

// Custom action
function handleCustomAction(action, item) { emit('custom-action', { action, item }); }

// Select all
function toggleSelectAll(event) {
  selectedIds.value = event.target.checked ? items.value.map(i => i.id) : [];
  emit('selection-changed', selectedIds.value);
}

// Watch pagination
watch([currentPage, rowsPerPage], fetchData);

// Lifecycle
onMounted(fetchData);

// Expose
defineExpose({ fetchData, refresh: fetchData, selectedIds });

</script>

<template>
  <div class="p-4">
    <!-- Search & Create -->
    <div v-if="showSearch || showCreate" class="mb-4 flex items-center justify-between flex-wrap gap-2">
      <input
        v-if="showSearch"
        v-model="search"
        type="text"
        :placeholder="searchPlaceholder"
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
      />
      <div class="flex gap-2">
        <button
          v-if="showCreate"
          @click="goToCreate"
          class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
        >
          <slot name="create-button-text">Add {{ resourceName }}</slot>
        </button>
        <button
          v-if="showDelete"
          class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
          :disabled="selectedIds.length === 0"
          @click="deleteSelected"
        >
          {{ t('delete_selected') }}  ({{ selectedIds.length }})
        </button>
      </div>
    </div>

    <!-- Select All -->
    <div v-if="items.length > 0" class="mb-2">
      <input
        type="checkbox"
        :checked="selectedIds.length === items.length && items.length > 0"
        @change="toggleSelectAll"
        class="mr-2"
      />
      {{ t('select_all') }}
    </div>

    <!-- EasyDataTable -->
    <div class="overflow-x-auto">
      <EasyDataTable
        :headers="computedHeaders"
        :items="items"
        :loading="loading"
        :rows-per-page="rowsPerPage"
        :current-page="currentPage"
        :total-items="total"
        show-index
        @update:current-page="currentPage = $event"
        @update:rows-per-page="rowsPerPage = $event"
      >
        <!-- Selection Column -->
        <template v-if="showSelect" #item-select="row">
          <input type="checkbox" v-model="selectedIds" :value="row.id" class="w-4 h-4" />
        </template>

        <!-- Actions -->
        <template #item-controller="item">
          <slot name="actions" :item="item">
            <div class="flex gap-2">
              <button v-if="showView" @click="() => goToView(item)" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">View</button>
              <button v-if="showEdit" @click="() => goToEdit(item)" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
              <button v-if="showDelete" @click="() => deleteItem(item)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
              <button
                v-for="action in customActions"
                :key="action.name"
                @click="() => handleCustomAction(action.name, item)"
                :class="action.class || 'px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600'"
              >
                {{ action.label }}
              </button>
            </div>
          </slot>
        </template>

        <!-- Empty Message -->
        <template #empty-message>
          <div class="text-center p-4">{{ emptyMessage }}</div>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>
