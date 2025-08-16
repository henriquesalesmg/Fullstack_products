<template>
  <div>
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th v-for="col in columns" :key="col.field" :class="col.sortable !== false ? 'sortable' : ''">
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in data" :key="row.id">
          <td v-for="col in columns" :key="col.field">
            <span v-if="col.field !== 'actions'">
              {{ col.format ? col.format(row[col.field]) : row[col.field] }}
            </span>
            <span v-else>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Ações</button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" @click.prevent="$emit('edit', row.id)">Editar</a></li>
                  <li><a class="dropdown-item text-danger" href="#" @click.prevent="$emit('delete', row.id)">Excluir</a></li>
                </ul>
              </div>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    columns: Array,
    data: Array
  }
}
</script>

<style scoped>
.table {
  border-radius: 0.5rem;
  overflow: hidden;
}
.sortable {
  cursor: pointer;
}
</style>
