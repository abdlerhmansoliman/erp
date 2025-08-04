<!-- src/components/forms/PartyForm.vue -->
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

// Watch form changes and emit updates
watch(form, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

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
    
    <div class="flex gap-2 mt-4">
      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
        Save {{ type === 'customer' ? 'Customer' : 'Supplier' }}
      </button>
      <button type="button" @click="handleCancel" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded transition-colors">
        Cancel
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
  transition: border-color 0.2s;
}

.input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px #3b82f6;
}

.border-red-500 {
  border-color: #ef4444;
}
</style>