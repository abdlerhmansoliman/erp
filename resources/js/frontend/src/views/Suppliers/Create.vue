<script setup>
import PartyForm from '@/components/Forms/PartyForm.vue';
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from "vue-toastification"
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const toast = useToast()
const route = useRoute();
const router = useRouter();
const formData=ref({
    name: '',
    email: '',
    phone: '',
    address: '',
})
const createCastomer=async(supplierData)=>{
    await api.post('http://localhost:8000/api/suppliers', supplierData);
    toast.success('the supplier created successfully');
    try {
        router.push({name:'suppliers'});
    } catch (error) {
        router.push({name:'suppliers'});
    }
}
</script>

<template>
    <h1>{{ t('create_supplier') }}</h1>
    <PartyForm :model-value="formData" type="supplier" @submit="createCastomer"/>
    </template>