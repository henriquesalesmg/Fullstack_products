import 'datatables.net-bs5';
import $ from 'jquery';

export function initProductsDataTable() {
  $(document).ready(function () {
    $('#products-datatable').DataTable({
      responsive: true,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
      },
      order: [[0, 'desc']]
    });
  });
}
