/**
 * Authentication Service
 * Handles authentication state and token management
 */
const AuthService = {
    // Get the authentication token
    getToken() {
        return localStorage.getItem('token');
    },
    
    // Save the authentication token
    setToken(token) {
        localStorage.setItem('token', token);
    },
    
    // Clear authentication data (logout)
    clearAuth() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    },
    
    // Check if user is authenticated
    isAuthenticated() {
        return !!this.getToken();
    },
    
    // Get current user info
    getCurrentUser() {
        const userStr = localStorage.getItem('user');
        return userStr ? JSON.parse(userStr) : null;
    },
    
    // Check if user has specific role
    hasRole(role) {
        const user = this.getCurrentUser();
        return user && user.role === role;
    },
    
    // Add token to headers for API requests
    getAuthHeaders() {
        const token = this.getToken();
        return token ? {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        } : {};
    },
    
    // Setup axios interceptors (if using axios)
    setupAxiosInterceptors(axios) {
        axios.interceptors.request.use(
            (config) => {
                const token = this.getToken();
                if (token) {
                    config.headers['Authorization'] = `Bearer ${token}`;
                }
                return config;
            },
            (error) => {
                return Promise.reject(error);
            }
        );
        
        // Handle token expiration
        axios.interceptors.response.use(
            (response) => response,
            (error) => {
                if (error.response && error.response.status === 401) {
                    this.clearAuth();
                    window.location.href = '/login';
                }
                return Promise.reject(error);
            }
        );
    }
};

// Export for usage in other scripts
window.AuthService = AuthService;