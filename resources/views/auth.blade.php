<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Test Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div id="vue-auth" class="container py-5"></div>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp, ref } = Vue;
createApp({
    setup() {
        const mode = ref('login');
        const token = ref("");
        const loading = ref(false);
        const error = ref("");
        const success = ref("");
        // Variáveis reativas
        const loginEmail = ref("");
        const loginPassword = ref("");
        const regName = ref("");
        const regEmail = ref("");
        const regPassword = ref("");
        const forgotEmail = ref("");
        const resetToken = ref("");
        const resetPassword = ref("");
        const resetPasswordConfirm = ref("");

        // Funções
        const login = async () => {
            loading.value = true;
            error.value = "";
            success.value = "";
            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: loginEmail.value, password: loginPassword.value })
                });
                const data = await res.json();
                if (res.ok && data.token) {
                    token.value = data.token;
                    success.value = data.message || 'Login realizado com sucesso!';
                } else {
                    error.value = data.message || 'Falha no login.';
                }
            } catch (e) {
                error.value = 'Erro de conexão.';
            }
            loading.value = false;
        };
        const register = async () => {
            loading.value = true;
            error.value = "";
            success.value = "";
            try {
                const res = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ name: regName.value, email: regEmail.value, password: regPassword.value })
                });
                const data = await res.json();
                if (res.ok) {
                    success.value = data.message || 'Registro realizado com sucesso!';
                } else {
                    error.value = data.message || 'Falha no registro.';
                }
            } catch (e) {
                error.value = 'Erro de conexão.';
            }
            loading.value = false;
        };
        const forgot = async () => {
            loading.value = true;
            error.value = "";
            success.value = "";
            try {
                const res = await fetch('/api/forgot-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: forgotEmail.value })
                });
                const data = await res.json();
                if (res.ok && data.data && data.data.reset_token) {
                    success.value = data.message + ' Token: ' + data.data.reset_token;
                    resetToken.value = data.data.reset_token;
                    mode.value = 'reset';
                } else {
                    error.value = data.message || 'Falha ao gerar token.';
                }
            } catch (e) {
                error.value = 'Erro de conexão.';
            }
            loading.value = false;
        };
        const reset = async () => {
            loading.value = true;
            error.value = "";
            success.value = "";
            try {
                const res = await fetch('/api/reset-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        email: forgotEmail.value,
                        token: resetToken.value,
                        password: resetPassword.value,
                        password_confirmation: resetPasswordConfirm.value
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    success.value = data.message || 'Senha redefinida com sucesso!';
                    mode.value = 'login';
                } else {
                    error.value = data.message || 'Falha ao redefinir senha.';
                }
            } catch (e) {
                error.value = 'Erro de conexão.';
            }
            loading.value = false;
        };
        const logout = async () => {
            loading.value = true;
            error.value = "";
            success.value = "";
            try {
                const res = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token.value,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    success.value = data.message || 'Logout realizado.';
                    token.value = "";
                    mode.value = 'login';
                } else {
                    error.value = data.message || 'Falha no logout.';
                }
            } catch (e) {
                error.value = 'Erro de conexão.';
            }
            loading.value = false;
        };

        return {
            mode, loading, error, success, token,
            loginEmail, loginPassword, login,
            regName, regEmail, regPassword, register,
            forgotEmail, forgot, logout,
            resetToken, resetPassword, resetPasswordConfirm, reset
        };
    },
    template: `
        <div class="container py-5">
            <h1 class="mb-4">Authentication Test Page</h1>
            <div v-if="token">
                <div class="alert alert-success">Logged in! Token: @{{ token }}</div>
                <button class="btn btn-danger mb-3" @click="logout">Logout</button>
            </div>
            <div class="mb-3">
                <button class="btn btn-outline-primary me-2" @click="mode='login'">Login</button>
                <button class="btn btn-outline-success me-2" @click="mode='register'">Register</button>
                <button class="btn btn-outline-warning" @click="mode='forgot'">Forgot Password</button>
            </div>
            <div v-if="loading" class="alert alert-info">Loading...</div>
            <div v-if="error" class="alert alert-danger">@{{ error }}</div>
            <div v-if="success" class="alert alert-success">@{{ success }}</div>
            <form v-if="mode==='login'" @submit.prevent="login" class="mb-4">
                <h3>Login</h3>
                <input type="email" v-model="loginEmail" class="form-control mb-2" placeholder="Email" required>
                <input type="password" v-model="loginPassword" class="form-control mb-2" placeholder="Password" required>
                <button class="btn btn-primary" type="submit">Login</button>
            </form>
            <form v-if="mode==='register'" @submit.prevent="register" class="mb-4">
                <h3>Register</h3>
                <input type="text" v-model="regName" class="form-control mb-2" placeholder="Name" required>
                <input type="email" v-model="regEmail" class="form-control mb-2" placeholder="Email" required>
                <input type="password" v-model="regPassword" class="form-control mb-2" placeholder="Password" required>
                <button class="btn btn-success" type="submit">Register</button>
            </form>
            <form v-if="mode==='forgot'" @submit.prevent="forgot" class="mb-4">
                <h3>Forgot Password</h3>
                <input type="email" v-model="forgotEmail" class="form-control mb-2" placeholder="Email" required>
                <button class="btn btn-warning" type="submit">Generate Reset Token</button>
            </form>
            <form v-if="mode==='reset'" @submit.prevent="reset" class="mb-4">
                <h3>Reset Password</h3>
                <div class="mb-2">
                    <label>Token</label>
                    <input type="text" v-model="resetToken" class="form-control" readonly>
                </div>
                <input type="password" v-model="resetPassword" class="form-control mb-2" placeholder="New Password" required>
                <input type="password" v-model="resetPasswordConfirm" class="form-control mb-2" placeholder="Confirm Password" required>
                <button class="btn btn-info" type="submit">Reset Password</button>
            </form>
        </div>
    `
}).mount('#vue-auth');
</script>
</body>
</html>
