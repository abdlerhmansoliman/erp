<template>
  <select v-model="locale" @change="updateLanguage">
    <option 
      v-for="lang in availableLanguages" 
      :key="lang.code" 
      :value="lang.code"
    >
      {{ lang.label }}
    </option>
  </select>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { availableLanguages } from '@/i18n'

const { locale } = useI18n()

function updateLanguage() {
  localStorage.setItem('locale', locale.value)
}
</script>

