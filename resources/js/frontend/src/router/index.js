import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import { authRoutes } from './auth'

const routes = [
  ...authRoutes,

{
  path: '/',
  component: MainLayout,
  children: [
    { path: '', redirect: '/dashboard' },
    { path: 'dashboard', component: () => import('@/views/Dashboard.vue') },

    // Customers
    { 
      path: 'customers',
      name: 'customers',
      component: () => import('@/views/Customers/Customers.vue') 
      },
    { path: 'Customers/:id/edit', name: 'CustomerEdit', component: () => import('@/views/Customers/Edit.vue') },
    {
      path: '/customers/create',
      name: 'CustomerCreate',
      component: () => import('@/views/Customers/Create.vue'),
    },
    // Suppliers
    { 
      path: 'suppliers', 
      name: 'suppliers', 
      component: () => import('@/views/Suppliers/Suppliers.vue') 
    },
    { path: 'suppliers/:id/edit', name: 'SupplierEdit', component: () => import('@/views/Suppliers/Edit.vue') },
    {
      path: '/suppliers/create',
      name: 'SupplierCreate',
      component: () => import('@/views/Suppliers/Create.vue'),
    },
  ]
}

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
