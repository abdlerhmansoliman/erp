<template>
  <div class="relative inline-block text-left">
    <div>
      <button
        type="button"
        class="inline-flex justify-between w-40 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
        @click="open = !open"
      >
        {{ currentLanguage.label }}
        <svg
          class="-mr-1 ml-2 h-5 w-5"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
    </div>

    <!-- Dropdown menu -->
    <div
      v-if="open"
      class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
    >
      <div class="py-1">
        <button
          v-for="lang in availableLanguages"
          :key="lang.code"
          @click="changeLanguage(lang.code)"
          class="block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
        >
          {{ lang.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { availableLanguages } from '@/i18n'

const { locale } = useI18n()
const open = ref(false)

const currentLanguage = computed(() =>
  availableLanguages.find(l => l.code === locale.value) || availableLanguages[0]
)

function changeLanguage(code) {
  locale.value = code
  localStorage.setItem('locale', code)
  open.value = false
}
</script>
