const Auth = {
    setToken(token) {
        localStorage.setItem('token', token);
        sessionStorage.setItem('token', token);
    },

    getToken() {
        return localStorage.getItem('token') || sessionStorage.getItem('token');
    },

    setUser(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    getUser() {
        try {
            return JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user') || '{}');
        } catch { return {}; }
    },

    getRole() {
        return this.getUser().role || null;
    },

    isLoggedIn() {
        return !!this.getToken();
    },

    isAdmin() {
        return this.isLoggedIn() && this.getRole() === 'admin';
    },

    isPetugas() {
        return this.isLoggedIn() && this.getRole() === 'petugas';
    },

    isKoordinator() {
        return this.isLoggedIn() && this.getRole() === 'koordinator';
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('user');
        window.location.href = '/admin/login';
    },

    requireLogin() {
        if (!this.isLoggedIn()) {
            window.location.href = '/admin/login';
            return false;
        }
        return true;
    },

    requireAdmin() {
        if (!this.requireLogin()) return false;
        if (!this.isAdmin()) {
            this.logout();
            return false;
        }
        return true;
    },

    requirePetugas() {
        if (!this.requireLogin()) return false;
        if (!this.isPetugas()) {
            this.logout();
            return false;
        }
        return true;
    },

    requireKoordinator() {
        if (!this.requireLogin()) return false;
        if (!this.isKoordinator()) {
            this.logout();
            return false;
        }
        return true;
    },

    requireAdminOrKoordinator() {
        if (!this.requireLogin()) return false;
        if (!this.isAdmin() && !this.isKoordinator()) {
            this.logout();
            return false;
        }
        return true;
    }
};
