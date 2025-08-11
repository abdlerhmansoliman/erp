import axios from "axios";
const api = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true
})
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("auth_token");
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
)
api.interceptors.response.use(
    (response)=>{
        return response;
    },
    (error) =>{
        if(error.response ?. status === 401) {
            localStorage.removeItem("auth_token");
            window.location.href = '/login';
        }
        if(error.response ?. status === 403) {
            alert("You do not have permission to perform this action.");
        }
        return Promise.reject(error);
    }

)

export default api;