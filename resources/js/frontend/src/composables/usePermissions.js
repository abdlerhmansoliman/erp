// composables/usePermissions.js
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore' // Assuming you have an auth store
import { useUserRoleStore } from '@/stores/userRoleStore'

export function usePermissions() {
  const authStore = useAuthStore()
  const userRoleStore = useUserRoleStore()
  
  // Get current user
  const currentUser = computed(() => authStore.user)
  const isAuthenticated = computed(() => authStore.isAuthenticated)

  /**
   * Check if current user has a specific permission
   */
  const can = (permission) => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    return userRoleStore.userHasPermission(currentUser.value.id, permission)
  }

  /**
   * Check if current user has a specific role
   */
  const hasRole = (role) => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    return userRoleStore.userHasRole(currentUser.value.id, role)
  }

  /**
   * Check if current user has any of the specified permissions
   */
  const canAny = (permissions) => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    return userRoleStore.userHasAnyPermission(currentUser.value.id, permissions)
  }

  /**
   * Check if current user has all specified permissions
   */
  const canAll = (permissions) => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    return userRoleStore.userHasAllPermissions(currentUser.value.id, permissions)
  }

  /**
   * Check if current user has any of the specified roles
   */
  const hasAnyRole = (roles) => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    return roles.some(role => userRoleStore.userHasRole(currentUser.value.id, role))
  }

  /**
   * Get current user's roles
   */
  const userRoles = computed(() => {
    if (!isAuthenticated.value || !currentUser.value) {
      return []
    }
    
    return userRoleStore.getUserRoleNames(currentUser.value.id)
  })

  /**
   * Get current user's permissions
   */
  const userPermissions = computed(() => {
    if (!isAuthenticated.value || !currentUser.value) {
      return []
    }
    
    return userRoleStore.getUserPermissionNames(currentUser.value.id)
  })

  /**
   * Predefined permission checks for common actions
   */
  const permissions = computed(() => ({
    // Role management
    roles: {
      view: can('view-roles'),
      create: can('create-roles'),
      edit: can('edit-roles'),
      delete: can('delete-roles')
    },
    
    // User management
    users: {
      view: can('view-users'),
      create: can('create-users'),
      edit: can('edit-users'),
      delete: can('delete-users'),
      manageRoles: can('manage-user-roles')
    },
    
    // Content management
    posts: {
      view: can('view-posts'),
      create: can('create-posts'),
      edit: can('edit-posts'),
      delete: can('delete-posts'),
      publish: can('publish-posts')
    },
    
    // System administration
    system: {
      viewDashboard: can('view-dashboard'),
      manageSettings: can('manage-settings'),
      viewLogs: can('view-logs'),
      backup: can('backup-system')
    }
  }))

  /**
   * Check if user is admin (has admin role)
   */
  const isAdmin = computed(() => hasRole('admin') || hasRole('super-admin'))

  /**
   * Check if user is super admin
   */
  const isSuperAdmin = computed(() => hasRole('super-admin'))

  /**
   * Check if user can access admin areas
   */
  const canAccessAdmin = computed(() => {
    return isAdmin.value || can('view-dashboard')
  })

  /**
   * Load user permissions if not already loaded
   */
  const loadUserPermissions = async () => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    try {
      await userRoleStore.fetchUserPermissions(currentUser.value.id)
      return true
    } catch (error) {
      console.error('Failed to load user permissions:', error)
      return false
    }
  }

  /**
   * Refresh user permissions
   */
  const refreshPermissions = async () => {
    if (!isAuthenticated.value || !currentUser.value) {
      return false
    }
    
    try {
      await userRoleStore.fetchUserPermissions(currentUser.value.id, true)
      return true
    } catch (error) {
      console.error('Failed to refresh user permissions:', error)
      return false
    }
  }

  return {
    // Permission checking functions
    can,
    hasRole,
    canAny,
    canAll,
    hasAnyRole,
    
    // Computed properties
    userRoles,
    userPermissions,
    permissions,
    isAdmin,
    isSuperAdmin,
    canAccessAdmin,
    
    // Utility functions
    loadUserPermissions,
    refreshPermissions
  }
}