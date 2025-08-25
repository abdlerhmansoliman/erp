<template>
<form>
  <label for="language-select">اختر اللغة:</label>
<select :key="languageKey" v-model="locale" @change="updateLanguage" autocomplete="off">
    <option v-for="lang in availableLanguages" :key="lang.code" :value="lang.code">
      {{ lang.label }}
    </option>
  </select>
</form>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { availableLanguages } from '@/i18n'

const { locale } = useI18n()
const languageKey = ref(Date.now())

function updateLanguage() {
  localStorage.setItem('locale', locale.value)
}
</script>

