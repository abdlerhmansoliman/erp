// router/guards.js
import { useAuthStore } from '@/stores/authStore'
import { usePermissions } from '@/composables/usePermissions'
import { useNotification } from '@/composables/useNotification'

/**
 * Authentication guard
 */
export const authGuard = (to, from, next) => {
  const authStore = useAuthStore()
  
  if (!authStore.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
}

/**
 * Guest guard (redirect authenticated users)
 */
export const guestGuard = (to, from, next) => {
  const authStore = useAuthStore()
  
  if (authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
}

/**
 * Permission guard
 */
export const permissionGuard = (requiredPermissions) => {
  return async (to, from, next) => {
    const authStore = useAuthStore()
    const { can, canAny, canAll, loadUserPermissions } = usePermissions()
    const { showError } = useNotification()
    
    // Check if user is authenticated
    if (!authStore.isAuthenticated) {
      next('/login')
      return
    }
    
    // Load user permissions if not already loaded
    await loadUserPermissions()
    
    let hasPermission = false
    
    if (Array.isArray(requiredPermissions)) {
      // Check if user has any of the required permissions
      hasPermission = canAny(requiredPermissions)
    } else if (typeof requiredPermissions === 'string') {
      // Check single permission
      hasPermission = can(requiredPermissions)
    } else if (typeof requiredPermissions === 'object') {
      // Handle complex permission requirements
      const { any, all, permission } = requiredPermissions
      
      if (any) {
        hasPermission = canAny(any)
      } else if (all) {
        hasPermission = canAll(all)
      } else if (permission) {
        hasPermission = can(permission)
      }
    }
    
    if (hasPermission) {
      next()
    } else {
      showError('You do not have permission to access this page', 'Access Denied')
      next('/dashboard')
    }
  }
}

/**
 * Role guard
 */
export const roleGuard = (requiredRoles) => {
  return async (to, from, next) => {
    const authStore = useAuthStore()
    const { hasRole, hasAnyRole, loadUserPermissions } = usePermissions()
    const { showError } = useNotification()
    
    // Check if user is authenticated
    if (!authStore.isAuthenticated) {
      next('/login')
      return
    }
    
    // Load user permissions if not already loaded
    await loadUserPermissions()
    
    let hasRequiredRole = false
    
    if (Array.isArray(requiredRoles)) {
      hasRequiredRole = hasAnyRole(requiredRoles)
    } else {
      hasRequiredRole = hasRole(requiredRoles)
    }
    
    if (hasRequiredRole) {
      next()
    } else {
      showError('You do not have the required role to access this page', 'Access Denied')
      next('/dashboard')
    }
  }
}

/**
 * Admin guard
 */
export const adminGuard = async (to, from, next) => {
  const authStore = useAuthStore()
  const { isAdmin, loadUserPermissions } = usePermissions()
  const { showError } = useNotification()
  
  if (!authStore.isAuthenticated) {
    next('/login')
    return
  }
  
  await loadUserPermissions()
  
  if (isAdmin.value) {
    next()
  } else {
    showError('Admin access required', 'Access Denied')
    next('/dashboard')
  }
}

/**
 * Super admin guard
 */
export const superAdminGuard = async (to, from, next) => {
  const authStore = useAuthStore()
  const { isSuperAdmin, loadUserPermissions } = usePermissions()
  const { showError } = useNotification()
  
  if (!authStore.isAuthenticated) {
    next('/login')
    return
  }
  
  await loadUserPermissions()
  
  if (isSuperAdmin.value) {
    next()
  } else {
    showError('Super admin access required', 'Access Denied')
    next('/dashboard')
  }
}

/**
 * Combine multiple guards
 */
export const combineGuards = (...guards) => {
  return async (to, from, next) => {
    for (const guard of guards) {
      try {
        await new Promise((resolve, reject) => {
          guard(to, from, (result) => {
            if (result === false || (typeof result === 'string' && result !== to.path)) {
              reject(new Error('Guard failed'))
            } else {
              resolve(result)
            }
          })
        })
      } catch (error) {
        return // Guard failed, stop execution
      }
    }
    next() // All guards passed
  }
}

/**
 * Helper function to create permission-based route meta
 */
export const requiresPermission = (permissions) => {
  return {
    requiresAuth: true,
    requiredPermissions: permissions
  }
}

/**
 * Helper function to create role-based route meta
 */
export const requiresRole = (roles) => {
  return {
    requiresAuth: true,
    requiredRoles: roles
  }
}

/**
 * Global navigation guard setup
 */
export const setupNavigationGuards = (router) => {
  router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    const { loadUserPermissions, can, canAny, hasRole, hasAnyRole } = usePermissions()
    const { showError } = useNotification()
    
    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      next('/login')
      return
    }
    
    // Redirect authenticated users from guest-only pages
    if (to.meta.guestOnly && authStore.isAuthenticated) {
      next('/dashboard')
      return
    }
    
    // If user is authenticated and route has permission/role requirements
    if (authStore.isAuthenticated && (to.meta.requiredPermissions || to.meta.requiredRoles)) {
      // Load user permissions if not already loaded
      await loadUserPermissions()
      
      // Check permissions
      if (to.meta.requiredPermissions) {
        const permissions = to.meta.requiredPermissions
        let hasPermission = false
        
        if (Array.isArray(permissions)) {
          hasPermission = canAny(permissions)
        } else if (typeof permissions === 'string') {
          hasPermission = can(permissions)
        } else if (typeof permissions === 'object') {
          const { any, all, permission } = permissions
          
          if (any) {
            hasPermission = canAny(any)
          } else if (all) {
            hasPermission = canAll(all)
          } else if (permission) {
            hasPermission = can(permission)
          }
        }
        
        if (!hasPermission) {
          showError('You do not have permission to access this page', 'Access Denied')
          next('/dashboard')
          return
        }
      }
      
      // Check roles
      if (to.meta.requiredRoles) {
        const roles = to.meta.requiredRoles
        let hasRequiredRole = false
        
        if (Array.isArray(roles)) {
          hasRequiredRole = hasAnyRole(roles)
        } else {
          hasRequiredRole = hasRole(roles)
        }
        
        if (!hasRequiredRole) {
          showError('You do not have the required role to access this page', 'Access Denied')
          next('/dashboard')
          return
        }
      }
    }
    
    next()
  })
}