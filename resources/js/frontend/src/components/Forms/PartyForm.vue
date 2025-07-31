<!-- src/components/forms/PartyForm.vue -->
<script setup>
import { ref, watch, onMounted } from 'vue';
const props = defineProps({
  modelValue: Object,
  type: {
    type: String,
    default: 'customer'
  },
  errors: {
    type: Object,
    default: () => ({})
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
  router.push({name:'CustomerIndex'});

}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4 p-4">
    <div>
      <label>Name</label>
      <input v-model="form.name" type="text" class="input" required />
    </div>
    <div>
      <label>Email</label>
      <input v-model="form.email" type="email" class="input" required />
    </div>
    <div>
      <label>Phone</label>
      <input v-model="form.phone" type="number" class="input" required/>
    </div>
    <div>
      <label>Address</label>
      <input v-model="form.address" type="text" class="input" required/>
    </div>
    <div class="flex gap-2 mt-4">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
        Save
      </button>
      <button type="button" @click="handleCancel" class="bg-gray-300 px-4 py-2 rounded">
        Cansel
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
