import { defineStore } from "pinia";
export const useAuthStore=defineStore("auth",{
    state: () => ({
        user:null,
        token: null,
    }),
    actions: {
        setUSer(user){
            this.user=user
            this.isAuthenticated = !!user
        },
        setToken(token) {
            this.token = token
            if(token) {
                localStorage.setItem("token", token)
            }else {
                localStorage.removeItem("auth_token")
            }   
        },
        lougout() {
            this.user = null
            this.token = null
            this.isAuthenticated = false
            localStorage.removeItem("auth_token")
        }
    }
})