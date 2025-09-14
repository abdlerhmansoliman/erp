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
      component: () => import('@/views/Customers/Customers.vue') ,
      meta:{title:'Customers'}
      },
    { path: 'Customers/:id/edit', name: 'CustomerEdit', component: () => import('@/views/Customers/Edit.vue') },
    {
      path: '/customers/create',
      name: 'CustomerCreate',
      component: () => import('@/views/Customers/Create.vue'),
      meta:{title:'Edit Customers'}
      
    },
    // Suppliers
    { 
      path: 'suppliers', 
      name: 'suppliers', 
      component: () => import('@/views/Suppliers/Suppliers.vue') ,
      meta:{title:'Suppliers'}
    },
    { path: 'suppliers/:id/edit', name: 'SupplierEdit', component: () => import('@/views/Suppliers/Edit.vue') ,meta:{title:'Edit Suppliers'}},
    {
      path: '/suppliers/create',
      name: 'SupplierCreate',
      component: () => import('@/views/Suppliers/Create.vue'),
      meta:{title:'Create Suppliers'}
    },
    {
      path:'purchases',
      name:'purchases',
      component: () => import('@/views/Purchases/Purchases.vue') ,
      meta:{title:'Purchases'}
    },
    {
      path: '/purchases/create',
      name: 'PurchaseCreate',
      component: () => import('@/views/Purchases/Create.vue'),
      meta:{title:'Create Purchases'}
    },

    {
      path:'products',
      name:'products',
      component: () => import('@/views/products/Products.vue'),
      meta:{title:'Products'}
    },
    {
      path: '/products/create',
      name: 'ProductCreate',
      component: () => import('@/views/products/Create.vue'),
      meta:{title:'Create Products'}
    },
    {
      path: '/purchases/:id',
      name: 'PurchaseShow',
      component: () => import('@/views/Purchases/Show.vue'),
      meta:{title:'Show Purchases'}
    },
    {
      path:'categories',
      name:'categories',
      component: () => import('@/views/Categories/Categories.vue'),
      meta:{title:'Categories'}
    },
    {
      path: '/categories/create',
      name: 'CategoryCreate',
      component: () => import('@/views/Categories/Create.vue'),
      meta:{title:'Create Categories'}
    },
    {
      path: '/categories/:id/edit',
      name: 'CategoryEdit',
      component: () => import('@/views/Categories/Edit.vue'),
      meta:{title:'Edit Categories'}
    },
    {
      path:'sales',
      name:'sales',
      component: () => import('@/views/Sales/Sales.vue'),
      meta:{title:'Sales'}
    },
    {
      path: '/sales/create',
      name: 'SalesCreate',
      component: () => import('@/views/Sales/Create.vue'),
      meta:{title:'Create Sales'}
    },
    {
      path: '/sales/:id',
      name: 'SalesShow',
      component: () => import('@/views/Sales/Show.vue'),
      meta:{title:'Show Sales'}
    },
    {
      path: 'returns',
      name: 'PurchaseReturnList', 
      component: () => import('@/views/Purchase Returns/Returns.vue'),
      meta:{title:'Returns'}
    },
    {
      path: '/returns/:type/create/:id',
      name: 'ReturnsCreate',
      component: () => import('@/views/Purchase Returns/Create.vue'),
      meta:{title:'Create Returns'},
      props: true,
    },
    {
      path: '/returns/purchase/:id',
      name: 'returns-show',
      component: () => import('@/views/Purchase Returns/Show.vue'),
      meta:{title:'Show Returns'},
      props: true
    },
    {
      path:'/returns/sales',
      name:'sales-returns',
      component: () => import('@/views/Sales Returns/Returns.vue'),
      meta:{title:'Sales Returns'}
    },
    {
      path: '/returns/sales/:id',
      name: 'sales-returns-show',
      component: () => import('@/views/Sales Returns/Show.vue'),
      meta:{title:'Show Sales Returns'},
      props: true
    },
    {
      path: '/returns/:type/create/:id',
      name: 'ReturnsCreate',
      component: () => import('@/views/Sales Returns/Create.vue'),
      meta:{title:'Create Returns'},
      props: true,
    }

  ]
}

]

const router = createRouter({
  history: createWebHistory(),
  routes
})
router.afterEach((to) => {
  document.title = to.meta.title || "ERP System";
});
export default router
