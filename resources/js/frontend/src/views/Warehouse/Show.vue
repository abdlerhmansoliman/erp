<script setup>
import { onMounted,ref } from 'vue';
import api from '@/plugins/axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';


const { t } = useI18n();
const route = useRoute();
const router = useRouter();
const toast = useToast();

const warehouse = ref(null);
const loading = ref(false);

const fetchWarehouse=async()=>{
    loading.value=true;
    try {
        const { data }= await api.get(`/warehouses/${route.params.id}`);
        console.log(data);
        warehouse.value = data.data
    } catch (error) {
        toast.error('Error fetching warehouse data');
        router.push({ name: 'warehouses' });
    }finally{
        loading.value=false;
    }
}
    onMounted(fetchWarehouse);

</script>

<template>
  <div class="">
        <span class="font-semibold">{{ warehouse?.name }}</span>
    <h1 class="text-xl font-bold mb-4">{{ t('warehouse_details') }}</h1>

   
    <div class="flex flex-wrap gap-6 bg-gray-50 p-4 rounded-lg shadow  justify-center">


    <div class="flex m-6 pl-3 pr-3 flex-col">
        <span class="text-gray-500 text-sm">{{ t('address') }}</span>
        <span class="font-semibold">{{ warehouse?.address }}</span>
    </div>

    <div class="flex m-6 pl-3 pr-3 flex-col">
        <span class="text-gray-500 text-sm">{{ t('phone') }}</span>
        <span class="font-semibold">{{ warehouse?.phone }}</span>
    </div>

    <div class="flex m-6 pl-3 pr-3 flex-col">
        <span class="text-gray-500 text-sm">{{ t('email') }}</span>
        <span class="font-semibold">{{ warehouse?.email }}</span>
    </div>

    <div class="flex m-6 pl-3 pr-3 flex-col">
        <span class="text-gray-500 text-sm"> {{ t('product_count') }}</span>
        <span class="font-semibold">{{ warehouse?.product_count }}</span>
    </div>

    <div class="flex m-6 pl-3 pr-3 flex-col">
        <span class="text-gray-500 text-sm">{{ t('total_quantity') }}</span>
        <span class="font-semibold">{{ warehouse?.total_quantity }}</span>
    </div>
    </div>


  </div>
  </template>

