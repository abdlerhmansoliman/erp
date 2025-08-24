<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import EasyDataTable from 'vue3-easy-data-table';
import { useRouter } from 'vue-router';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { useToast } from 'vue-toastification';

const { confirmDelete } = useConfirmDialog();
const toast = useToast();
const router = useRouter();

// Props
const props = defineProps({
  endpoint: {
    type: String,
    required: true
  },
  
  // Table headers configuration
  headers: {
    type: Array,
    required: true
  },
  
  // Resource name for messages and routing
  resourceName: {
    type: String,
    required: true
  },
  
  // Route names for navigation
  editRouteName: {
    type: String,
    default: null
  },
  
  createRoute: {
    type: String,
    default: null
  },
  

  
  // Search placeholder
  searchPlaceholder: {
    type: String,
    default: 'البحث...'
  },
  
  // Custom action buttons
  customActions: {
    type: Array,
    default: () => []
  },
  
  // Show/hide default actions
  showEdit: {
    type: Boolean,
    default: true
  },
  
  showDelete: {
    type: Boolean,
    default: true
  },
  
  showCreate: {
    type: Boolean,
    default: true
  },
  
  // Empty message
  emptyMessage: {
    type: String,
    default: 'لا توجد بيانات متاحة'
  },
  
  // Delete confirmation message key (will use item[this_key] in confirmation)
  deleteConfirmationKey: {
    type: String,
    default: 'name'
  }
});

// Emits
const emit = defineEmits(['item-selected', 'custom-action']);

// Reactive data
const items = ref([]);
const total = ref(0);
const loading = ref(false);
const currentPage = ref(1);
const rowsPerPage = ref(10);
const search = ref('');

// Computed headers with actions
const computedHeaders = computed(() => {
  const baseHeaders = [...props.headers];
  
  // Add actions column if we have any actions
  const hasActions = props.showEdit || props.showDelete || props.customActions.length > 0;
  
  if (hasActions) {
    const actionsHeader = baseHeaders.find(h => h.value === 'controller');
    if (!actionsHeader) {
      baseHeaders.push({ text: 'Actions', value: 'controller', sortable: false });
    }
  }
  
  return baseHeaders;
});



// Fetch data function
async function fetchData() {
  loading.value = true;
  
  try {
    const response = await api.get(props.endpoint, {
      params: {
        page: currentPage.value,
        perPage: rowsPerPage.value,
        search: search.value,
      }
    });

    const data = response.data;
    
    if (data.data && Array.isArray(data.data)) {
      items.value = data.data;
      total.value = data.total;
    } else {
      items.value = [];
      total.value = 0;
    }
  } catch (error) {
    console.error('Fetch error:', error);
    
    if (error.response?.status === 403) {
      toast.error(`Access denied for ${props.resourceName}`);
    } else {
      toast.error(`Error loading ${props.resourceName}`);
    }
    
    items.value = [];
    total.value = 0;
  } finally {
    loading.value = false;
  }
}

// Search function
function onSearch() {
  currentPage.value = 1;
  fetchData();
}

// Delete function
const deleteItem = async (item) => {
  if (!item || !item.id) {
    toast.error(`Error: Could not find ${props.resourceName}`);
    return;
  }

  const confirmationText = item[props.deleteConfirmationKey] || `${props.resourceName} #${item.id}`;
  const confirmed = await confirmDelete(`${props.resourceName}: ${confirmationText}`);
  if (!confirmed) return;

  try {
    await api.delete(`${props.endpoint}/${item.id}`);
    toast.success(`The ${props.resourceName} deleted successfully!`);
    fetchData();
  } catch (error) {
    console.error('Delete error:', error);
    
    if (error.response?.status === 403) {
      toast.error(`Access denied for deleting this ${props.resourceName}`);
    } else {
      toast.error(`Error deleting ${props.resourceName}.`);
    }
  }
};

// Edit function
function goToEdit(item) {
  if (props.editRouteName) {
    router.push({ name: props.editRouteName, params: { id: item.id } });
  } else {
    emit('item-selected', { action: 'edit', item });
  }
}

// Create function
function goToCreate() {
  if (props.createRoute) {
    router.push(props.createRoute);
  } else {
    emit('item-selected', { action: 'create' });
  }
}

// Custom action handler
function handleCustomAction(action, item) {
  emit('custom-action', { action, item });
}

// Watchers
watch([currentPage, rowsPerPage], () => {
  fetchData();
});

// Lifecycle
onMounted(() => {
  fetchData();
});

// Expose methods for parent component
defineExpose({
  fetchData,
  refresh: fetchData
});
</script>

<template>
  <div class="p-4">
    <!-- Header section with search and create button -->
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
      <input
        v-model="search"
        type="text"
        :placeholder="searchPlaceholder"
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
      />

      <button
        v-if="showCreate"
        @click="goToCreate"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-center"
      >
        <slot name="create-button-text">Add {{ resourceName }}</slot>
      </button>
    </div>
    
    <!-- Easy Data Table -->
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
      <!-- Actions column -->
      <template #item-controller="item">
        <div class="flex gap-2">
          <!-- Edit button -->
          <button
            v-if="showEdit"
            @click="() => goToEdit(item)"
            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Edit
          </button>
          
          <!-- Delete button -->
          <button
            v-if="showDelete"
            @click="() => deleteItem(item)"
            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
          >
            Delete
          </button>
          
          <!-- Custom actions -->
          <button
            v-for="action in customActions"
            :key="action.name"
            @click="() => handleCustomAction(action.name, item)"
            :class="action.class || 'px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600'"
          >
            {{ action.label }}
          </button>
        </div>
      </template>

      <!-- Custom column templates -->
      <template
        v-for="(_, slot) of $slots"
        v-slot:[slot]="slotProps"
      >
        <slot :name="slot" v-bind="slotProps" />
      </template>

      <template #empty-message>
        <div class="text-center p-4">
          {{ emptyMessage }}
        </div>
      </template>
    </EasyDataTable>
  </div>
</template>

<style>
</style>