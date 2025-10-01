<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t  } = useI18n();

// reactive headers

const productHeaders = computed(()=> [
    { text: t('name'), value: 'name', sortable: true },
    { text: t('category'), value: 'category_name', sortable: true },
    { text: t('description'), value: 'description', sortable: false },
    { text: t('product_code'), value: 'product_code', sortable: true },
    { text: t('price'), value: 'price', sortable: true },
    { text: t('unit'), value: 'unit_name', sortable: true },
  ]);


// initialize

</script>

<template>
  <div>
    <BaseCrudTable
      endpoint="/products"
      :headers="productHeaders"
      resource-name="product"
      edit-route-name="ProductEdit"
      create-route="/Products/Create"
      search-placeholder="البحث..."
      empty-message="لا توجد بيانات متاحة"
      delete-confirmation-key="product_id"
    >
      <template #create-button-text>
        {{ t('add') }} 
      </template>
    </BaseCrudTable>
  </div>
</template>
