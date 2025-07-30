<script setup>
import PartyForm from '@/components/Forms/PartyForm.vue';
import { ref, onMounted } from 'vue'; 
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const formDate = ref({
    name: '',
    email: '',
    phone: '',
    address: '', 
});

const fetchSuppliers = async () => {
    try {
        if (!route.params.id) {
            alert('معرف المورد غير موجود');
            router.push({ name: 'SupplierIndex' });
            return;
        }
        
        const response = await axios.get(`http://localhost:8000/api/suppliers/${route.params.id}`);
        
        if (response.data.data) {
            formDate.value = response.data.data;
        } else {
            formDate.value = response.data;
        }
        
    } catch (error) {
        if (error.response?.status === 404) {
            alert('المورد غير موجود - ربما تم حذفه');
            router.push({ name: 'SupplierIndex' });
        } else if (error.response?.status === 500) {
            alert('خطأ في الخادم');
        } else {
            alert(`خطأ في الاتصال: ${error.response?.status || 'خطأ في الشبكة'}`);
        }
    }
}

onMounted(fetchSuppliers);

const updateSupplier = async (updatedData) => {
    try {
        await axios.put(`http://localhost:8000/api/suppliers/${route.params.id}`, updatedData);
        alert('تم التحديث بنجاح')
        
        try {
            router.push({ name: 'SupplierIndex' });
        } catch (routeError) {
            try {
                router.push({ name: 'suppliers' });
            } catch (routeError2) {
                try {
                    router.push({ name: 'Suppliers' });
                } catch (routeError3) {
                    try {
                        router.push({ path: '/suppliers' });
                    } catch (routeError4) {
                        alert('تم التحديث بنجاح - تحقق من قائمة الموردين يدوياً');
                    }
                }
            }
        }
        
    } catch (error) {
        if (error.response?.status === 404) {
            alert('المورد غير موجود');
        } else if (error.response?.status === 422) {
            alert('خطأ في البيانات المدخلة');
        } else {
            alert(`حدث خطأ أثناء التحديث: ${error.response?.status || 'خطأ في الشبكة'}`);
        }
    }
}
</script>

<template>
    <h1>Edit Supplier</h1>
    <PartyForm :model-value="formDate" type="supplier" @submit="updateSupplier" />
</template>