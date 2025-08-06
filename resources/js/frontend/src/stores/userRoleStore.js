// stores/userRoleStore.js
import { defineStore } from 'pinia'
import userRoleApiService from '@/services/userRoleApiService'

export const useUserRoleStore = defineStore('userRole', {
  state: () => ({
    userPermissions: {},
    loading: false,
    error: null,
    bulkOperationResults: null
  }),

  getters: {
    // Get user permissions by user ID
    getUserPermissions: (state) => (userId) => {
      return state.userPermissions[userId] || null
    },
    
    // Check if user has specific role
    userHasRole: (state) => (userId, roleName) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.roles) return false
      
      return userData.roles.some(role => role.name === roleName)
    },
    
    // Check if user has specific permission
    userHasPermission: (state) => (userId, permissionName) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.all_permissions) return false
      
      return userData.all_permissions.some(permission => permission.name === permissionName)
    },
    
    // Check if user has any of the specified permissions
    userHasAnyPermission: (state) => (userId, permissionNames) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.all_permissions) return false
      
      return permissionNames.some(permissionName => 
        userData.all_permissions.some(permission => permission.name === permissionName)
      )
    },
    
    // Check if user has all specified permissions
    userHasAllPermissions: (state) => (userId, permissionNames) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.all_permissions) return false
      
      return permissionNames.every(permissionName => 
        userData.all_permissions.some(permission => permission.name === permissionName)
      )
    },
    
    // Get user roles as array of role names
    getUserRoleNames: (state) => (userId) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.roles) return []
      
      return userData.roles.map(role => role.name)
    },
    
    // Get user permission names as array
    getUserPermissionNames: (state) => (userId) => {
      const userData = state.userPermissions[userId]
      if (!userData || !userData.all_permissions) return []
      
      return userData.all_permissions.map(permission => permission.name)
    }
  },

  actions: {
    /**
     * Fetch user's roles and permissions
     */
    async fetchUserPermissions(userId, forceRefresh = false) {
      // Use cache if available and not forcing refresh
      if (!forceRefresh && this.userPermissions[userId]) {
        return {
          success: true,
          data: this.userPermissions[userId]
        }
      }

      this.loading = true
      this.error = null

      try {
        const result = await userRoleApiService.getUserPermissions(userId)
        
        if (result.success) {
          // Store user permissions data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to fetch user permissions'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Assign roles to user
     */
    async assignRoles(userId, roles) {
      this.loading = true
      this.error = null
      
      try {
        const result = await userRoleApiService.assignRoles(userId, roles)
        
        if (result.success) {
          // Update stored user data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to assign roles'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Sync user roles (replace all existing roles)
     */
    async syncRoles(userId, roles) {
      this.loading = true
      this.error = null
      
      try {
        const result = await userRoleApiService.syncRoles(userId, roles)
        
        if (result.success) {
          // Update stored user data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to sync roles'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Remove specific roles from user
     */
    async removeRoles(userId, roles) {
      this.loading = true
      this.error = null
      
      try {
        const result = await userRoleApiService.removeRoles(userId, roles)
        
        if (result.success) {
          // Update stored user data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to remove roles'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Assign direct permissions to user
     */
    async assignPermissions(userId, permissions) {
      this.loading = true
      this.error = null
      
      try {
        const result = await userRoleApiService.assignPermissions(userId, permissions)
        
        if (result.success) {
          // Update stored user data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to assign permissions'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Sync user permissions (replace all existing direct permissions)
     */
    async syncPermissions(userId, permissions) {
      this.loading = true
      this.error = null
      
      try {
        const result = await userRoleApiService.syncPermissions(userId, permissions)
        
        if (result.success) {
          // Update stored user data
          this.userPermissions[userId] = result.data
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to sync permissions'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Bulk assign role to multiple users
     */
    async bulkAssignRole(userIds, role) {
      this.loading = true
      this.error = null
      this.bulkOperationResults = null
      
      try {
        const result = await userRoleApiService.bulkAssignRole(userIds, role)
        
        if (result.success) {
          this.bulkOperationResults = result.data
          
          // Clear cache for affected users to force refresh
          userIds.forEach(userId => {
            delete this.userPermissions[userId]
          })
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to bulk assign role'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Clear error state
     */
    clearError() {
      this.error = null
    },

    /**
     * Clear bulk operation results
     */
    clearBulkResults() {
      this.bulkOperationResults = null
    },

    /**
     * Remove user from cache
     */
    removeUserFromCache(userId) {
      delete this.userPermissions[userId]
    },

    /**
     * Clear all cached user data
     */
    clearCache() {
      this.userPermissions = {}
    },

    /**
     * Reset store to initial state
     */
    $reset() {
      this.userPermissions = {}
      this.loading = false
      this.error = null
      this.bulkOperationResults = null
    }
  }
})