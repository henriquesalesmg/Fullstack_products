<template>
  <nav :class="['sidebar-custom', 'd-flex', 'flex-column', 'vh-100', 'p-0', { 'sidebar-collapsed': isCollapsed }]">
    <div class="sidebar-header py-4 px-3 d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
  <img v-if="!isCollapsed" :src="logoUrl" alt="Logo" style="width:100%;max-width:250px;height:auto;display:block;object-fit:contain;" />
      </div>
      <button class="btn btn-sm btn-outline-light" @click="toggleSidebar" title="Contrair/Expandir menu">
        <i :class="isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
      </button>
    </div>
    <ul class="nav flex-column mt-3">
      <li class="nav-item">
        <router-link to="/dashboard" class="nav-link px-4 py-3 d-flex align-items-center sidebar-link" active-class="active">
          <span class="me-3">
            <i class="fas fa-home"></i>
          </span>
          <span v-if="!isCollapsed">Dashboard</span>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/products" class="nav-link px-4 py-3 d-flex align-items-center sidebar-link" active-class="active">
          <span class="me-3">
            <i class="fas fa-box"></i>
          </span>
          <span v-if="!isCollapsed">Produtos</span>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/settings" class="nav-link px-4 py-3 d-flex align-items-center sidebar-link" active-class="active">
          <span class="me-3">
            <i class="fas fa-cog"></i>
          </span>
          <span v-if="!isCollapsed">Configurações</span>
        </router-link>
      </li>
    </ul>
    <div class="mt-auto py-3 text-center">
      <ul class="nav flex-column">
        <li class="nav-item">
          <router-link to="/logout" class="nav-link px-4 py-3 d-flex align-items-center sidebar-link" active-class="active">
            <span class="me-3">
              <i class="fas fa-sign-out-alt"></i>
            </span>
            <span v-if="!isCollapsed">Sair</span>
          </router-link>
        </li>
      </ul>
      <span v-if="!isCollapsed" class="text-muted small d-block mt-2 sidebar-copyright">&copy; 2025 Products Test</span>
    </div>
  </nav>
</template>

<script>
import '@fortawesome/fontawesome-free/css/all.css';

export default {
  name: 'Menu',
  data() {
    return {
      isCollapsed: false,
      isDesktop: true,
  logoUrl: window.location.origin + '/logo_blue.png'
    }
  },
  methods: {
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
      this.$emit('sidebar-toggle', this.isCollapsed);
    },
    handleResize() {
      this.isDesktop = window.innerWidth >= 992;
      if (this.isDesktop) {
        this.isCollapsed = false;
      }
    }
  },
  mounted() {
    this.handleResize();
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
  }
}
</script>

<style scoped>
/* Cores restauradas do menu lateral */
/* Sidebar expand/collapse */
.sidebar-custom {
  background: #1A1A2E;
  min-width: 250px;
  max-width: 250px;
  box-shadow: 0 0 24px 0 rgba(0,0,0,0.04);
  transition: width 0.3s;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  z-index: 1050;
}
.sidebar-custom.sidebar-collapsed {
  min-width: 70px;
  max-width: 70px;
}
.sidebar-header {
  background: transparent;
}
.sidebar-logo-text {
  color: #B2B2B2;
}
.sidebar-link {
  color: #B2B2B2;
  font-size: 1.05rem;
  border-radius: 0.375rem;
  transition: background 0.2s, color 0.2s;
}
.sidebar-link .fas {
  color: #FFFFFF;
  font-size: 1.2rem;
}
.sidebar-link.active,
.sidebar-link.router-link-exact-active {
  color: #00FFAB;
  background: #16213E;
}
.sidebar-link.active .fas,
.sidebar-link.router-link-exact-active .fas {
  color: #FFFFFF;
}
.sidebar-link:hover {
  background: #16213E;
  color: #00FFAB;
}
.sidebar-link:hover .fas {
  color: #FFFFFF;
}
.sidebar-copyright {
  color: #B2B2B2 !important;
}
@media (max-width: 991.98px) {
  .sidebar-custom {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 100vw;
    min-width: 0;
    max-width: 100vw;
    box-shadow: 2px 0 12px rgba(0,0,0,0.12);
    transition: left 0.3s, width 0.3s;
    background: #1A1A2E;
    z-index: 3000;
  }
  .sidebar-custom.sidebar-collapsed {
    left: 0 !important;
    width: 70px !important;
    min-width: 70px !important;
    max-width: 70px !important;
    overflow: visible !important;
  }
}
@media (max-width: 575.98px) {
  .sidebar-custom {
    width: 100vw;
    max-width: 100vw;
    min-width: 0;
    left: 0;
    z-index: 4000;
  }
  .sidebar-custom.sidebar-collapsed {
    left: 0 !important;
    width: 60px !important;
    min-width: 60px !important;
    max-width: 60px !important;
    overflow: visible !important;
  }
}
</style>



