import axios from 'axios';

// Store token in memory (not in storage) for session-only authentication
let sessionToken: string | null = null;

const api = axios.create({
    baseURL: 'http://localhost:8000',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    withCredentials: false
});

// Add interceptor to include auth token if available
api.interceptors.request.use((config) => {
    // Check localStorage first (persistent login)
    const persistentToken = localStorage.getItem('token');
    if (persistentToken) {
        config.headers.Authorization = `Bearer ${persistentToken}`;
    } else if (sessionToken) {
        // Otherwise use in-memory token (session-only login)
        config.headers.Authorization = `Bearer ${sessionToken}`;
    }
    return config;
});

// Handle 401 responses - redirect to login
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            sessionStorage.removeItem('token');
            sessionToken = null;
            localStorage.removeItem('user');
            
            // Only redirect if not already on login/register page
            const currentPath = window.location.pathname;
            if (currentPath !== '/login' && currentPath !== '/register') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

// Export session token setter/getter for use in components
export function setSessionToken(token: string | null) {
    sessionToken = token;
}

export function getSessionToken() {
    return sessionToken;
}

export default api;