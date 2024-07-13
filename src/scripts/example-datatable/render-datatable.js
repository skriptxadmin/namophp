import DataTable from "datatables.net-bs5";
import { ColumnDefs } from "./column-defs";

export function RenderDatatable(){

    const page$ = jQuery(document).find("body.page.super-administrator.coordinators");

    const table$ = page$.find('table.coordinators');

    let tableInstance;

    getCoordinators();

    function getCoordinators() {
        const options = {
          processing: true,
          serverSide: true,
          ajax: function (request, drawCallback, settings) {
            jQuery.ajax({
              url: "super-administrator/coordinators/get",
              type: "POST",
              data: request,
              success: function (res) {
             
                drawCallback(res);
              },
            });
          },
          columnDefs: ColumnDefs,
          aaSorting: [],
          createdRow: function (row, data, index) {
            row.setAttribute("data-username", data.username);
          },
        };
        tableInstance = new DataTable(table$, options);
        return;
      }
}