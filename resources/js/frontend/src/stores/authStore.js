import { defineStore } from "pinia";
import api from "@/plugins/axios";

export const useAuthStore=defineStore('auth',{
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token'),
        isAuthenticated: false,
        error: null,
        loading: false,
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
                const data = response.data;

                if(data && data.success){
                    this.user = data.user;
                    this.token = data.token;
                    this.isAuthenticated = true;
                    localStorage.setItem('auth_token', this.token);
                    
                    return {
                        success: true,
                        message: 'Login successful',
                        data: data.user
                    };
                }
                
                this.error = data?.message || 'Invalid credentials';
                return {
                    success: false,
                    message: this.error
                };
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Login failed';
                this.error = errorMessage;
                return {
                    success: false,
                    message: errorMessage,
                    errors: error.response?.data?.errors || {}
                };
            } finally {
                this.loading = false;
            }
        },
        async register(userData){
            this.loading=true;
            this.error=null;
            try {
                const response=await api.post('/auth/register', userData)
                if(response.data.success){
                    this.token=response.data.token;
                    this.user=response.data.user
                    this.isAuthenticated=true;
                    localStorage.setItem('auth_token', this.token);
                    return{
                        success: true,
                        message: 'Registration successful',
                        data: response.data.data
                    }
                }
                else{
                    this.error=response.data.message;
                    return {
                        success: false,
                        message: response.data.message
                    }
                }
            } catch (error) {
                    this.error=error.response?.data?.message || 'Registration failed';
                    return{
                        success:false,
                        message: this.error,
                        errors: error.response?.data?.errors || {}
                    }
            }
            finally{
                this.loading = false;
            }
        },
        async logout(){
            this.loading=true;
            try {
                await api.post('/auth/logout');
            } catch (error) {
                console.error('Logout failed:', error);
            }finally {
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;
                localStorage.removeItem('auth_token');
                this.loading = false;
            }
        },
        async fetchUser() {
            if(!this.token) return;
            try {
                const response=await api.get('/auth/user');
                if(response.data.success){
                    this.user=response.data.data;
                    this.isAuthenticated=true;
                } 
                else{
                    this.logout();
                }
            } catch (error) {
                    console.error('Fetch user error:', error)
                    this.logout()
            }
            finally{
                this.loading = false;
            }
        },
         async initializeAuth() {
            const token = localStorage.getItem('auth_token')
            if (token) {
                this.token = token
                this.isAuthenticated = true
                await this.fetchUser()
            } else {
                this.token = null
                this.user = null
                this.isAuthenticated = false
            }
         },

    clearError() {
      this.error = null
    }
     
    }
})