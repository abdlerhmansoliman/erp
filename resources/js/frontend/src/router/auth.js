// router/auth.js
import Login from '@/views/auth/Login.vue'

import AuthLayout from '@/layouts/AuthLayout.vue'

export const authRoutes = [
  {
    path: '/auth',
    component: AuthLayout,
    meta: { guestOnly: true },
    children: [
      {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: {
          title: 'Sign In',
          guestOnly: true
        }
      },
     
    ]
  }
]