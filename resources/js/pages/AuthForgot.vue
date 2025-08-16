<template>
  <div class="auth-forgot container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Recuperação de Senha</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitRecovery">
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" v-model="email" class="form-control" id="email" required />
              </div>
              <div class="mb-3">
                <label for="birthdate" class="form-label">Data de Nascimento</label>
                <input type="date" v-model="birthdate" class="form-control" id="birthdate" required />
              </div>
              <div v-if="error" class="alert alert-danger">{{ error }}</div>
              <button type="submit" class="btn btn-primary w-100">Verificar</button>
            </form>
            <div v-if="step === 2">
              <hr />
              <form @submit.prevent="submitNewPassword">
                <div class="mb-3">
                  <label for="password" class="form-label">Nova Senha</label>
                  <input type="password" v-model="password" class="form-control" id="password" required />
                </div>
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Confirme a Senha</label>
                  <input type="password" v-model="password_confirmation" class="form-control" id="password_confirmation" required />
                </div>
                <div v-if="error" class="alert alert-danger">{{ error }}</div>
                <button type="submit" class="btn btn-success w-100">Salvar Nova Senha</button>
              </form>
            </div>
            <div v-if="success" class="alert alert-success mt-3">{{ success }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthForgot',
  data() {
    return {
      email: '',
      birthdate: '',
      password: '',
      password_confirmation: '',
      error: '',
      success: '',
      step: 1,
      userId: null
    };
  },
  methods: {
    async submitRecovery() {
      this.error = '';
      this.success = '';
      try {
        const res = await fetch('/api/auth/verify-user', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: this.email, birthdate: this.birthdate })
        });
        const data = await res.json();
        if (res.ok && data.userId) {
          this.userId = data.userId;
          this.step = 2;
        } else {
          this.error = data.message || 'Dados não conferem.';
        }
      } catch (e) {
        this.error = 'Erro ao verificar dados.';
      }
    },
    async submitNewPassword() {
      this.error = '';
      this.success = '';
      if (this.password !== this.password_confirmation) {
        this.error = 'As senhas não conferem.';
        return;
      }
      try {
        const res = await fetch('/api/auth/reset-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ userId: this.userId, password: this.password, password_confirmation: this.password_confirmation })
        });
        const data = await res.json();
        if (res.ok) {
          this.success = 'Senha alterada com sucesso!';
          this.step = 1;
          this.email = '';
          this.birthdate = '';
          this.password = '';
          this.password_confirmation = '';
        } else {
          this.error = data.message || 'Erro ao alterar senha.';
        }
      } catch (e) {
        this.error = 'Erro ao alterar senha.';
      }
    }
  }
};
</script>

<style scoped>
.auth-forgot {
  min-height: 80vh;
}
</style>
