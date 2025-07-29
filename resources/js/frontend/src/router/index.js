import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import Suppliers from '@/views/Suppliers.vue'
const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', redirect: '/dashboard' },
      { path: 'dashboard', component: () => import('@/views/Dashboard.vue') },
      { path: 'customers', component: () => import('@/views/Customers.vue') },
      { path: 'invoices', component: () => import('@/views/Invoices.vue') },
      { path: 'suppliers', component: () => import('@/views/Suppliers.vue') },
{
  path: 'suppliers/:id/edit',
  name: 'SupplierEdit',
  component: () => import('@/views/SupplierForm.vue')
}

    ]
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
