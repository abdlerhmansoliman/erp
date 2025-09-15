<script setup>
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import api from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';

const router = useRouter();
const toast = useToast();

const schema = yup.object({
  name: yup.string().required('الاسم مطلوب'),
  address: yup.string().required('العنوان مطلوب'),
phone: yup.string()
    .matches(/^[0-9]{10,15}$/, 'رقم الهاتف غير صحيح')
    .required('رقم الهاتف مطلوب'),
      email: yup.string().email('صيغة الايميل غير صحيحة').required('الإيميل مطلوب'),
});

function submitForm(values, {setFieldError}){
    api.post('/warehouses', values)
    .then(res => {
      toast.success('تم إضافة المخزن بنجاح')
      router.push({ name: 'warehouses' })
    })
    .catch(err => {
      if (err.response?.data?.errors) {
        const backendErrors = err.response.data.errors
        Object.keys(backendErrors).forEach(key => {
          setFieldError(key, backendErrors[key][0]) 
        })
      } else {
        toast.error(err.response?.data?.message || 'حدث خطأ')
      }
    })
}
</script>

<template>
    <div class="max-w-md mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">إضافة مخزن جديد</h2>
        <Form @submit="submitForm" :validation-schema="schema" v-slot="{ errors, setFieldError }">
                <div>
        <label class="block mb-1">الاسم</label>
        <Field name="name" type="text" placeholder="اسم المخزن" class="border p-2 w-full" />
        <span class="text-red-500 text-sm mt-1">{{ errors.name }}</span>
      </div>

      <!-- العنوان -->
      <div>
        <label class="block mt-5">العنوان</label>
        <Field name="address" type="text" placeholder="عنوان المخزن" class="border p-2 w-full" />
        <span class="text-red-500 text-sm mt-1">{{ errors.address }}</span>
      </div>

      <!-- الهاتف -->
      <div>
        <label class="block mt-5">رقم الهاتف</label>
        <Field name="phone" type="text" placeholder="رقم الهاتف" class="border p-2 w-full" />
        <span class="text-red-500 text-sm mt-1">{{ errors.phone }}</span>
      </div>

      <!-- الايميل -->
      <div>
        <label class="block mt-5">الإيميل</label>
        <Field name="email" type="email" placeholder="البريد الإلكتروني" class="border p-2 w-full" />
        <span class="text-red-500 text-sm mt-1">{{ errors.email }}</span>
      </div>

      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">حفظ</button>
        </Form>
    </div>
</template>