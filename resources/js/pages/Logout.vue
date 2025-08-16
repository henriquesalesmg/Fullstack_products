<template>
  <div class="container py-4">
    <h2>Logout</h2>
    <div class="alert alert-info">Você foi desconectado.</div>
  </div>
</template>
<script>
export default {
  name: 'Logout',
  mounted() {
    localStorage.removeItem('token');
    this.$router.push('/auth');
  }
}
</script>
