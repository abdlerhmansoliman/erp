<script setup>
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import api from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const props = defineProps({
  warehouse: {
    type: Object,
    default: null
  },
  mode: {
    type: String,
    default: 'create', // 'create' or 'edit'
    validator: (value) => ['create', 'edit'].includes(value)
  }
});

const router = useRouter();
const toast = useToast();

const schema = yup.object({
  name: yup.string().required('الاسم مطلوب'),
  address: yup.string().required('العنوان مطلوب'),
  phone: yup
    .string()
    .matches(/^[0-9+\-\s()]{8,20}$/, 'رقم الهاتف غير صحيح')
    .required('رقم الهاتف مطلوب'),
  email: yup.string().email('صيغة الإيميل غير صحيحة').required('الإيميل مطلوب'),
});

// Computed properties for dynamic content
const formTitle = computed(() => {
  return props.mode === 'create' ? 'إضافة مخزن جديد' : 'تعديل المخزن';
});

const submitButtonText = computed(() => {
  return props.mode === 'create' ? 'حفظ' : 'تحديث';
});

const successMessage = computed(() => {
  return props.mode === 'create' ? 'تم إضافة المخزن بنجاح' : 'تم تحديث المخزن بنجاح';
});

// Initial values for the form
const initialValues = computed(() => {
  if (props.mode === 'edit' && props.warehouse) {
    return {
      name: props.warehouse.name || '',
      address: props.warehouse.address || '',
      phone: props.warehouse.phone || '',
      email: props.warehouse.email || ''
    };
  }
  return {
    name: '',
    address: '',
    phone: '',
    email: ''
  };
});

function submitForm(values, { setFieldError }) {
  const apiCall = props.mode === 'create' 
    ? api.post('/warehouses', values)
    : api.put(`/warehouses/${props.warehouse.id}`, values);

  apiCall
    .then(res => {
      toast.success(successMessage.value);
      router.push({ name: 'warehouses' });
    })
    .catch(err => {
      if (err.response?.data?.errors) {
        const backendErrors = err.response.data.errors;
        Object.keys(backendErrors).forEach(key => {
          setFieldError(key, backendErrors[key][0]);
        });
      } else {
        toast.error(err.response?.data?.message || 'حدث خطأ');
      }
    });
}
</script>

<template>
  <div class="max-w-md mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">{{ formTitle }}</h2>
    
    <Form 
      @submit="submitForm" 
      :validation-schema="schema" 
      :initial-values="initialValues"
      v-slot="{ errors }"
    >
      <!-- الاسم -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">الاسم</label>
        <Field 
          name="name" 
          type="text" 
          placeholder="اسم المخزن" 
          class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
        />
        <span v-if="errors.name" class="text-red-500 text-sm mt-1 block">{{ errors.name }}</span>
      </div>

      <!-- العنوان -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">العنوان</label>
        <Field 
          name="address" 
          type="text" 
          placeholder="عنوان المخزن" 
          class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
        />
        <span v-if="errors.address" class="text-red-500 text-sm mt-1 block">{{ errors.address }}</span>
      </div>

      <!-- الهاتف -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">رقم الهاتف</label>
        <Field 
          name="phone" 
          type="text" 
          placeholder="رقم الهاتف" 
          class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
        />
        <span v-if="errors.phone" class="text-red-500 text-sm mt-1 block">{{ errors.phone }}</span>
      </div>

      <!-- الايميل -->
      <div class="mb-6">
        <label class="block mb-1 font-medium">الإيميل</label>
        <Field 
          name="email" 
          type="email" 
          placeholder="البريد الإلكتروني" 
          class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
        />
        <span v-if="errors.email" class="text-red-500 text-sm mt-1 block">{{ errors.email }}</span>
      </div>

      <div class="flex gap-2">
        <button 
          type="submit" 
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors duration-200"
        >
          {{ submitButtonText }}
        </button>
        
        <button 
          type="button" 
          @click="router.push({ name: 'warehouses' })"
          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors duration-200"
        >
          إلغاء
        </button>
      </div>
    </Form>
  </div>
</template>