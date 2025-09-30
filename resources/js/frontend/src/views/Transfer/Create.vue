<script setup>
import { ref} from 'vue';
import api from '@/plugins/axios';
const form = ref({
    from_warehouse_id: '',
    to_warehouse_id: '',
    transfer_date: '',
});
</script>
<template>
    <h1>Transfer</h1>
</template>