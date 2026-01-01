import axios from 'axios';

// Store token in memory (not in storage) for session-only authentication
// Store token in memory (not in storage) for session-only authentication
// We also persist it to `sessionStorage` so a session-only login survives a page reload
let sessionToken: string | null = null;

// Initialize sessionToken from sessionStorage (survives reloads, cleared when tab/window closes)
try {
    const storedSession = sessionStorage.getItem('token');
    if (storedSession) sessionToken = storedSession;
} catch (e) {
    // sessionStorage may be unavailable in some environments; fall back to in-memory only
}

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
    } else {
        // Check sessionStorage (session-only token that survives reload)
        const sessionStorageToken = sessionStorage.getItem('token');
        if (sessionStorageToken) {
            config.headers.Authorization = `Bearer ${sessionStorageToken}`;
        } else if (sessionToken) {
            // Fallback to in-memory token (rare: sessionStorage unavailable)
            config.headers.Authorization = `Bearer ${sessionToken}`;
        }
    }
    return config;
});

// Handle 401 responses - redirect to login
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Clear any stored auth state but do NOT force a global redirect.
            // Emit an event so the app can decide how to react (router guard will handle route-level redirects).
            localStorage.removeItem('token');
            try { sessionStorage.removeItem('token'); } catch (e) {}
            sessionToken = null;
            localStorage.removeItem('user');

            // Emit a global event so consuming code can react (e.g., show login or redirect if on protected page)
            window.dispatchEvent(new Event('api-unauthorized'));
        }
        return Promise.reject(error);
    }
);

// Export session token setter/getter for use in components
export function setSessionToken(token: string | null) {
    sessionToken = token;
    try {
        if (token) {
            sessionStorage.setItem('token', token);
        } else {
            sessionStorage.removeItem('token');
        }
    } catch (e) {
        // sessionStorage may be disabled/unavailable; in that case we keep in-memory only
    }
}

export function getSessionToken() {
    return sessionToken;
}

export default api;