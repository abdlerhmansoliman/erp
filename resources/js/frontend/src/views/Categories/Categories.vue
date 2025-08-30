<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'
import api from '@/plugins/axios'

const categoriesHeader=[
    { text: 'Name', value: 'name', sortable: true },
]
const router = useRouter()

const categories = ref([])

onMounted(async () => {
    const response = await api.get('/categories')
    categories.value = response.data
})
</script>
<template>
<h1>Categories</h1>
    <BaseCrudTable
        endpoint="/categories"
        :headers="categoriesHeader"
        resource-name="category"
        edit-route-name="category-edit"
        create-route="/categories/Create"
        search-placeholder="البحث..."
        empty-message="لا توجد بيانات متاحة"
        delete-confirmation-key="category_id"
    >
        <!-- Custom create button text if needed -->
        <template #create-button-text>
            Add Product
        </template>
    </BaseCrudTable>
</template>