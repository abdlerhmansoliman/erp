// layouts/AuthLayout.vue
<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="flex items-center">
              <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">A</span>
              </div>
              <span class="ml-2 text-xl font-semibold text-gray-900">App Name</span>
            </router-link>
          </div>
          
          <div class="flex items-center space-x-4">
            <router-link 
              to="/login" 
              class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 bg-blue-50': $route.name === 'Login' }"
            >
              Sign In
            </router-link>
            <router-link 
              to="/register" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              :class="{ 'bg-green-600 hover:bg-green-700': $route.name === 'Register' }"
            >
              Sign Up
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main>
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-500">
            © 2024 App Name. All rights reserved.
          </p>
          <div class="flex space-x-6">
            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Privacy</a>
            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Terms</a>
            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Support</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// Component logic if needed
</script>