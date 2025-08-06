import api from "@/plugins/axios";
class USerRoleApiService{
    // get users permission
    async getUserPermissions(userId) {
        try {
            const response = await api.get(`/users/${userId}/permissions`);
            return {
                success: true,
                data: response.data,
                message: "User permissions fetched successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    //assign role to user
    async assignRoleToUser(userId, role){
        try {
            const response = await api.post(`/users/${userId}/roles`, { role });
            return {
                success:true,
                date:response.date,
                message:response.data.message || "Role assigned successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    // sync user role replace all roles with new role
    async syncUserRole(userId , role){
        try {
            const response=await api.put(`/users/${userId}/roles`, { role });
            return {
                success:true,
                data: response.data,
                message: response.data.message || "User role updated successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    //remove role from user
    async removeRoleFromUser(userid,roles){
        try {
            const response = await api.delete(`/users/${userid}/roles`, { data: { roles } });
            return {
                success: true,
                data: response.data,
                message: response.data.message || "Role removed successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    // assign permissions to user
    async assignPermissions(userId, permissions) {
        try {
            const response = await api.post(`/users/${userId}/permissions`, { permissions });
            return {
                success: true,
                data: response.data,
                message: response.data.message || 'Permissions assigned successfully'
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    // sync user permissions
    async syncPermissions(userId, permissions) {
        try {
            const response = await api.put(`/users/${userId}/permissions`, { permissions });
            return {
                success: true,
                data: response.data,
                message: response.data.message || "Permissions updated successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    
}