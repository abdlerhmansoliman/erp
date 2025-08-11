// directives/permissions.js
import { usePermissions } from '@/composables/usePermissions'

/**
 * v-can directive - shows/hides element based on permission
 * Usage: v-can="'edit-users'" or v-can="['edit-users', 'delete-users']"
 */
const vCan = {
  mounted(el, binding) {
    const { can, canAny } = usePermissions()
    const permission = binding.value
    
    let hasPermission = false
    
    if (Array.isArray(permission)) {
      hasPermission = canAny(permission)
    } else {
      hasPermission = can(permission)
    }
    
    if (!hasPermission) {
      el.style.display = 'none'
    }
  },
  
  updated(el, binding) {
    const { can, canAny } = usePermissions()
    const permission = binding.value
    
    let hasPermission = false
    
    if (Array.isArray(permission)) {
      hasPermission = canAny(permission)
    } else {
      hasPermission = can(permission)
    }
    
    el.style.display = hasPermission ? '' : 'none'
  }
}

/**
 * v-role directive - shows/hides element based on role
 * Usage: v-role="'admin'" or v-role="['admin', 'editor']"
 */
const vRole = {
  mounted(el, binding) {
    const { hasRole, hasAnyRole } = usePermissions()
    const role = binding.value
    
    let hasRequiredRole = false
    
    if (Array.isArray(role)) {
      hasRequiredRole = hasAnyRole(role)
    } else {
      hasRequiredRole = hasRole(role)
    }
    
    if (!hasRequiredRole) {
      el.style.display = 'none'
    }
  },
  
  updated(el, binding) {
    const { hasRole, hasAnyRole } = usePermissions()
    const role = binding.value
    
    let hasRequiredRole = false
    
    if (Array.isArray(role)) {
      hasRequiredRole = hasAnyRole(role)
    } else {
      hasRequiredRole = hasRole(role)
    }
    
    el.style.display = hasRequiredRole ? '' : 'none'
  }
}

/**
 * v-can-all directive - shows element only if user has ALL specified permissions
 * Usage: v-can-all="['edit-users', 'delete-users']"
 */
const vCanAll = {
  mounted(el, binding) {
    const { canAll } = usePermissions()
    const permissions = binding.value
    
    if (!Array.isArray(permissions)) {
      console.warn('v-can-all directive expects an array of permissions')
      return
    }
    
    const hasAllPermissions = canAll(permissions)
    
    if (!hasAllPermissions) {
      el.style.display = 'none'
    }
  },
  
  updated(el, binding) {
    const { canAll } = usePermissions()
    const permissions = binding.value
    
    if (!Array.isArray(permissions)) {
      return
    }
    
    const hasAllPermissions = canAll(permissions)
    el.style.display = hasAllPermissions ? '' : 'none'
  }
}

/**
 * v-admin directive - shows element only for admin users
 * Usage: v-admin
 */
const vAdmin = {
  mounted(el, binding) {
    const { isAdmin } = usePermissions()
    
    if (!isAdmin.value) {
      el.style.display = 'none'
    }
  },
  
  updated(el, binding) {
    const { isAdmin } = usePermissions()
    el.style.display = isAdmin.value ? '' : 'none'
  }
}

/**
 * v-super-admin directive - shows element only for super admin users
 * Usage: v-super-admin
 */
const vSuperAdmin = {
  mounted(el, binding) {
    const { isSuperAdmin } = usePermissions()
    
    if (!isSuperAdmin.value) {
      el.style.display = 'none'
    }
  },
  
  updated(el, binding) {
    const { isSuperAdmin } = usePermissions()
    el.style.display = isSuperAdmin.value ? '' : 'none'
  }
}

/**
 * Plugin to register all permission directives
 */
export default {
  install(app) {
    app.directive('can', vCan)
    app.directive('role', vRole)
    app.directive('can-all', vCanAll)
    app.directive('admin', vAdmin)
    app.directive('super-admin', vSuperAdmin)
  }
}

// Export individual directives for manual registration
export {
  vCan,
  vRole,
  vCanAll,
  vAdmin,
  vSuperAdmin
}