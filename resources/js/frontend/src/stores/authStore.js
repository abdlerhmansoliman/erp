import { defineStore } from "pinia";
import api from "@/plugins/axios";

export const useAuthStore=defineStore('auth',{
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        isAuthenticated: false,
        error: null,
    }),
    getters: {
        isLoggedIn: (state) => !!state.token && !!state.user
    },
    actions:{
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/auth/login', credentials);
                if(response.data.success){
                    this.user=response.data.user
                    this.token=response.data.token
                    this.isAuthenticated=true;
                    localStorage.setItem('auth_token', this.token);
                    return{
                        success: true,
                        message:'Login successful',
                        data: response.data.data
                    }
                }
                else{
                    this.error=response.data.message
                return {
                    success: false,
                    message: response.data.message
                }
                }
            } catch (error) {
                this.error=error.response?.data?.message || 'Login failed';
                return{
                    success: false,
                    message: this.error,
                    errors: error.response?.data?.errors || {}
                }
            }finally{
                this.loading = false;
            }
        },
        
    }
})