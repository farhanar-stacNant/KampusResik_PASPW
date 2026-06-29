const API = {
    IS_LOCAL: (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'),
    BASE_URL: (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
        ? 'http://127.0.0.1:8000/api'
        : '',
    PROXY_URL: 'proxy.php',
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
        let url;
        if (this.IS_LOCAL) {
            url = this.BASE_URL + endpoint;
        } else {
            const cleanEndpoint = endpoint.startsWith('/') ? endpoint.substring(1) : endpoint;
            const [path, query] = cleanEndpoint.split('?');
            const params = new URLSearchParams(query || '');
            params.set('endpoint', path);
            url = this.PROXY_URL + '?' + params.toString();
        }
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
        let url;
        if (this.IS_LOCAL) {
            url = this.BASE_URL + endpoint;
        } else {
            url = this.PROXY_URL + '?endpoint=' + encodeURIComponent(endpoint);
        }
        try {
            const res = await fetch(url, { method: 'POST', headers, body: formData });
            const data = await res.json();
            return { ok: res.ok, status: res.status, data, body: data };
        } catch (err) {
            console.error('API Error:', err);
            return { ok: false, status: 0, data: null, error: err.message };
        }
    }
};
