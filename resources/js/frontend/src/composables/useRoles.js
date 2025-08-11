import {ref , computed} from 'vue';
import { useRoleStore } from '@/stores/roleStore';
import { useNotification } from '@/composables/useNotification'

export function useRoles(){
    const roleStore= useRoleStore();
  const { showNotification } = useNotification()

    const selectedRole = ref(null);
    const isFormModelOpen= ref(false);
    const isDeleteConfirmOpen = ref(false);
    const roleToDelete = ref(null);

    const roles=computed(() => roleStore.roles);
    const permissions=computed(() => roleStore.permissions);
    const loading=computed(()=> roleStore.loading);
    const error=computed(()=>roleStore.error);
    const roleOptions=computed(()=>roleStore.roleOptions);
    const permissionOptions=computed(()=>roleStore.permissionOptions);
    const groupedPermissions=computed(()=>roleStore.groupedPermissions);
    const rolesCount=computed(()=> roleStore.rolesCount);
    const permissionsCount=computed(()=> roleStore.permissionsCount);

    const fetchRoles= async()=>{
        const result = await roleStore.fetchRoles();
        if(!result.success){
            showNotification({
                type: 'error',
                title: 'Error',
                message: result.message || 'Failed to fetch roles'
            })
        }
        return result;
    }
    //create role
    const createRole=async(roleData)=>{
        const result= await roleStore.createRole(roleData)
        if(result.success){
            showNotification({
                type: 'success',
                title: 'Success',
                message: 'Role created successfully'
            })
            closeFormModel();
        }
        else{
            showNotification({
                type: 'error',
                title: 'Error',
                message: result.message || 'Failed to create role'
            })
        }
        return result;
    }
    //update role
    const updateRole=async(id , roleData)=>{
        const result=await roleStore.updateRole(id, roleData)
        if(result.success){
            showNotification({
                type: 'success',
                title: 'Success',
                message: 'Role updated successfully'
            })
            closeFormModel();
        }
        else{
            showNotification({
                type: 'error',
                title: 'Error',
                message: result.message || 'Failed to update role'
            })
        }
        return result;
    }
    //delete role
    const deleteRole=async(id)=>{
        const result=await roleStore.deleteRole(id)
        if(result.success){
            showNotification({
                type: 'success',
                title: 'Success',
                message: 'Role deleted successfully'
            })
            closeDeleteModel();
        }
        else{
            showNotification({
                type: 'error',
                title: 'Error',
                message: result.message || 'Failed to delete role'
            })
        }
        return result;
    }
    //assign permissions
    const assignPermissions=async(roleId, permissions)=>{
        const result=await roleStore.assignPermissions(roleId, permissions)
        if(result.success){
            showNotification({
                type: 'success',
                title: 'Success',
                message: 'Permissions assigned successfully'
            })
        }
        else{
            showNotification({
                type: 'error',
                title: 'Error',
                message: result.message || 'Failed to assign permissions'
            })
        }
        return result;
    }
    const openCreateModal=()=>{
        selectedRole.value = null;
        isFormModelOpen.value = true;
    }
    const openEditModal=(role)=>{
        selectedRole.value = role;
        isFormModelOpen.value = true;
    }
    const closeFormModel=()=>{
        isFormModelOpen.value = false;
        selectedRole.value = null;
    }
    const closeDeleteConfirm=(role)=>{
        roleToDelete.value = null;
        isDeleteConfirmOpen.value = false;
    }
  const confirmDelete = async () => {
    if (roleToDelete.value) {
      await deleteRole(roleToDelete.value.id)
     }
    }
      const getRoleById = (id) => roleStore.getRoleById(id)
  const getRoleByName = (name) => roleStore.getRoleByName(name)
  const hasRole = (roleName) => roleStore.hasRole(roleName)
  
  const formatRoleForDisplay = (role) => {
    return {
      ...role,
      displayName: role.name.charAt(0).toUpperCase() + role.name.slice(1).replace(/-/g, ' '),
      permissionCount: role.permissions?.length || 0,
      permissionList: role.permissions?.map(p => p.name).join(', ') || 'No permissions'
    }
  }

  const validateRoleData = (roleData) => {
    const errors = {}
    
    if (!roleData.name || roleData.name.trim().length === 0) {
      errors.name = 'Role name is required'
    } else if (roleData.name.trim().length < 2) {
      errors.name = 'Role name must be at least 2 characters'
    } else if (!/^[a-zA-Z0-9\s\-_]+$/.test(roleData.name)) {
      errors.name = 'Role name can only contain letters, numbers, spaces, hyphens, and underscores'
    }

    // Check for duplicate role name (excluding current role if editing)
    const existingRole = getRoleByName(roleData.name.toLowerCase())
    if (existingRole && (!selectedRole.value || existingRole.id !== selectedRole.value.id)) {
      errors.name = 'Role name already exists'
    }

    if (roleData.permissions && !Array.isArray(roleData.permissions)) {
      errors.permissions = 'Permissions must be an array'
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    }
  }

  const clearError = () => {
    roleStore.clearError()
  }

  return {
    // State
    roles,
    permissions,
    loading,
    error,
    selectedRole,
    isFormModalOpen,
    isDeleteConfirmOpen,
    roleToDelete,
    
    // Computed
    roleOptions,
    permissionOptions,
    groupedPermissions,
    rolesCount,
    permissionsCount,
    
    // Methods
    fetchRoles,
    createRole,
    updateRole,
    deleteRole,
    assignPermissions,
    
    // Modal methods
    openCreateModal,
    openEditModal,
    closeFormModal,
    openDeleteConfirm,
    closeDeleteConfirm,
    confirmDelete,
    
    // Utility methods
    getRoleById,
    getRoleByName,
    hasRole,
    formatRoleForDisplay,
    validateRoleData,
    clearError
  }
}