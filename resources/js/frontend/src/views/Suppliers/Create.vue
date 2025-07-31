<script setup>
import PartyForm from '@/components/Forms/PartyForm.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
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
const createCastomer=async(supplierData)=>{
    await axios.post('http://localhost:8000/api/suppliers', supplierData);
    toast.success('the supplier created successfully');
    try {
        router.push({name:'suppliers'});
    } catch (error) {
        router.push({name:'suppliers'});
    }
}
</script>

<template>
    <h1>Create Supplier</h1>
    <PartyForm :model-value="formData" type="supplier" @submit="createCastomer"/>
    </template>