import api from "@/plugins/axios";
import { data } from "autoprefixer";
class RoleApiService {
    // get all roles 
    async getAllRoles(){
        try {
            const response=await api.get("/roles")
            return{
                success: true,
                data: response.data,
                message: "Roles fetched successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    // git role by id
    async getRoleById(roleId){
        try {
            const response=await api.get(`/roles/${id}`)
            return{
                success:true,
                data:response.data,
                message: "Role fetched successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    // create a new role
    async createRole(roleDAte){
        try{
            const response= await api.post("/roles", roleDAte)  
            return{
                success:true,
                data: response.data,
                message: response.data.message || "Role created successfully"
            }
        }catch (error) {
            return this.handleError(error);
        }
    }
    // update a role
    async updateRode (id , roleData){
        try {
            const response=await api.put(`/roles/${id}`, roleData)
            return{
                success:true,
                data: response.data,
                message: response.data.message || "Role updated successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    // delete a role
    async deleteRole(id){
        try {
            const response=await api.delete(`/roles/${id}`)
            return {
                success: true,
                data: response.data,
                message: response.data.message || "Role deleted successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    //assign permissions to role 
    async assignPermissions(roleID, permissions){
        try {
            const response = await api.post(`/roles/${roleID}/permissions`, { permissions });
            return {
                success: true,
                data: response.data,
                message: "Permissions assigned successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    //get user by role
    async getUsersByRole(rolename){
        try {
            const response = await api.get(`/roles/${rolename}/users`);
            return{
                success:true,
                data:response.data,
                message: "Users fetched successfully"
            }
        } catch (error) {
            return this.handleError(error);
        }
    }
    // handle error
    handleError(error) {
        let errorMessage="un expected error occurred";
        let validationErrors = {};
        if(error.response){
            const {status, data} = error.response;
            errorMessage=data.message || `HTTP ERROR:${status}`;
            if(status===422 && data.errors){
                validationErrors = data.errors;
            }
            switch (status){
                case 401:
                    errorMessage = "Unauthorized access. Please log in again.";
                    break;
                    case 403:
                    errorMessage = "Forbidden. You do not have permission to perform this action.";
                    break;
                    case 404:
                    errorMessage = "Resource not found.";
                    break;
                    case 500:
                    errorMessage = "Internal server error. Please try again later.";
                    break;
                }

        }
            else if(error.request) {
                    errorMessage='Network error. Please check your connection.';
                }
                return{
                        success: false,
                        message: errorMessage,
                        errors: validationErrors,
                        status: error.response?.status || 0

                }
    }
}
export default new RoleApiService();