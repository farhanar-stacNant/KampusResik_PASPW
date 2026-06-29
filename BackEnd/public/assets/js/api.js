const API = {
    BASE_URL: '/api',

    getToken() {
        return localStorage.getItem('token') || sessionStorage.getItem('token');
    },

    getHeaders(extra = {}) {
        const headers = {
            'Accept': 'application/json',
            ...extra
        };
        const token = this.getToken();
        if (token) headers['Authorization'] = 'Bearer ' + token;
        return headers;
    },

    async fetch(endpoint, options = {}) {
        const url = this.BASE_URL + endpoint;
        const config = {
            headers: this.getHeaders(options.headers),
            ...options
        };
        if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
            config.body = JSON.stringify(config.body);
            config.headers['Content-Type'] = 'application/json';
        }
        try {
            const res = await fetch(url, config);
            const data = await res.json();
            return { ok: res.ok, status: res.status, data, body: data };
        } catch (err) {
            console.error('API Error:', err);
            return { ok: false, status: 0, data: null, error: err.message };
        }
    },

    get(endpoint) {
        return this.fetch(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.fetch(endpoint, { method: 'POST', body });
    },

    put(endpoint, body) {
        return this.fetch(endpoint, { method: 'PUT', body });
    },

    delete(endpoint) {
        return this.fetch(endpoint, { method: 'DELETE' });
    },

    async postFormData(endpoint, formData) {
        const token = this.getToken();
        const headers = { 'Accept': 'application/json' };
        if (token) headers['Authorization'] = 'Bearer ' + token;
        try {
            const res = await fetch(this.BASE_URL + endpoint, { method: 'POST', headers, body: formData });
            const data = await res.json();
            return { ok: res.ok, status: res.status, data, body: data };
        } catch (err) {
            console.error('API Error:', err);
            return { ok: false, status: 0, data: null, error: err.message };
        }
    }
};
