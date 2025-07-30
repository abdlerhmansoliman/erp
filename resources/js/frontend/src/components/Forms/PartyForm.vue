<!-- src/components/forms/PartyForm.vue -->
<script setup>
import { ref, watch, onMounted } from 'vue';
const props = defineProps({
  modelValue: Object,
  type: {
    type: String,
    default: 'supplier' // أو 'customer'
  }
});
const emit = defineEmits(['update:modelValue', 'submit', 'cancel']);

const form = ref({ ...props.modelValue });

watch(() => props.modelValue, (newVal) => {
  form.value = { ...newVal };
});

function handleSubmit() {
  emit('submit', form.value);
}
function handleCancel() {
  emit('cancel');
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4 p-4">
    <div>
      <label>الاسم</label>
      <input v-model="form.name" type="text" class="input" required />
    </div>
    <div>
      <label>البريد الإلكتروني</label>
      <input v-model="form.email" type="email" class="input" />
    </div>
    <div>
      <label>رقم الهاتف</label>
      <input v-model="form.phone" type="text" class="input" />
    </div>
    <div>
      <label>العنوان</label>
      <input v-model="form.address" type="text" class="input" />
    </div>
    <div class="flex gap-2 mt-4">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
        حفظ
      </button>
      <button type="button" @click="handleCancel" class="bg-gray-300 px-4 py-2 rounded">
        إلغاء
      </button>
    </div>
  </form>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 0.375rem;
}
</style>
