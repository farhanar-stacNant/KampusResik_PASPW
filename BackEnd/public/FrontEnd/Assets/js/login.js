document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const btnLogin = document.getElementById('btnLogin');
    const btnText = btnLogin.querySelector('.btn-text');
    const btnIcon = btnLogin.querySelector('.btn-icon');
    const btnSpinner = document.getElementById('btnSpinner');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    const API_BASE_URL = 'http://127.0.0.1:8000/api';
    const API_SECRET_TOKEN = 'KampusResik_Secret_Token_2026';

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Reset error
        hideError();
        
        // Validasi
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email || !password) {
            showError('Email dan kata sandi wajib diisi');
            return;
        }

        // Loading state
        setLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Api-Token': API_SECRET_TOKEN
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Simpan token & data user ke session
                sessionStorage.setItem('token', data.data.token);
                sessionStorage.setItem('user', JSON.stringify(data.data.user));
                
                const token = sessionStorage.getItem('token');
                if (!token) {window.location.href = '../login.php';}
                
                // Redirect berdasarkan role
                const role = data.data.user.role;
                if (role === 'admin') {
                    window.location.href = 'admin/dashboard.php';
                } else if (role === 'petugas') {
                    window.location.href = 'petugas/dashboard.php';
                } else {
                    showError('Akses ditolak. Hanya admin dan petugas yang boleh login.');
                }
            } else {
                showError(data.message || 'Email atau kata sandi salah');
                shakeForm();
            }

        } catch (error) {
            console.error('Login error:', error);
            showError('Gagal terhubung ke server. Periksa koneksi Anda.');
            shakeForm();
        } finally {
            setLoading(false);
        }
    });

    
    function showError(message) {
        errorText.textContent = message;
        errorMessage.style.display = 'flex';
    }

    function hideError() {
        errorMessage.style.display = 'none';
        errorText.textContent = '';
    }

    function setLoading(loading) {
        btnLogin.disabled = loading;
        if (loading) {
            btnText.style.opacity = '0';
            btnIcon.style.display = 'none';
            btnSpinner.style.display = 'block';
        } else {
            btnText.style.opacity = '1';
            btnIcon.style.display = 'inline';
            btnSpinner.style.display = 'none';
        }
    }

    function shakeForm() {
        loginForm.classList.add('shake');
        setTimeout(() => {
            loginForm.classList.remove('shake');
        }, 400);
    }

    // Simpan session ke PHP (opsional - untuk kompatibilitas)
    async function saveSession(data) {
        try {
            await fetch('api/session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: data.token,
                    user: data.user
                })
            });
        } catch (e) {
            console.log('Session save skipped');
        }
    }
});