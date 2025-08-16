<template>
  <div class="auth-bg d-flex align-items-center justify-content-center min-vh-100">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'Auth',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      logoUrl: window.location.origin + '/logo.png',
      showPassword: false
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async login() {
        this.error = '';
        try {
          const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email: this.email, password: this.password })
          });
          const data = await res.json();
          if (res.ok && data.data && data.data.token) {
            localStorage.setItem('token', data.data.token);
            this.$router.push('/dashboard');
          } else {
            this.error = data.message || 'Falha no login.';
          }
        } catch (e) {
          this.error = 'Erro de conexão.';
        }
    },
    async register() {
      this.regError = '';
      if (this.regPassword !== this.regPasswordConf) {
        this.regError = 'As senhas não conferem.';
        return;
      }
      try {
        const res = await fetch('/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            name: this.regName,
            email: this.regEmail,
            password: this.regPassword,
            password_confirmation: this.regPasswordConf
          })
        });
        const data = await res.json();
        if (res.ok && data.data && data.data.token) {
          localStorage.setItem('token', data.data.token);
          this.$router.push('/dashboard');
        } else {
          this.regError = data.message || 'Falha no registro.';
        }
      } catch (e) {
        this.regError = 'Erro de conexão.';
      }
    },
    async recoverPassword() {
      this.forgotMsg = '';
      this.forgotError = '';
      try {
        const res = await fetch('/api/request-password-reset', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email: this.forgotEmail })
        });
        const data = await res.json();
        if (res.ok) {
          this.forgotMsg = data.message || 'Instruções exibidas na tela.';
        } else {
          this.forgotError = data.message || 'Falha na recuperação.';
        }
      } catch (e) {
        this.forgotError = 'Erro de conexão.';
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
/* Fundo inspirado no Adminkit */
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
