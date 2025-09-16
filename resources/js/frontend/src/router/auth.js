// router/auth.js
import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
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
      {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: {
          title: 'Sign Up',
          guestOnly: true
        }
      }
     
    ]
  }
]