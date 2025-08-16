<template>
  <div class="d-flex">
  <Menu @sidebar-toggle="handleSidebarToggle" />
  <main :class="['flex-grow-1', 'dashboard-main', { 'dashboard-expanded': isSidebarCollapsed }]">
      <div class="container-fluid py-4">
        <div class="row mb-4">
          <div class="col-12 d-flex align-items-center gap-3 mb-3">
            <img :src="logoUrl" alt="Logo" style="height:40px;width:auto;" />
            <h1 class="fw-bold mb-0">Dashboard</h1>
          </div>
        </div>
        <div class="row g-4 justify-content-center">
          <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm mb-4">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Relatório de Produtos</h5>
              </div>
              <div class="card-body">
                <div v-if="user">
                  <ProductChart :data="chartData" :currentYear="currentYear" />
                </div>
                <div v-else>
                  <div class="alert alert-info">Loading user data...</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-8 col-lg-4">
            <div class="card shadow-sm mb-4">
              <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Área do Usuário</h5>
              </div>
              <div class="card-body">
                <p class="mb-1">Olá, <strong>{{ user ? user.name : '---' }}</strong>!</p>
                <p class="mb-1"><strong>Email:</strong> {{ user ? user.email : '---' }}</p>
                <p class="mb-1"><strong>Ano:</strong> {{ currentYear }}</p>
              </div>
            </div>
            <!-- Earnings Card -->
            <div class="card shadow-sm mb-4">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">Valores de produtos deste mês</h5>
                  </div>
                  <div class="col-auto">
                    <div class="stat text-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign align-middle"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                  </div>
                </div>
                <h1 class="mt-1 mb-3">R$ {{ earningsCurrentMonth }}</h1>
                <div class="mb-0">
                  <span :class="earningsDiff >= 0 ? 'badge bg-success' : 'badge bg-danger'">
                    {{ earningsDiff.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                  </span>
                  <span class="text-muted"> Diferença em relação ao mês anterior</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Menu from '../components/Menu.vue';
import ProductChart from '../components/ProductChart.vue';

export default {
  name: 'Dashboard',
  components: { Menu, ProductChart },
  data() {
    return {
      user: null,
      chartData: Array(12).fill(0),
      currentYear: new Date().getFullYear(),
      logoUrl: window.location.origin + '/logo.png',
      earningsCurrentMonth: 0,
      earningsPreviousMonth: 0,
      isSidebarCollapsed: false
    }
  },
  methods: {
    handleSidebarToggle(collapsed) {
      this.isSidebarCollapsed = collapsed;
    },
    fetchChartData() {
      fetch(`/api/products/stats?year=${this.currentYear}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      })
        .then(res => res.json())
        .then(data => {
          this.chartData = data.counts;
        });
    }
  },
  mounted() {
    // Fetch user data
    fetch('/api/user', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
      .then(res => {
        if (!res.ok) throw new Error('Unauthorized');
        return res.json();
      })
      .then(data => {
        // Ajusta para pegar o usuário do campo 'data' da resposta
        this.user = data.data || data;
      })
      .catch(() => {
        this.user = null;
        this.$router.push('/auth');
      });
    // Atualiza gráfico ao montar
    this.fetchChartData();
    // Atualiza gráfico ao retornar do cadastro de produto
    if (this.$route.query.success) {
      this.fetchChartData();
    }

    // Fetch earnings for current and previous month
    fetch('/api/products/earnings', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
      .then(res => res.json())
      .then(data => {
        // Ajusta para pegar os valores corretos da resposta
        this.earningsCurrentMonth = data.currentMonth || data.total_earnings || 0;
        this.earningsPreviousMonth = data.previousMonth || 0;
      });
  },
  computed: {
    earningsDiff() {
      const curr = Number(this.earningsCurrentMonth);
      const prev = Number(this.earningsPreviousMonth);
      if (isNaN(curr) || isNaN(prev)) return 0;
      return +(curr - prev).toFixed(2);
    },
    earningsDiffPercent() {
      const curr = Number(this.earningsCurrentMonth);
      const prev = Number(this.earningsPreviousMonth);
      if (isNaN(curr) || isNaN(prev) || !isFinite(prev)) return '0.00';
      if (prev === 0) {
        if (curr === 0) return '0.00';
        return ((curr > 0 ? 1 : -1) * 100).toFixed(2);
      }
      const percent = ((curr - prev) / prev * 100);
      if (!isFinite(percent)) return '0.00';
      return percent.toFixed(2);
    }
  }
}
</script>

<style scoped>
.card-header {
  border-bottom: 1px solid #e3e6f0;
}
/* Dashboard main margin changes with sidebar state */
.dashboard-main {
  margin-left: 250px;
  min-width: 0;
  transition: margin-left 0.3s;
}
.dashboard-main.dashboard-expanded {
  margin-left: 70px;
}
</style>
