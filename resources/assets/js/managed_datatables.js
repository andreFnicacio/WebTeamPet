var TableDatatablesManaged = function () {

    var initTable = function () {

        var table = $('.datatables');

        // begin first table
        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": ative para ordenar a coluna de forma ascendente",
                    "sortDescending": ": ative para ordenar a coluna de forma descendente"
                },
                "emptyTable": "Não há dados para essa tabela",
                "info": "Mostrando de _START_ até _END_ de um total de _TOTAL_ registros",
                "infoEmpty": "Nenhum registro encontrado",
                "infoFiltered": "(filtered1 de um total de _MAX_ registros)",
                "lengthMenu": "Mostrar _MENU_",
                "search": "Buscar:",
                "zeroRecords": "Nenhum resultado encontrado",
                "paginate": {
                    "previous":"Anterior",
                    "next": "Próximo",
                    "last": "Último",
                    "first": "Primeiro"
                }
            },

            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            "sDom": "lrti",
            //"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            "bPaging": false,
            "paging": false,
            "lengthMenu": [
                [20, 35, 50, -1],
                [20, 35, 50, "Todos"] // change per page values here
            ],
            // set the initial value
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [
                {  // set default column settings
                    'orderable': false,
                    'targets': [0]
                },
                {
                    'orderable': true
                },
                {
                    'orderable': false
                },
                {
                    "className": "dt-right"
                    //"targets": [2]
                }
            ],
            "order": [
                [6, 'desc'],[3,"asc"]
            ]
        });

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();
        }
    };
}();

var GuiasDatatablesManaged = function () {

    var initTable = function () {

        var table = $('.datatables-guias');

        // begin first table
        table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": ative para ordenar a coluna de forma ascendente",
                    "sortDescending": ": ative para ordenar a coluna de forma descendente"
                },
                "emptyTable": "Não há dados para essa tabela",
                "info": "Mostrando de _START_ até _END_ de um total de _TOTAL_ registros",
                "infoEmpty": "Nenhum registro encontrado",
                "infoFiltered": "(filtered1 de um total de _MAX_ registros)",
                "lengthMenu": "Mostrar _MENU_",
                "search": "Buscar:",
                "zeroRecords": "Nenhum resultado encontrado",
                "paginate": {
                    "previous":"Anterior",
                    "next": "Próximo",
                    "last": "Último",
                    "first": "Primeiro"
                }
            },

            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            "sDom": "lrti",
            //"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
            "bPaging": false,
            "paging": false,
            "lengthMenu": [
                [20, 35, 50, -1],
                [20, 35, 50, "Todos"] // change per page values here
            ],
            // set the initial value
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [
                {
                    'orderable': false,
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    'orderable': false,
                    responsivePriority: 2,
                    targets: -1
                },
                {
                    'orderable': true
                },
                {
                    'orderable': false
                },
                {
                    "className": "dt-right"
                }
            ],
            "order": [
                [6, 'desc'],[3,"asc"]
            ]
        });

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).prop("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).prop("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable();
        }
    };
}();

jQuery(document).ready(function() {
    TableDatatablesManaged.init();
    GuiasDatatablesManaged.init();
});