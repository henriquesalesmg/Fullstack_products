<template>
  <div class="card flex-fill w-100 mb-3 product-chart-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Produtos por mês</h5>
      <form class="row g-2">
        <div class="col-auto">
          <select class="form-select form-select-sm bg-light border-0" v-model="selectedMonth">
            <option value="-1">Escolher mês</option>
            <option v-for="(month, idx) in months" :key="idx" :value="idx">{{ month }}</option>
          </select>
        </div>
      </form>
    </div>
    <div class="card-body pt-2 pb-3">
      <div class="chart chart-sm d-flex justify-content-center">
        <canvas id="mdbChart" ref="chart" class="product-chart-canvas"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

export default {
  name: 'ProductChart',
  props: {
    data: {
      type: Array,
      required: true
    },
    currentYear: {
      type: Number,
      required: true
    }
  },
  setup(props) {
    const chart = ref(null);
    const months = [
      'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    const selectedMonth = ref(-1);
    const filteredData = computed(() => {
      if (selectedMonth.value === -1) {
        // Exibe todos os meses
        return props.data ? [...props.data] : Array(12).fill(0);
      }
      // Exibe apenas o mês selecionado
      return props.data ? props.data.map((v, i) => (i === selectedMonth.value ? v ?? 0 : 0)) : Array(12).fill(0);
    });
    let chartInstance = null;

    const renderChart = () => {
      if (chartInstance) {
        chartInstance.destroy();
      }
      const chartData = filteredData.value && filteredData.value.length === 12 ? filteredData.value : Array(12).fill(0);
      chartInstance = new Chart(chart.value, {
        type: 'line',
        data: {
          labels: months,
          datasets: [
            {
              label: 'Produtos',
              data: chartData,
              borderColor: '#1266f1',
              backgroundColor: 'rgba(18,102,241,0.1)',
              pointBackgroundColor: '#00B4D8',
              pointBorderColor: '#FFFFFF',
              pointRadius: 5,
              tension: 0.4,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              enabled: true,
              callbacks: {
                title: function(context) {
                  return 'Mês: ' + context[0].label;
                },
                label: function(context) {
                  return 'Produtos: ' + context.parsed.y;
                }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: '#002B5B' }
            },
            y: {
              grid: { color: '#B0C4DE' },
              ticks: { color: '#002B5B' }
            }
          }
        }
      });
    };
    onMounted(() => {
      renderChart();
    });
    watch([filteredData, selectedMonth], () => {
      renderChart();
    });
    return { chart, months, selectedMonth };
  }
}
</script>

<style scoped>
.product-chart-card {
  max-width: 700px;
  margin: 0 auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border-radius: 12px;
  background: #fff;
}
.product-chart-canvas {
  max-width: 100%;
  width: 100% !important;
  height: 250px !important;
  margin: 0 auto;
  display: block;
  border-radius: 8px;
}
.card-header, .card-body {
  padding-left: 1rem;
  padding-right: 1rem;
}
.card-title {
  font-size: 1.1rem;
  font-weight: 600;
}
form.row.g-2 {
  flex-wrap: wrap;
}
.form-select {
  min-width: 120px;
  font-size: 1rem;
}
@media (max-width: 991.98px) {
  .product-chart-card {
    max-width: 100%;
    border-radius: 8px;
  }
  .product-chart-canvas {
    height: 180px !important;
  }
  .card-header, .card-body {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  .card-title {
    font-size: 1rem;
  }
  .form-select {
    min-width: 100px;
    font-size: 0.95rem;
  }
}
@media (max-width: 575.98px) {
  .product-chart-card {
    margin: 0.5rem;
    box-shadow: none;
    border-radius: 4px;
  }
  .product-chart-canvas {
    height: 120px !important;
  }
  .card-header, .card-body {
    padding-left: 0.25rem;
    padding-right: 0.25rem;
  }
  .card-title {
    font-size: 0.95rem;
  }
  .form-select {
    min-width: 80px;
    font-size: 0.9rem;
  }
}
</style>
