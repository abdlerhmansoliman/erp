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
const fetchCustomers = async () => {
    try {
        if(!route.params.id){
            alert('the customer ID is missing');
            router.push({name:'CustomerIndex'});
            return;
        }
        const response= await axios.get(`http://localhost:8000/api/customers/${route.params.id}`);
        if(response.data.data){
            formData.value=response.data.data;
        }else{
            formData.value=response.data;
        }
    } catch (error) {
        if (error.response?.status === 404) {
            alert('The customer is not found - maybe it was deleted');
            router.push({ name: 'CustomerIndex' });
        } else if (error.response?.status === 500) {
            alert('error in the server');
        } else {
            alert(`error in the connection: ${error.response?.status || 'خطاء في الشبكة'}`);
        }
    }
}
onMounted(fetchCustomers);
const updateCustomer=async(updatedData)=>{
    await axios.put(`http://localhost:8000/api/customers/${route.params.id}`, updatedData);
    toast.success('the customer updated successfully');
    try {
        router.push({name:'CustomerIndex'});
    } catch (error) {
        router.push({name:'customers'});
    }
}
</script>

<template>
    <h1>Edit Customer</h1>
    <PartyForm :model-value="formData" type="customer" @submit="updateCustomer" />
</template>