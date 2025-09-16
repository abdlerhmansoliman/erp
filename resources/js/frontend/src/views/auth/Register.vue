<script setup>
import { ref } from 'vue';
import api from '@/plugins/axios';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';

const router = useRouter();
const toast = useToast();
const { t } = useI18n();

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});
const errors = ref({});

const register = async () => {
    errors.value = {};
    try {
        await api.post('/auth/register', form.value);

        const { data } = await api.post('/auth/login', {
            email: form.value.email,
            password: form.value.password
        });

        localStorage.setItem('token', data.token);

        toast.success('Registration successful!');
        router.push({ name: 'Home' });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            toast.error(error.response?.data?.message || 'Registration failed. Please try again.');
        }
    }
}


</script>

<template>
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-2xl shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Sign Up</h1>

    <form @submit.prevent="register" class="space-y-4">
    <input type="text" name="fake-username" autocomplete="username" style="display:none" />
<input type="password" name="fake-password" autocomplete="new-password" style="display:none" />
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            autocomplete="off"

        />
        <p v-if="errors.name" class="text-red-600 text-sm mt-1">
          {{ errors.name[0] }}
        </p>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            autocomplete="off"

        />
        <p v-if="errors.email" class="text-red-600 text-sm mt-1">
          {{ errors.email[0] }}
        </p>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            autocomplete="off"

        />
        <p v-if="errors.password" class="text-red-600 text-sm mt-1">
          {{ errors.password[0] }}
        </p>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium mb-1">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            autocomplete="off"

        />
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
      >
        Sign Up
      </button>
    </form>

    <p class="text-center text-sm mt-4">
      Already have an account?
      <router-link to="/login" class="text-blue-600 hover:underline">Sign In</router-link>
    </p>
  </div>
</template>