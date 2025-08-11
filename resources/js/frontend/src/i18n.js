import { createI18n } from 'vue-i18n'
import en from '../locales/en.json'
import ar from '../locales/ar.json'
const savedLocale = localStorage.getItem('locale') || 'en'

export const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('lang') || 'en',
    locale: savedLocale,
    fallbackLocale: 'en',
    messages: { en, ar }
})
export const availableLanguages = [
  { code: 'en', label: 'English' },
  { code: 'ar', label: 'العربية' }
]
