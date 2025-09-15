<script setup>
import PartyForm from '@/components/Forms/PartyForm.vue';
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from "vue-toastification"

const toast = useToast()
const route = useRoute();
const router = useRouter();
const formData=ref({
    name: '',
    email: '',
    phone: '',
    address: '',
})
const createCastomer=async(customerData)=>{
    await api.post('http://localhost:8000/api/customers', customerData);
    toast.success('the customer created successfully');
    try {
        router.push({name:'CustomerIndex'});
    } catch (error) {
        router.push({name:'customers'});
    }
}
</script>

<template>
    <h1>Create Customer</h1>
    <PartyForm :model-value="formData" type="customer" @submit="createCastomer"/>
    </template>