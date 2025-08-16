<template>
  <div class="d-flex">
    <Menu />
    <div class="flex-grow-1 products-page">
      <h2>Módulo</h2>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
          <i class="fas fa-box me-2"></i> Produtos
        </h2>
        <router-link to="/products/new" class="btn btn-primary">
          <i class="fas fa-plus me-1"></i> New Product
        </router-link>
      </div>
      <div class="card shadow-sm">
        <div class="card-body">
      <DataTable :columns="columns" :data="products" @edit="onEdit" @delete="onDelete" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Menu from './Menu.vue';
import DataTable from './DataTable.vue';

export default {
  name: 'Products',
  components: { Menu, DataTable },
  data() {
    return {
      columns: [
        { label: 'ID', field: 'id' },
        { label: 'Name', field: 'name' },
        { label: 'Description', field: 'description' },
        { label: 'Price', field: 'price', format: value => `$ ${value.toFixed(2)}` },
        { label: 'Stock', field: 'stock_quantity' },
        { label: 'Actions', field: 'actions', sortable: false }
      ],
      products: []
    }
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await fetch('/api/products');
        const result = await response.json();
        if (result.data && Array.isArray(result.data.data)) {
          this.products = result.data.data;
        } else if (result.items && Array.isArray(result.items)) {
          this.products = result.items;
        } else {
          this.products = [];
        }
      } catch (e) {
        this.products = [];
      }
    }
      ,
      onEdit(id) {
        this.$router.push(`/products/${id}/edit`);
      },
      async onDelete(id) {
        if (confirm('Deseja realmente excluir este produto?')) {
          try {
            const response = await fetch(`/api/products/${id}`, {
              method: 'DELETE',
              headers: { 'Content-Type': 'application/json' }
            });
            const result = await response.json();
            if (result.message) alert(result.message);
            this.fetchProducts();
          } catch (e) {
            alert('Erro ao excluir produto.');
          }
        }
      }
  }
}
</script>

<style scoped>
.d-flex { display: flex; }
.flex-grow-1 { flex-grow: 1; }
.card { border-radius: 0.75rem; }
.products-page { padding: 2rem 0; }
</style>
