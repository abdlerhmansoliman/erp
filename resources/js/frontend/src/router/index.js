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
    {
      path:'purchases',
      name:'purchases',
      component: () => import('@/views/Purchases/Purchases.vue') 
    },
    {
      path: '/purchases/create',
      name: 'PurchaseCreate',
      component: () => import('@/views/Purchases/Create.vue')
    },
    {
      path: '/purchases/:id/edit',
      name: 'PurchaseEdit',
      component: () => import('@/views/Purchases/Edit.vue')
    },

    {
      path:'products',
      name:'products',
      component: () => import('@/views/products/Products.vue')
    },
    {
      path: '/products/create',
      name: 'ProductCreate',
      component: () => import('@/views/products/Create.vue')
    },
    {
      path: '/purchases/:id',
      name: 'PurchaseShow',
      component: () => import('@/views/Purchases/Show.vue')
    },
    {
      path:'categories',
      name:'categories',
      component: () => import('@/views/Categories/Categories.vue')
    },
    {
      path: '/categories/create',
      name: 'CategoryCreate',
      component: () => import('@/views/Categories/Create.vue')
    },
    {
      path: '/categories/:id/edit',
      name: 'CategoryEdit',
      component: () => import('@/views/Categories/Edit.vue')
    },
    {
      path:'sales',
      name:'sales',
      component: () => import('@/views/Sales/Sales.vue')
    },
    {
      path: '/sales/create',
      name: 'SalesCreate',
      component: () => import('@/views/Sales/Create.vue')
    },
    {
      path: '/sales/:id',
      name: 'SalesShow',
      component: () => import('@/views/Sales/Show.vue')
    },
    {
      path:'returns',
      name:'returns',
      component: () => import('@/views/Returns/Returns.vue')
    }
  ]
}

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
