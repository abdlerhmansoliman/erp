import api from "@/plugins/axios";
class UserRoleApiService{
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
    // assign permission to multiple user
    async bulkAssignPermissions(userIds,permissions) {
        try {
            const response = await api.post(`/users/bulk/assign-roles`, { userIds, permissions });
            return {
                success: true,
                data: response.data,
                message: response.data.message || "Permissions assigned to users successfully"
            }
        } catch (error) {
            return this.errorHandler(error);
        }
    }
    // Error handler
    handleError(error) {
        let message = "An error occurred";
        let validationErrors = {};
        if(error.response){
            const {status,data} = error.response;
            error=data.response || `HTTP Error: ${status}`;
            if (data.validationErrors) {
                validationErrors = data.validationErrors;
            }
            switch (status) {
                case 400:
                    message = "Bad Request: " + (data.message || "Invalid data provided");
                    break;
                case 401:
                    message = "Unauthorized: Please log in again";
                    break;
                case 403:
                    message = "Forbidden: You do not have permission to perform this action";
                    break;
                case 404:
                    message = "Not Found: The requested resource could not be found";
                    break;
                case 500:
                    message = "Internal Server Error: Please try again later";
                    break;
                default:
                    message = data.message || "An unexpected error occurred";
            }
        }
            else if(error.request) {
                error = "Network Error: Unable to reach the server";
            }
            return{
                success: false,
                message: message,
                errors:validationErrors,
                status: error.response?.status || 0
            }
        
    }
}
export default new UserRoleApiService();