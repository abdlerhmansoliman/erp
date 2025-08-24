<script setup>
import PartyForm from '@/components/Forms/PartyForm.vue';
import { ref, onMounted } from 'vue'; 
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from "vue-toastification"

const toast = useToast()
const route = useRoute();
const router = useRouter();

const formDate = ref({
    name: '',
    email: '',
    phone: '',
    address: '', 
});

const fetchCustomers = async () => {
    try {
        if (!route.params.id) {
            alert('the customer ID is missing');
            router.push({ name: 'customerIndex' });
            return;
        }
        
        const response = await axios.get(`http://localhost:8000/api/customers/${route.params.id}`);
        
        if (response.data.data) {
            formDate.value = response.data.data;
        } else {
            formDate.value = response.data;
        }
        
    } catch (error) {
        if (error.response?.status === 404) {
            alert('The customer is not found - maybe it was deleted');
            router.push({ name: 'CustomerIndex' });
        } else if (error.response?.status === 500) {
            alert('خطأ في الخادم');
        } else {
            alert(`خطأ في الاتصال: ${error.response?.status || 'خطأ في الشبكة'}`);
        }
    }
}

onMounted(fetchCustomers);

const updateCustomer = async (updatedData) => {
    try {
        await axios.put(`http://localhost:8000/api/customers/${route.params.id}`, updatedData);
        toast.success('the customer updated successfully');
        
        try {
            router.push({ name: 'CustomerIndex' });
        } catch (routeError) {
            try {
                router.push({ name: 'customers' });
            } catch (routeError2) {
                try {
                    router.push({ name: 'Customers' });
                } catch (routeError3) {
                    try {
                        router.push({ path: '/customers' });
                    } catch (routeError4) {
                        alert('تم التحديث بنجاح - تحقق من قائمة الموردين يدوياً');
                    }
                }
            }
        }
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
    const errors = error.response.data.errors
    Object.values(errors).forEach(errArr => {
      toast.error(errArr[0])
    })
  } else {
    toast.error(error.message)
  }
    }
}
</script>

<template>
    <h1>Edit Customer</h1>
    <PartyForm :model-value="formDate" type="customer" @submit="updateCustomer" />
</template>