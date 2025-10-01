<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const transferHeaders = computed(() => [
  
  { text: t('from_warehouse'), value: 'from_warehouse', sortable: true },
  { text: t('to_warehouse'), value: 'to_warehouse', sortable: true },
  { text: t('transfer_date'), value: 'transfer_date', sortable: true },
  { text: t('created_by'), value: 'created_by', sortable: true },
  { text: t('status'), value: 'status', sortable: true },
]);
</script>

<template>
  <div>
    <BaseCrudTable
      endpoint="/transfers"
      :headers="transferHeaders"
      resource-name="transfer"
      edit-route-name="TransferEdit"
      create-route="/transfer/create"
      :show-view="true"
      show-route-name="TransferShow"
      search-placeholder="ابحث عن تحويل..."
      empty-message="لا توجد تحويلات متاحة"
      delete-confirmation-key="id"
    >
      <template #create-button-text>
        {{ t('add') }}
      </template>
    </BaseCrudTable>
  </div>
</template>
