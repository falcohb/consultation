import 'bootstrap-table';
import 'bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min'
import 'bootstrap-table/dist/extensions/print/bootstrap-table-print.min'
import $ from "jquery";

global.$ = $;
global.jQuery = $;

const $table = $('#table')
$(() => {
    $table.bootstrapTable({
        printPageBuilder (table) {
            return `
<html>
  <head>
  <style type="text/css" media="print">
  @page {
    size: auto;
    margin: 25px 0 25px 0;
  }
  </style>
  <style type="text/css" media="all">
  table {
    border-collapse: collapse;
    font-size: 12px;
  }
  table, th, td {
    border: 1px solid grey;
  }
  th, td {
    text-align: left;
    vertical-align: top;
  }
  p {
    font-weight: bold;
    margin-left:20px;
  }
  table {
    width:94%;
    margin-left:3%;
    margin-right:3%;
  }
  div.bs-table-print {
    text-align:left;
  }
  </style>
  </head>
  <title>Print Table</title>
  <body>
  <div class="bs-table-print">${table}</div>
  </body>
</html>`
        }
    })
})