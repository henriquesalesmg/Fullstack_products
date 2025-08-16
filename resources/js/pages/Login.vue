<template>
  <div class="auth-bg d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 1rem;">
      <div class="text-center mb-4">
        <img :src="logoUrl" alt="Logo" class="auth-logo" />
        <h2 class="fw-bold mb-2">Área de acesso</h2>
        <p class="text-muted mb-0">Seja bem vinda(o)!</p>
      </div>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input v-model="email" type="email" id="email" class="form-control form-control-lg" required />
        </div>
        <div class="mb-3 position-relative">
          <label for="password" class="form-label">Senha</label>
          <div class="input-group">
            <input v-model="password" :type="showPassword ? 'text' : 'password'" id="password" class="form-control form-control-lg" required />
            <button type="button" class="btn btn-outline-secondary btn-lg" style="border-top-left-radius:0;border-bottom-left-radius:0;" @click="togglePassword">
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>
  <div v-if="$route.query.success" class="alert alert-success mt-2">{{ $route.query.success }}</div>
  <div v-if="error" class="alert alert-danger mt-2">{{ error }}</div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Entrar</button>
      </form>
      <div class="d-flex justify-content-center gap-2 mt-3">
        <router-link to="/auth/register" class="btn btn-outline-secondary btn-sm">Registre-se</router-link>
        <router-link to="/auth/forgot" class="btn btn-outline-secondary btn-sm">Recuperar senha</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      logoUrl: window.location.origin + '/logo.png',
      showPassword: false,
      loginAttempts: 0,
      loginBlockedUntil: null
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async login() {
      this.error = '';
      const now = Date.now();
      if (this.loginBlockedUntil && now < this.loginBlockedUntil) {
        const seconds = Math.ceil((this.loginBlockedUntil - now) / 1000);
        this.error = `Muitas tentativas. Tente novamente em ${seconds} segundos.`;
        return;
      }
      try {
        const res = await fetch('/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email: this.email, password: this.password })
        });
        const data = await res.json();
        if (res.ok && data.data && data.data.token) {
          localStorage.setItem('token', data.data.token);
          this.loginAttempts = 0;
          this.loginBlockedUntil = null;
          this.$router.push('/dashboard');
        } else {
          this.loginAttempts++;
          if (this.loginAttempts >= 5) {
            this.loginBlockedUntil = now + 60 * 1000; // 1 minuto de bloqueio
            this.error = 'Muitas tentativas. Tente novamente em 60 segundos.';
          } else {
            this.error = data.message || 'Falha no login.';
          }
        }
      } catch (e) {
        this.loginAttempts++;
        if (this.loginAttempts >= 5) {
          this.loginBlockedUntil = now + 60 * 1000;
          this.error = 'Muitas tentativas. Tente novamente em 60 segundos.';
        } else {
          this.error = 'Erro de conexão.';
        }
      }
    }
  }
}
</script>

<style scoped>
.auth-logo {
  height: 160px;
  width: auto;
  margin-bottom: 0.7rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
.auth-bg {
  background: #f5f6fa;
  min-height: 100vh;
}
.auth-card {
  border-radius: 1rem;
  box-shadow: 0 0 32px 0 rgba(0,0,0,0.08);
  background: #fff;
}
</style>
