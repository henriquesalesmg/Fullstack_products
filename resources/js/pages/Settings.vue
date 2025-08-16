<template>
  <div class="d-flex">
    <Menu @sidebar-toggle="handleSidebarToggle" />
    <div :class="['flex-grow-1', 'settings-page', { 'settings-expanded': isSidebarCollapsed }]">
      <div class="container-fluid">
        <h2 class="fw-bold mb-4"><i class="fas fa-cog me-2"></i> Configurações</h2>
        <div class="row">
          <div class="col-md-6">
            <form @submit.prevent="submitSettings" class="card shadow-sm p-4" style="max-width:500px;">
              <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input v-model="form.name" type="text" id="name" class="form-control" required />
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input v-model="form.email" type="email" id="email" class="form-control" required />
                <div v-if="errors.email" class="text-danger small">{{ errors.email }}</div>
              </div>
              <div class="mb-3">
                <label for="currentPassword" class="form-label">Senha atual</label>
                <input v-model="form.currentPassword" type="password" id="currentPassword" class="form-control" required />
                <div v-if="errors.currentPassword" class="text-danger small">{{ errors.currentPassword }}</div>
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">Nova senha</label>
                <input v-model="form.newPassword" type="password" id="newPassword" class="form-control" required />
                <div v-if="errors.newPassword" class="text-danger small">{{ errors.newPassword }}</div>
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirme a nova senha</label>
                <input v-model="form.confirmPassword" type="password" id="confirmPassword" class="form-control" required />
                <div v-if="errors.confirmPassword" class="text-danger small">{{ errors.confirmPassword }}</div>
              </div>
              <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Salvar</button>
              </div>
              <div v-if="successMessage" class="alert alert-success mt-3">{{ successMessage }}</div>
              <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
            </form>
            <div class="text-center mt-4">
              <button type="button" class="btn btn-danger" @click="deleteAccount">
                Excluir Conta
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card shadow-sm p-4 mb-4">
              <h5 class="fw-bold mb-3">Informações da Conta</h5>
              <p>Você está cadastrado desde <b>{{ formattedCreatedAt }}</b></p>
              <hr>
              <p>Para novas solicitações, entre em contato com o suporte:<br>
                <a href="https://wa.link/tqs0xb" target="_blank" class="btn btn-success btn-sm mt-2"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
              </p>
              <p>Nos envie um e-mail: <a href="mailto:hsmgop@gmail.com">hsmgop@gmail.com</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Menu from '../components/Menu.vue';
export default {
  name: 'Settings',
  components: { Menu },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      errors: {},
      successMessage: '',
      errorMessage: '',
      createdAt: '',
      isSidebarCollapsed: false
    };
  },
  computed: {
    formattedCreatedAt() {
      if (!this.createdAt) return '';
      // Tenta formatar para d/m/Y
      const date = new Date(this.createdAt);
      if (isNaN(date)) return this.createdAt;
      const dia = String(date.getDate()).padStart(2, '0');
      const mes = String(date.getMonth() + 1).padStart(2, '0');
      const ano = date.getFullYear();
      return `${dia}/${mes}/${ano}`;
    }
  },
  methods: {
    handleSidebarToggle(collapsed) {
      this.isSidebarCollapsed = collapsed;
    },
    async loadUserData() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('/api/user', {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });
        if (response.ok) {
          const result = await response.json();
          if (result.data) {
            this.form.name = result.data.name;
            this.form.email = result.data.email;
            this.createdAt = result.data.created_at;
          }
        }
      } catch (e) {
        // erro ao carregar dados do usuário
      }
    },
    async submitSettings() {
      this.errors = {};
      // Validação frontend
      if (!this.validateEmail(this.form.email)) {
        this.errors.email = 'E-mail inválido ou já cadastrado.';
        return;
      }
      if (!this.validatePassword(this.form.newPassword)) {
        this.errors.newPassword = 'A senha deve ter no mínimo 6 caracteres, incluindo 1 número, 1 caractere especial e 1 letra maiúscula.';
        return;
      }
      if (this.form.newPassword !== this.form.confirmPassword) {
        this.errors.confirmPassword = 'A confirmação de senha não confere.';
        return;
      }
      if (!this.form.currentPassword) {
        this.errors.currentPassword = 'Informe sua senha atual.';
        return;
      }
      // Enviar para backend
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('/api/settings', {
          method: 'PUT',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            name: this.form.name,
            email: this.form.email,
            currentPassword: this.form.currentPassword,
            password: this.form.newPassword
          })
        });
        const result = await response.json();
        if (response.ok) {
          this.successMessage = 'Configurações salvas com sucesso!';
        } else {
          this.errorMessage = result.message || 'Erro ao salvar configurações.';
        }
      } catch (e) {
        this.errorMessage = 'Erro ao salvar configurações.';
      }
    },
    validateEmail(email) {
      // Regex simples para e-mail
      const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
      if (!emailRegex.test(email)) return false;
      // Aqui seria feita a verificação de unicidade no backend
      return true;
    },
    validatePassword(password) {
      // Mínimo 6 caracteres, 1 número, 1 especial, 1 maiúscula
      const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{6,}$/;
      return regex.test(password);
    },
    async deleteAccount() {
      if (!confirm('Ao executar essa ação, você perderá o acesso a todos os benefícios da plataforma. Tem certeza disso?')) return;
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('/api/settings/delete', {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });
        const result = await response.json();
        if (response.ok) {
          alert('Conta excluída com sucesso!');
          // Redirecionar para login ou página inicial
          this.$router.push('/login');
        } else {
          alert(result.message || 'Erro ao excluir conta.');
        }
      } catch (e) {
        alert('Erro ao excluir conta.');
      }
    }
  },
  mounted() {
    this.loadUserData();
  }
};
</script>

<style scoped>
/* Sidebar expand/collapse */
.settings-page {
  padding: 2rem 0;
  margin-left: 250px;
  transition: margin-left 0.3s;
}
.settings-page.settings-expanded {
  margin-left: 70px;
}
</style>
