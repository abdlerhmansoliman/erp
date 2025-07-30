import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
const routes = [
{
  path: '/',
  component: MainLayout,
  children: [
    { path: '', redirect: '/dashboard' },
    { path: 'dashboard', component: () => import('@/views/Dashboard.vue') },

    // Customers
    { path: 'customers', component: () => import('@/views/Customers/Customers.vue') },

    // Suppliers
    { path: 'suppliers', component: () => import('@/views/Suppliers/Suppliers.vue') },
    { path: 'suppliers/create', name: 'SupplierCreate', component: () => import('@/views/SupplierForm.vue') },
    { path: 'suppliers/:id/edit', name: 'SupplierEdit', component: () => import('@/views/Suppliers/Edit.vue') },
  ]
}

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
