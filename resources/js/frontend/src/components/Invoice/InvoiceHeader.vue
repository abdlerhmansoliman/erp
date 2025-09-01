<script setup>

const props = defineProps({
  partyList: { type: Array, default: () => [] },    
  selectedParty: { type: Object, default: null },
  warehouseList: { type: Array, default: () => [] },
  selectedWarehouse: { type: Object, default: null },
  date: { type: String, default: new Date().toISOString().slice(0,10) },
  status: { type: String, default: 'draft' },
  partyLabel: { type: String, default: 'المورد / العميل' } 

});

const emit = defineEmits([
  'update-selected-party',
  'update-selected-warehouse',
  'update-date',
  'update-status'
]);

function onPartyChange(e) {
  const party = props.partyList.find(p => p.id == e.target.value)
  emit('update-selected-party', party)
}

function onWarehouseChange(e) {
  const warehouse = props.warehouseList.find(w => w.id == e.target.value)
  emit('update-selected-warehouse', warehouse)
}

function onDateChange(e) {
  emit('update-date', e.target.value)
}

function onStatusChange(e) {
  emit('update-status', e.target.value)
}
</script>

<template>
  <div class="grid grid-cols-4 gap-4 mb-4">
    <!-- Party Select -->
    <div>
      <label>{{ props.partyLabel }}</label>
      <select :value="selectedParty?.id" @change="onPartyChange" class="border rounded p-2 w-full">
        <option value="">اختر</option>
        <option v-for="party in partyList" :key="party.id" :value="party.id">
          {{ party.name }}
        </option>
      </select>
    </div>

    <!-- Warehouse Select -->
    <div>
      <label>المخزن</label>
      <select :value="selectedWarehouse?.id" @change="onWarehouseChange" class="border rounded p-2 w-full">
        <option value="">اختر</option>
        <option v-for="w in warehouseList" :key="w.id" :value="w.id">
          {{ w.name }}
        </option>
      </select>
    </div>

    <!-- Status Select -->
    <div>
      <label>الحالة</label>
      <select :value="status" @change="onStatusChange" class="border rounded p-2 w-full">
        <option value="draft">مسودة</option>
        <option value="ordered">تم الطلب</option>
        <option value="received">تم الاستلام</option>
        <option value="cancelled">ملغية</option>
      </select>
    </div>

    <!-- Date -->
    <div>
      <label>التاريخ</label>
      <input type="date" :value="date" @change="onDateChange" class="border rounded p-2 w-full" />
    </div>
  </div>
</template>
