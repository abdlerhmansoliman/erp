// stores/roleStore.js
import { defineStore } from 'pinia'
import roleApiService from '@/services/roleApiService'

export const useRoleStore = defineStore('role', {
  state: () => ({
    roles: [],
    permissions: [],
    currentRole: null,
    loading: false,
    error: null,
    lastFetch: null,
    cacheTimeout: 5 * 60 * 1000 // 5 minutes
  }),

  getters: {
    // Get total count of roles
    rolesCount: (state) => state.roles.length,
    
    // Get total count of permissions
    permissionsCount: (state) => state.permissions.length,
    
    // Get roles formatted for select options
    roleOptions: (state) => {
      return state.roles.map(role => ({
        value: role.name,
        label: role.name.charAt(0).toUpperCase() + role.name.slice(1),
        id: role.id,
        permissionCount: role.permissions?.length || 0
      }))
    },
    
    // Get permissions formatted for select options
    permissionOptions: (state) => {
      return state.permissions.map(permission => ({
        value: permission.name,
        label: permission.name.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        id: permission.id
      }))
    },
    
    // Group permissions by module (e.g., user-create, user-edit -> user module)
    groupedPermissions: (state) => {
      const grouped = {}
      
      state.permissions.forEach(permission => {
        const parts = permission.name.split('-')
        const action = parts[0] // create, view, edit, delete
        const module = parts.slice(1).join('-') || 'general' // users, posts, etc.
        
        if (!grouped[module]) {
          grouped[module] = {
            name: module,
            label: module.charAt(0).toUpperCase() + module.slice(1),
            permissions: []
          }
        }
        
        grouped[module].permissions.push({
          ...permission,
          action,
          actionLabel: action.charAt(0).toUpperCase() + action.slice(1)
        })
      })
      
      return grouped
    },
    
    // Check if cache is still valid
    isCacheValid: (state) => {
      if (!state.lastFetch) return false
      return (Date.now() - state.lastFetch) < state.cacheTimeout
    },
    
    // Get role by ID
    getRoleById: (state) => (id) => {
      return state.roles.find(role => role.id === id)
    },
    
    // Get role by name
    getRoleByName: (state) => (name) => {
      return state.roles.find(role => role.name === name)
    },
    
    // Check if a role exists
    hasRole: (state) => (roleName) => {
      return state.roles.some(role => role.name === roleName)
    }
  },

  actions: {
    /**
     * Fetch all roles and permissions
     */
    async fetchRoles(forceRefresh = false) {
      // Use cache if valid and not forcing refresh
      if (!forceRefresh && this.isCacheValid && this.roles.length > 0) {
        return {
          success: true,
          data: { roles: this.roles, permissions: this.permissions }
        }
      }

      this.loading = true
      this.error = null

      try {
        const result = await roleApiService.getAllRoles()
        
        if (result.success) {
          this.roles = result.data.roles || []
          this.permissions = result.data.permissions || []
          this.lastFetch = Date.now()
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to fetch roles'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch a specific role by ID
     */
    async fetchRoleById(id) {
      this.loading = true
      this.error = null
      
      try {
        const result = await roleApiService.getRoleById(id)
        
        if (result.success) {
          this.currentRole = result.data
          
          // Update the role in the roles array if it exists
          const index = this.roles.findIndex(role => role.id === id)
          if (index !== -1) {
            this.roles[index] = result.data
          }
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to fetch role'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Create a new role
     */
    async createRole(roleData) {
      this.loading = true
      this.error = null
      
      try {
        const result = await roleApiService.createRole(roleData)
        
        if (result.success) {
          // Add new role to the store
          this.roles.push(result.data)
          
          // Refresh to get updated data with relationships
          await this.fetchRoles(true)
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to create role'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Update an existing role
     */
    async updateRole(id, roleData) {
      this.loading = true
      this.error = null
      
      try {
        const result = await roleApiService.updateRole(id, roleData)
        
        if (result.success) {
          // Update role in the store
          const index = this.roles.findIndex(role => role.id === id)
          if (index !== -1) {
            this.roles[index] = result.data
          }
          
          // Update current role if it's the one being edited
          if (this.currentRole && this.currentRole.id === id) {
            this.currentRole = result.data
          }
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to update role'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Delete a role
     */
    async deleteRole(id) {
      this.loading = true
      this.error = null
      
      try {
        const result = await roleApiService.deleteRole(id)
        
        if (result.success) {
          // Remove role from the store
          this.roles = this.roles.filter(role => role.id !== id)
          
          // Clear current role if it's the one being deleted
          if (this.currentRole && this.currentRole.id === id) {
            this.currentRole = null
          }
        } else {
          this.error = result.message
        }
        
        return result
      } catch (error) {
        this.error = error.message || 'Failed to delete role'
        return {
          success: false,
          message: this.error
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Assign permissions to a role
     */
    async assignPermissions(roleId, permissions) {
      this.loading = true
      this.error = null
      
      try {
        const result = await roleApiService.assignPermissions(roleId, permissions)
        
        if (result.success) {
          // Update role in the store
          const index = this.roles.findIndex(role => role.id === roleId)
          if (index !== -1) {
            this.roles[index] = result.data
          }
          
          // Update current role if it's the one being updated
          if (this.currentRole && this.currentRole.id === roleId) {
            this.currentRole = result.data
          }
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
     * Clear error state
     */
    clearError() {
      this.error = null
    },

    /**
     * Set current role
     */
    setCurrentRole(role) {
      this.currentRole = role
    },

    /**
     * Clear current role
     */
    clearCurrentRole() {
      this.currentRole = null
    },

    /**
     * Clear cache
     */
    clearCache() {
      this.lastFetch = null
    },

    /**
     * Reset store to initial state
     */
    $reset() {
      this.roles = []
      this.permissions = []
      this.currentRole = null
      this.loading = false
      this.error = null
      this.lastFetch = null
    }
  }
})