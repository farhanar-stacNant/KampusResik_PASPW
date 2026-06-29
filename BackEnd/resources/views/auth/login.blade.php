<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="/images/LOGO.png">
    <style>
        :root { --primary: #2e7d32; --primary-dark: #1b5e20; }
        body { font-family: 'Segoe UI', sans-serif; min-height: 100vh; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); }
        .login-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .login-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(46,125,50,0.15); width: 66.666%; max-width: 800px; min-width: 320px; overflow: hidden; }
        .login-header { background: linear-gradient(135deg, #2e7d32, #00897b); padding: 2rem; text-align: center; color: #fff; }
        .login-header .logo-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; padding: 8px; }
        .login-body { padding: 2rem; }
        .form-floating > .form-control { border-radius: 12px; border: 2px solid #e0e0e0; height: 56px; }
        .form-floating > .form-control:focus { border-color: #2e7d32; box-shadow: 0 0 0 0.2rem rgba(46,125,50,0.15); }
        .input-group-text { background: #e8f5e9; border: 2px solid #e0e0e0; border-right: none; color: #2e7d32; border-radius: 12px 0 0 12px; }
        .btn-login-submit { background: linear-gradient(135deg, #2e7d32, #00897b); color: #fff; font-weight: 600; padding: 0.875rem; border-radius: 12px; border: none; width: 100%; font-size: 1rem; }
        .btn-login-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46,125,50,0.3); }
        .role-selector { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .role-selector .btn-role { flex: 1; padding: 0.625rem; border: 2px solid #e0e0e0; background: #fff; border-radius: 10px; font-weight: 600; font-size: 0.9rem; color: #666; cursor: pointer; transition: all 0.2s; }
        .role-selector .btn-role.active { background: #2e7d32; border-color: #2e7d32; color: #fff; }
        .role-selector .btn-role i { font-size: 1.2rem; display: block; margin-bottom: 0.25rem; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-login { animation: fadeInUp 0.5s ease forwards; }
        @media (max-width: 768px) { .login-card { width: 90%; } .login-body { padding: 1.5rem; } }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card animate-login">
            <div class="login-header">
                <div class="logo-icon"><img src="/images/LOGO.png" alt="KampusResik" height="48"></div>
                <h4>KampusResik</h4>
                <p>Masuk ke akun Anda</p>
            </div>
            <div class="login-body">
                <div id="alertContainer"></div>
                <form id="loginForm">
                    <div class="role-selector">
                        <button type="button" class="btn-role active" data-role="petugas">
                            <i class="bi bi-person-badge"></i>Petugas
                        </button>
                        <button type="button" class="btn-role" data-role="admin">
                            <i class="bi bi-shield-lock"></i>Admin
                        </button>
                    </div>
                    <input type="hidden" name="role" id="roleInput" value="petugas">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-success"></i></span>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock text-success"></i></span>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login-submit" id="btnLogin">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/helpers.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const p = document.getElementById('password');
            const i = this.querySelector('i');
            if (p.type === 'password') { p.type = 'text'; i.classList.replace('bi-eye', 'bi-eye-slash'); }
            else { p.type = 'password'; i.classList.replace('bi-eye-slash', 'bi-eye'); }
        });

        document.querySelectorAll('.btn-role').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.btn-role').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('roleInput').value = this.dataset.role;
            });
        });

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('roleInput').value;

            const res = await API.post('/auth/login', { email, password });

            if (res.ok && res.data?.data?.token) {
                const token = res.data.data.token;
                const user = res.data.data.user || {};

                if (user.role !== role) {
                    Helpers.showAlert('Bro, Anda salah Halaman', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Masuk';
                    return;
                }

                Auth.setToken(token);
                Auth.setUser(user);

                if (user.role === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else {
                    window.location.href = '/petugas/dashboard';
                }
            } else {
                const msg = res.data?.message || 'Email atau password salah!';
                Helpers.showAlert(msg, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Masuk';
            }
        });
    </script>
</body>
</html>
