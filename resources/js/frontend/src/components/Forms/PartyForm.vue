<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

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
}, { deep: true });

watch(form, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

function handleSubmit() {
  emit('submit', form.value);
}

function handleCancel() {
  router.push({ name: 'CustomerIndex' });
}
</script>

<template>
  <form
    @submit.prevent="handleSubmit"
    class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto space-y-6"
  >
    <!-- Form fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="input"
          :class="{ 'border-red-500': errors.name }"
          required
        />
        <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name[0] }}</span>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="input"
          :class="{ 'border-red-500': errors.email }"
          required
        />
        <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email[0] }}</span>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input
          v-model="form.phone"
          type="tel"
          class="input"
          :class="{ 'border-red-500': errors.phone }"
          required
        />
        <span v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone[0] }}</span>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Address</label>
        <input
          v-model="form.address"
          type="text"
          class="input"
          :class="{ 'border-red-500': errors.address }"
          required
        />
        <span v-if="errors.address" class="text-red-500 text-sm">{{ errors.address[0] }}</span>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 pt-4">
      <button
        type="submit"
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors w-full sm:w-auto"
      >
        Save {{ type === 'customer' ? 'Customer' : 'Supplier' }}
      </button>
      <button
        type="button"
        @click="handleCancel"
        class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded-lg transition-colors w-full sm:w-auto"
      >
        Cancel
      </button>
    </div>
  </form>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 0.5rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

.border-red-500 {
  border-color: #ef4444;
}
</style>
