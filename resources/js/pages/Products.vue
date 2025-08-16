<template>
  <div class="d-flex">
    <Menu @sidebar-toggle="handleSidebarToggle" />
    <div :class="['flex-grow-1', 'products-page', { 'products-expanded': isSidebarCollapsed }]">
  <div class="container-fluid">
        <h2>Módulo</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="text-center">
            <h2 class="fw-bold mb-0">
              <i class="fas fa-box me-2"></i> Produtos
            </h2>
          </div>
          <router-link to="/products/new" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Produto
          </router-link>
        </div>
        <div class="card shadow-sm">
          <div class="card-body">
            <form class="row g-3 mb-3" @submit.prevent="applyFilters">
              <div class="col-md-3">
                <select v-model="orderBy" class="form-control">
                  <option value="name">Ordenar por Nome</option>
                  <option value="price">Ordenar por Preço</option>
                  <option value="stock_quantity">Ordenar por Estoque</option>
                </select>
              </div>
              <div class="col-md-2">
                <select v-model="orderDir" class="form-control">
                  <option value="asc">Ascendente</option>
                  <option value="desc">Descendente</option>
                </select>
              </div>
              <div class="col-md-3">
                <input type="text" v-model="filters.search" class="form-control" placeholder="Pesquisar nome...">
              </div>
              <div class="col-md-2">
                <input type="number" v-model.number="filters.price_min" class="form-control" placeholder="Preço mínimo">
              </div>
              <div class="col-md-2">
                <input type="number" v-model.number="filters.price_max" class="form-control" placeholder="Preço máximo">
              </div>
              <div class="col-md-2">
                <input type="number" v-model.number="filters.stock_min" class="form-control" placeholder="Estoque mínimo">
              </div>
              <div class="col-md-2">
                <input type="number" v-model.number="filters.stock_max" class="form-control" placeholder="Estoque máximo">
              </div>
              <div class="col-md-2">
                <select v-model.number="perPage" class="form-control" @change="changePerPage">
                  <option :value="25">25 por página</option>
                  <option :value="50">50 por página</option>
                  <option :value="100">100 por página</option>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success">Filtrar</button>
                <button type="button" class="btn btn-outline-secondary" @click="clearFilters">Limpar pesquisa</button>
              </div>
            </form>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
            <div class="table-responsive">
              <table id="products-datatable" class="table table-striped table-bordered align-middle" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">ID</th>
                  <th class="text-center">Nome</th>
                  <th class="text-center">Preço</th>
                  <th class="text-center">Quantidade no estoque</th>
                  <th class="text-center">Descrição</th>
                  <th class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="items.length === 0">
                  <td colspan="6" class="text-center text-muted">Nenhum produto encontrado.</td>
                </tr>
                <tr v-for="item in items" :key="item.id">
                  <td class="text-center">{{ item.id }}</td>
                  <td class="text-center">{{ item.name }}</td>
                  <td class="text-center">R$ {{ formatPrice(item.price) }}</td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center">
                      <input type="number" class="form-control form-control-sm" style="width: 60px; text-align: center;" v-model.number="stockInputs[item.id]" min="0" />
                      <button class="btn btn-sm btn-success ms-2" @click="updateStock(item)">
                        <i class="fas fa-save"></i>
                      </button>
                    </div>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-info" @click="showDescriptionModal(item)">Visualizar</button>
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Ações
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="#" @click.prevent="editProduct(item.id)">
                            <i class="fas fa-edit me-2"></i> Editar
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item text-danger" href="#" @click.prevent="deleteProduct(item.id)">
                            <i class="fas fa-trash me-2"></i> Excluir
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3">
              <button class="btn btn-outline-primary me-2" @click="loadMore">Carregar mais 50</button>
              <button class="btn btn-outline-secondary me-2" @click="loadAll">Carregar todos</button>
              <button class="btn btn-success" @click="downloadProducts">
                <i class="fas fa-download me-1"></i> Download Planilha
              </button>
            </div>
            <!-- Paginação Bootstrap -->
            <nav v-if="total > perPage" aria-label="Paginação de produtos" class="mt-3">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: page === 1 }">
                  <button class="page-link" @click="goToPage(page - 1)" :disabled="page === 1">Anterior</button>
                </li>
                <li v-for="p in totalPages" :key="p" class="page-item" :class="{ active: page === p }">
                  <button class="page-link" @click="goToPage(p)">{{ p }}</button>
                </li>
                <li class="page-item" :class="{ disabled: page === totalPages }">
                  <button class="page-link" @click="goToPage(page + 1)" :disabled="page === totalPages">Próxima</button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para descrição -->
    <div v-if="showDescription" class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Descrição do Produto</h5>
            <button type="button" class="btn-close" @click="closeDescription"></button>
          </div>
          <div class="modal-body">
            <p>{{ descriptionText }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDescription">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import Menu from '../components/Menu.vue';
import { initProductsDataTable } from '../datatable-init.js';

export default {
  name: 'Products',
  components: {
    Menu
  },
  data() {
    return {
      items: [],
      error: null,
      loading: false,
      successMessage: '',
      showDescription: false,
      descriptionText: '',
          successMessage: '',
          showDescription: false,
          descriptionText: '',
          stockInputs: {},
          isSidebarCollapsed: false,
          perPage: 25,
          page: 1,
          total: 0,
          orderBy: 'name',
          orderDir: 'asc',
          filters: {
            search: '',
            price_min: null,
            price_max: null,
            stock_min: null,
            stock_max: null,
          },
        };
      },
      methods: {
        downloadProducts() {
          if (!Array.isArray(this.items) || this.items.length === 0) {
            this.error = 'Nenhum produto para exportar.';
            setTimeout(() => { this.error = ''; }, 2500);
            return;
          }
          const header = ['Id', 'Nome', 'Preço', 'Quantidade', 'Descrição', 'Data de Criação'];
          const rows = this.items.map(item => [
            item.id,
            `"${item.name.replace(/"/g, '""')}"`,
            item.price,
            item.stock_quantity,
            `"${(item.description || '').replace(/"/g, '""')}"`,
            item.created_at ? new Date(item.created_at).toLocaleString('pt-BR') : ''
          ]);
          let csv = header.join(';') + '\n';
          csv += rows.map(r => r.join(';')).join('\n');
          const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.setAttribute('download', 'produtos.csv');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        },
        clearFilters() {
          this.filters = {
            search: '',
            price_min: null,
            price_max: null,
            stock_min: null,
            stock_max: null
          };
          this.page = 1;
          this.fetchProducts();
        },
        goToPage(p) {
          if (p < 1 || p > this.totalPages()) return;
          this.page = p;
          this.fetchProducts();
        },
        totalPages() {
          return Math.max(1, Math.ceil(this.total / this.perPage));
        },
        deleteProduct(id) {
          if (confirm('Deseja realmente excluir este produto?')) {
            const token = localStorage.getItem('token');
            fetch(`/api/products/${id}`, {
              method: 'DELETE',
              headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
              }
            })
            .then(async response => {
              const result = await response.json();
              if (response.ok) {
                this.successMessage = 'Produto excluído com sucesso!';
                this.error = null;
                this.fetchProducts();
                setTimeout(() => { this.successMessage = ''; }, 2500);
              } else {
                this.error = result.message || 'Erro ao excluir produto.';
                this.successMessage = '';
              }
            })
            .catch(() => {
              this.error = 'Erro ao excluir produto.';
              this.successMessage = '';
            });
          }
        },
        showDescriptionModal(item) {
          this.descriptionText = item && item.description ? item.description : 'Descrição não disponível.';
          this.showDescription = true;
        },
        closeDescription() {
          this.showDescription = false;
          this.descriptionText = '';
        },
        formatPrice(value) {
          if (!value) return '0,00';
          return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '').trim();
        },
        updateStock(item) {
          const newQty = this.stockInputs[item.id];
          if (newQty === undefined || newQty === null || isNaN(newQty)) {
            this.error = 'Informe uma quantidade válida.';
            this.successMessage = '';
            setTimeout(() => { this.error = ''; }, 2500);
            return;
          }
          const token = localStorage.getItem('token');
          fetch(`/api/products/${item.id}`, {
            method: 'PUT',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
              stock_quantity: newQty,
              name: item.name,
              price: item.price,
              description: item.description
            })
          })
          .then(async response => {
            const result = await response.json();
            if (response.ok) {
              this.successMessage = `Quantidade de ${item.name} atualizada para ${newQty}.`;
              this.error = '';
              item.stock_quantity = newQty;
              this.fetchProducts();
              setTimeout(() => { this.successMessage = ''; }, 2500);
            } else {
              this.error = result.message || 'Erro ao atualizar quantidade.';
              this.successMessage = '';
              setTimeout(() => { this.error = ''; }, 2500);
            }
          })
          .catch(() => {
            this.error = 'Erro ao atualizar quantidade.';
            this.successMessage = '';
            setTimeout(() => { this.error = ''; }, 2500);
          });
        },
        handleSidebarToggle(collapsed) {
          this.isSidebarCollapsed = collapsed;
        },
        async fetchProducts({ append = false, all = false } = {}) {
          this.loading = true;
          try {
            const token = localStorage.getItem('token');
            let url = '';
            const params = new URLSearchParams();
            if (all) {
              params.append('all', '1');
            } else {
              params.append('per_page', this.perPage);
              params.append('page', this.page);
            }
            if (this.filters.search) params.append('search', this.filters.search);
            if (this.filters.price_min !== null && this.filters.price_min !== '') params.append('price_min', this.filters.price_min);
            if (this.filters.price_max !== null && this.filters.price_max !== '') params.append('price_max', this.filters.price_max);
            if (this.filters.stock_min !== null && this.filters.stock_min !== '') params.append('stock_min', this.filters.stock_min);
            if (this.filters.stock_max !== null && this.filters.stock_max !== '') params.append('stock_max', this.filters.stock_max);
            params.append('order_by', this.orderBy);
            params.append('order_dir', this.orderDir);
            url = `/api/products?${params.toString()}`;
            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
              }
            });
            if (!response.ok) {
              const errorData = await response.json();
              this.error = errorData.message || 'Erro ao carregar produtos.';
              if (!append) this.items = [];
              this.loading = false;
              return;
            }
            const result = await response.json();
            if (append && !all) {
              this.items = [...(Array.isArray(this.items) ? this.items : []), ...(Array.isArray(result.items) ? result.items.filter(item => !(Array.isArray(this.items) ? this.items : []).some(i => i.id === item.id)) : [])];
            } else {
              this.items = Array.isArray(result.items) ? result.items : [];
            }
            this.total = result.total;
            this.error = null;
          } catch (e) {
            if (!append) this.items = [];
            this.error = 'Erro ao carregar produtos.';
          }
          this.loading = false;
        },
        applyFilters() {
          this.page = 1;
          this.fetchProducts();
        },
        changePerPage() {
          this.page = 1;
          this.fetchProducts();
        },
        loadMore() {
          this.page += 1;
          this.fetchProducts({ append: true });
        },
        loadAll() {
          this.fetchProducts({ all: true });
        },
        editProduct(id) {
          if (id) {
            this.$router.push(`/products/${id}/edit`);
          } else {
            this.error = 'ID do produto inválido para edição.';
            this.successMessage = '';
            setTimeout(() => { this.error = ''; }, 2500);
          }
        }
      },
  mounted() {
    this.fetchProducts();
    if (this.$route.query.success) {
      this.successMessage = this.$route.query.success;
      setTimeout(() => { this.successMessage = ''; }, 2500);
      this.$router.replace({ query: null });
    }
  },
  watch: {
    items: {
      handler(newItems) {
        // Inicializa os inputs com os valores atuais
        this.stockInputs = {};
        (Array.isArray(newItems) ? newItems : []).forEach(item => {
          this.stockInputs[item.id] = item.stock_quantity;
        });
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
/* Sidebar expand/collapse */
.d-flex { display: flex; }
  .products-page {
    flex-grow: 1;
    padding: 2rem 0;
    margin-left: 250px;
    transition: margin-left 0.3s;
  }
  .products-page.products-expanded {
    margin-left: 70px;
  }
  .card { border-radius: 0.75rem; }
  .easy-data-table {
    font-size: 1.15rem;
  }
  @media (max-width: 991px) {
    .products-page {
      padding: 1rem 0.2rem;
      margin-left: 0;
    }
    .table-responsive {
      overflow-x: auto;
    }
    #products-datatable th, #products-datatable td {
      white-space: nowrap;
      font-size: 0.95rem;
      padding: 0.5rem;
    }
    #products-datatable td input {
      width: 45px !important;
      font-size: 0.95rem;
    }
  }
</style>
