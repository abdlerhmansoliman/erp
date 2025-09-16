<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/plugins/axios';
import WarehouseForm from '@/components/Forms/WarehouseForm.vue';
import { useToast } from 'vue-toastification';

const route = useRoute();
const toast = useToast();
const warehouse = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await api.get(`/warehouses/${route.params.id}/edit`);    
    // Extract warehouse from the data wrapper
    warehouse.value = response.data.data;
    
  } catch (error) {
    
    if (error.response?.status === 404) {
      toast.error('المخزن غير موجود');
    } else {
      toast.error('فشل في تحميل بيانات المخزن');
    }
    
    // Redirect to warehouses list on error
    router.push({ name: 'warehouses' });
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div>
    <div v-if="loading" class="text-center p-8">
      جاري التحميل...
    </div>
    
    <WarehouseForm 
      v-else-if="warehouse"
      mode="edit" 
      :warehouse="warehouse" 
    />

  </div>
</template>