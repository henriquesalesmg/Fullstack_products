<template>
  <div class="products-page">
    <h2>Products</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">
        <i class="fas fa-box me-2"></i> Products
      </h2>
      <router-link to="/products/new" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Product
      </router-link>
    </div>
    <div class="card shadow-sm">
      <div class="card-body">
        <DataTable :columns="columns" :data="products" />
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from './DataTable.vue';

export default {
  name: 'Products',
  components: { DataTable },
  data() {
    return {
      columns: [
        { label: 'ID', field: 'id' },
        { label: 'Name', field: 'name' },
        { label: 'Category', field: 'category' },
        { label: 'Price', field: 'price', format: value => `$ ${value.toFixed(2)}` },
        { label: 'Stock', field: 'stock' },
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
        const data = await response.json();
        this.products = data;
      } catch (e) {
        this.products = [];
      }
    }
  }
}
</script>

<style scoped>
.card {
  border-radius: 0.75rem;
}
.products-page {
  padding: 2rem 0;
}
</style>
