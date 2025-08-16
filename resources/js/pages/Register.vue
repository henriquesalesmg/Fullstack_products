<template>
  <div class="auth-bg d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 1rem;">
      <div class="text-center mb-4">
        <img :src="logoUrl" alt="Logo" class="auth-logo" />
        <h2 class="fw-bold mb-2">Registrar</h2>
        <p class="text-muted mb-0">Crie sua conta</p>
      </div>
      <form @submit.prevent="register">
          <div v-if="loading" class="mb-3 text-center">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
            <div class="mt-2">Cadastro sendo realizado, aguarde...</div>
          </div>
        <div class="mb-3">
          <label for="name" class="form-label">Nome completo</label>
          <input v-model="name" type="text" id="name" class="form-control form-control-lg" required />
        </div>
        <div class="mb-3">
          <label for="birth_date" class="form-label">Data de nascimento</label>
          <input v-model="birthDate" type="date" id="birth_date" class="form-control form-control-lg" required />
        </div>
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
        <div class="mb-3">
          <label for="passwordConf" class="form-label">Confirme a senha</label>
          <input v-model="passwordConf" :type="showPassword ? 'text' : 'password'" id="passwordConf" class="form-control form-control-lg" required />
        </div>
        <div v-if="error" class="alert alert-danger mt-2">{{ error }}</div>
        <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Registrar</button>
      </form>
      <div class="d-flex justify-content-center gap-2 mt-3">
        <router-link to="/auth" class="btn btn-outline-secondary btn-sm">Voltar ao login</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
  name: '',
  email: '',
  birthDate: '',
  password: '',
  passwordConf: '',
  error: '',
  logoUrl: window.location.origin + '/logo.png',
  showPassword: false,
  loading: false
    }
  },
  methods: {
    validatePassword(password) {
      // Mínimo 6 caracteres, 1 número, 1 especial, 1 maiúscula
      const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{6,}$/;
      return regex.test(password);
    },
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async register() {
      this.error = '';
        this.loading = true;
      if (this.password !== this.passwordConf) {
        this.error = 'As senhas não conferem.';
          this.loading = false;
        return;
      }
      if (!this.validatePassword(this.password)) {
        this.error = 'A senha deve ter no mínimo 6 caracteres, incluindo 1 número, 1 caractere especial e 1 letra maiúscula.';
          this.loading = false;
        return;
      }
      try {
        const res = await fetch('/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            name: this.name,
            email: this.email,
            birth_date: this.birthDate,
            password: this.password,
            password_confirmation: this.passwordConf
          })
        });
        const data = await res.json();
        if (res.ok && data.data && data.data.token) {
          localStorage.setItem('token', data.data.token);
          this.$router.push('/dashboard');
        } else {
          this.error = data.message || 'Falha no registro.';
        }
      } catch (e) {
        this.error = 'Erro de conexão.';
      }
        this.loading = false;
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
