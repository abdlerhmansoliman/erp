<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(['update:modelValue', 'submit', 'cancel']);

// نعمل نسخة من modelValue داخل form
const form = ref({ ...props.modelValue });

// نراقب أي تغيير في modelValue ونعيد تعيين form
watch(() => props.modelValue, (newVal) => {
  form.value = { ...newVal };
});

function submit() {
  emit('submit', form.value);
}
</script>



<template>
  <div class="p-4 bg-gray-50 border rounded">
    <div class="mb-2">
      <label>الاسم:</label>
      <input v-model="form.name" type="text" class="border p-2 w-full rounded" />
    </div>

    <div class="mb-2">
      <label>الإيميل:</label>
      <input v-model="form.email" type="email" class="border p-2 w-full rounded" />
    </div>

    <div class="mb-2">
      <label>الهاتف:</label>
      <input v-model="form.phone" type="text" class="border p-2 w-full rounded" />
    </div>

    <div class="mb-2">
      <label>العنوان:</label>
      <input v-model="form.address" type="text" class="border p-2 w-full rounded" />
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button @click="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">حفظ</button>
      <button @click="$emit('cancel')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">إلغاء</button>
    </div>
  </div>
</template>
