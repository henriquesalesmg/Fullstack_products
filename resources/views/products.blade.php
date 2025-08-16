<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Test Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div id="app" class="container py-5">
    <h1 class="mb-4">Products Test Page</h1>
    <div id="vue-products"></div>
</div>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp, ref, onMounted } = Vue;
createApp({
    setup() {
        const products = ref([]);
        const loading = ref(false);
        const error = ref("");
        const fetchProducts = async () => {
            loading.value = true;
            error.value = "";
            try {
                const res = await fetch('/api/products', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                products.value = data.data.data || [];
            } catch (e) {
                error.value = "Failed to load products.";
            }
            loading.value = false;
        };
        onMounted(fetchProducts);
        return { products, loading, error };
    },
    template: `
        <div>
            <button class="btn btn-primary mb-3" @click="fetchProducts">Reload</button>
            <div v-if="loading" class="alert alert-info">Loading...</div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <table v-if="products.length" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Quantidade em Estoque</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id">
                        <td>{{ product.id }}</td>
                        <td>{{ product.name }}</td>
                        <td>{{ product.description }}</td>
                        <td>{{ product.price }}</td>
                        <td>{{ product.stock_quantity }}</td>
                        td>
                            <button class="btn btn-sm btn-warning me-2" @click="alert('Edit ' + product.name)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="alert('Delete ' + product.name)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="alert alert-warning">No products found.</div>
        </div>
    `
}).mount('#vue-products');
</script>
</body>
</html>
