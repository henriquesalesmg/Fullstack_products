// ...existing code...
<style scoped>
.products-page {
  flex-grow: 1;
  padding: 2rem 0;
  margin-left: 250px;
  transition: margin-left 0.3s;
}
.products-page.products-expanded {
  margin-left: 70px;
}
.products-page.products-collapsed {
  margin-left: 250px;
}
</style>
<template>
  <div class="d-flex">
  <Menu @sidebar-toggle="handleSidebarToggle" />
    <div :class="['flex-grow-1', 'products-page', { 'products-expanded': isSidebarCollapsed, 'products-collapsed': !isSidebarCollapsed }]">
      <div class="container-fluid py-4">
        <h2 class="fw-bold mb-4">
          <i class="fas fa-plus me-2"></i>
          Novo Produto
        </h2>
        <div class="card shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div v-if="error" class="alert alert-danger">{{ error }}</div>
              <div v-if="success" class="alert alert-success">{{ success }}</div>
              <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input v-model="form.name" type="text" id="name" class="form-control" required maxlength="255" />
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea v-model="form.description" id="description" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <input
                  v-model="form.priceFormatted"
                  type="text"
                  id="price"
                  class="form-control"
                  required
                  @input="onPriceInput"
                  @blur="onPriceBlur"
                  placeholder="R$ 0,00"
                />
              </div>
              <div class="mb-3">
                <label for="stock_quantity" class="form-label">Quantidade no estoque</label>
                <input v-model.number="form.stock_quantity" type="number" id="stock_quantity" class="form-control" min="0" required />
              </div>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i> Salvar
              </button>
              <router-link to="/products" class="btn btn-secondary ms-2">
                Cancelar
              </router-link>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Menu from '../components/Menu.vue';

export default {
  name: 'ProductForm',
  components: { Menu },
  data() {
    return {
      form: {
        name: '',
        description: '',
        price: '',
        priceFormatted: '',
        stock_quantity: 0
      },
      error: '',
      success: '',
  isEdit: false,
  productId: null,
  isSidebarCollapsed: false
    };
  },
  async mounted() {
    const id = this.$route.params.id;
    if (id && !isNaN(Number(id))) {
      this.isEdit = true;
      this.productId = Number(id);
      await this.loadProduct();
    }
  },
  methods: {
    handleSidebarToggle(collapsed) {
      this.isSidebarCollapsed = collapsed;
    },
    async loadProduct() {
      try {
        const response = await fetch(`http://localhost:8000/api/products/${this.productId}`, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        const result = await response.json();
        if (response.ok && result.data) {
          this.form.name = result.data.name;
          this.form.description = result.data.description;
          this.form.price = parseFloat(result.data.price);
          this.form.stock_quantity = result.data.stock_quantity;
          this.form.priceFormatted = Number(result.data.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '').trim();
        } else {
          this.error = result.message || 'Produto não encontrado.';
        }
      } catch (e) {
        this.error = 'Erro ao carregar produto.';
      }
    },
    onPriceInput(e) {
      // Permite digitação livre, apenas números e vírgula
      let value = e.target.value.replace(/[^\d,]/g, '');
      // Se houver mais de uma vírgula, mantém só a primeira
      const firstComma = value.indexOf(',');
      if (firstComma !== -1) {
        value = value.substring(0, firstComma + 1) + value.substring(firstComma + 1).replace(/,/g, '');
      }
      this.form.priceFormatted = value;
      // Converte para float para enviar ao backend
      let numeric = value.replace(/\./g, '').replace(',', '.');
      this.form.price = parseFloat(numeric) || 0;
    },
    onPriceBlur(e) {
      let value = e.target.value.replace(/[^\d,]/g, '');
      if (!value) {
        this.form.priceFormatted = '';
        this.form.price = 0;
        return;
      }
      // Se não houver vírgula, adiciona ,00
      if (!value.includes(',')) value += ',00';
      // Garante no máximo duas casas decimais
      value = value.replace(/,(\d{2}).*/, ',$1');
      let parts = value.split(',');
      let intPart = parts[0];
      let decPart = parts[1] || '00';
      // Formata milhares
      intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      let formatted = `R$ ${intPart},${decPart}`;
      this.form.priceFormatted = formatted;
      let numeric = (intPart.replace(/\./g, '') + '.' + decPart);
      this.form.price = parseFloat(numeric) || 0;
  },
    async submitForm() {
      this.error = '';
      this.success = '';
      // Validação frontend
      if (!this.form.name.trim()) {
        this.error = 'O nome do produto é obrigatório.';
        return;
      }
      if (!this.form.price || this.form.price <= 0) {
        this.error = 'O valor do produto deve ser maior que zero.';
        return;
      }
      if (!this.form.stock_quantity || this.form.stock_quantity <= 0) {
        this.error = 'A quantidade deve ser maior que zero.';
        return;
      }
      try {
        const payload = {
          name: this.form.name,
          description: this.form.description,
          price: this.form.price,
          stock_quantity: this.form.stock_quantity
        };
        let url = 'http://localhost:8000/api/products';
        let method = 'POST';
        if (this.isEdit) {
          url = `http://localhost:8000/api/products/${this.productId}`;
          method = 'PUT';
        }
        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (response.ok) {
          this.success = this.isEdit ? 'Produto atualizado com sucesso!' : 'Produto cadastrado com sucesso!';
          setTimeout(() => {
            this.$router.push({ path: '/products', query: { success: this.success } });
          }, 1200);
        } else {
          this.error = result.message || (this.isEdit ? 'Erro ao atualizar produto.' : 'Erro ao criar produto.');
        }
      } catch (e) {
        this.error = this.isEdit ? 'Erro ao atualizar produto.' : 'Erro ao criar produto.';
      }
    }
  }
}
</script>