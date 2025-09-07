<script setup>
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';

const props = defineProps({
  endpoint: { type: String, required: true },
  method: { type: String, default: "POST" },
  initialData: { type: Object, default: () => ({}) },
  redirectRoute: { type: String, default: null },
});

const toast = useToast();
const router = useRouter();

const form = ref({
  name_ar: "",
  name_en: "",
});

onMounted(() => {
  if (props.initialData && (props.initialData.name_ar || props.initialData.name_en)) {
    form.value = {
      name_ar: props.initialData.name_ar ?? "",
      name_en: props.initialData.name_en ?? "",
    };
  }
});

async function handleSubmit() {
  try {
    if (props.method === "POST") {
      await api.post(props.endpoint, form.value);
    } else {
      await api.put(`${props.endpoint}/${props.initialData.id}`, form.value);
    }

    toast.success(props.method === "POST" ? "Category added successfully" : "Category updated successfully");

    if (props.redirectRoute) {
      router.push({ name: props.redirectRoute });
    }
  } catch (error) {
    console.error(error);
    toast.error("حدث خطأ أثناء الحفظ ");
  }
}
</script>

<template>
  <div>
    <form
      @submit.prevent="handleSubmit"
      class="max-w-md mx-auto bg-white shadow rounded-lg p-6 space-y-4"
    >
      <div>
        <label class="block text-sm font-medium mb-1">Arabic Category Name</label>
        <input
          v-model="form.name_ar"
          type="text"
          class="w-full border rounded px-3 py-2"
          placeholder="Enter Arabic Category Name"
          required
        />
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium">Category Name (English)</label>
        <input
          v-model="form.name_en"
          type="text"
          class="w-full border rounded px-3 py-2"
          placeholder="Example: Electronics"
          required
        />
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          {{ props.method === "POST" ? "إضافة" : "تعديل" }}
        </button>
      </div>
    </form>
  </div>
</template>
