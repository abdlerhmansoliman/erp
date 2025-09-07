<script setup>
import CategoryForm from '@/components/Forms/CategoryFrom.vue'
</script>

<template>
  <CategoryForm 
    endpoint="/categories" 
    method="POST" 
    redirectRoute="categories" 
  />
</template>
