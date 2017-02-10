AntaRES.tableView = AntaRES.tableView || {};

AntaRES.prototype.tableView = {
    variables: {
        //vars
        table: $('table.clients-list'),
        massActions: $('.table-mass-actions'),
        tr: $('table.clients-list tr'),
        trSelected: $('table.clients-list tr.is-selected'),
    },
    options: {
        "iDisplayLength": 30,
        "bFilter": true,
        "bLengthChange": true,
        "bInfo": false,
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var index = iDisplayIndexFull + 1;
            $('td:eq(0)', nRow).html(index);
            return nRow;
        },
        //disable sorting
        "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
        "dom": '<"dt-area-top"i>rt<"dt-area-bottom"fpL><"clear">',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: false,
        initComplete: function () {

            var column = $('[data-table-init="true"]').DataTable().column(3),
                    select = $('[data-selectivity="true"]');

            select.on('change', function () {

                var val = $.fn.dataTable.util.escapeRegex(
                        $(this).children('option:selected').val()
                        );

                column.search(val ? '^' + val + '$' : '', true, false).draw();
            });

            column.data().unique().sort().each(function (d, j) {

                select.append('<option value="' + d + '">' + d + '</option>');
            });

        },
        "oLanguage": {
            // sProcessing: "<img src='http://i.imgur.com/zGCAUHJ.gif'>",
            "oPaginate": {
                "sPrevious": "<i class='zmdi zmdi-chevron-left dt-pag-left'></i>",
                "sNext": "<i class='zmdi zmdi-chevron-right dt-pag-right'></i>",
            },
            // "lengthMenu"  : "_MENU_ records per page",
            "sLengthMenu": "_MENU_",
        },
    },
    filterSearch: function () {
        //filter search
        $('.card-ctrls .search-box input').keyup(function () {
            oTable.search($(this).val()).draw();
        });
    },
    filterTags: function () {

        //filter tags
        $('.card-filter__sgl i').click(function (e) {
            $(this).closest('.card-filter__sgl').hide();
        });

    },
    contextMenu: function () {
        $.contextMenu({
            // define which elements trigger this menu
            selector: "table tr",
            // define the elements of the menu

            items: {
                clientsOverview: {
                    name: "Client Overview",
                    callback: function (key, opt) {
                        alert("Client Overview!");
                    }
                },
                clientContacts: {
                    name: "Client Contacts",
                    callback: function (key, opt) {
                        alert("Client Contacts!");
                    }
                },
                logInAsClient: {
                    name: "Log in As Client",
                    callback: function (key, opt) {
                        alert("Log in As Client!");
                    }
                },
                archiveClient: {
                    name: "Archive Client",
                    callback: function (key, opt) {
                        alert("Archive Client!");
                    }
                },
            },
            events: {
                show: function () {

                    if ($('table tr.is-selected').length > 1) {

                        $('table tr').removeClass('is-selected');

                        $(this).addClass('is-selected');

                    } else {

                        $('table tr').removeClass('is-selected');

                        $(this).addClass('is-selected');

                    }


                    if ($('tr.is-selected').length) {
                        $('#table-ma').removeClass('disabled');
                    } else {
                        $('#table-ma').addClass('disabled');
                    }


                },
            },
        });

        //taka delegacja
        $('.dt-actions i').click(function (e) {

            e.stopPropagation();

            $(this).closest('tr').addClass('is-selected');

            // $(this).contextMenu({x: e.pageX, y: e.pageY});
            var offset = $(this).offset();
            console.log('top: ' + offset.top);
            console.log('left: ' + offset.left);


            $(this).contextMenu({
                x: offset.left + 35,
                y: offset.top + 35,
            });

            // e.preventDefault();
        });

    },
    keyboardControl: function () {


    },
    generateRows: function () {

        $('table tbody tr').clone().appendTo('table');
        $('table tbody tr').clone().appendTo('table');

    },
    disableSelection: function () {

        var table = this.variables.table;

        $(document).mouseup(function (e) {
            // if the target of the click isn't the container...
            // ... nor a descendant of the container
            if (!table.is(e.target) && table.has(e.target).length === 0) {
                table.find('tr').removeClass('is-selected');
            }
        });


    },
    init: function () {

        var self = this;


        oTable = $('[data-table-init="true"]').DataTable(self.options);
        self.filterSearch();
        self.filterTags();
        self.keyboardControl();
        self.contextMenu();
        // self.disableSelection();
    },
};
// table select
var lastSelectedRow;
var trs = $('table tbody tr');

// disable text selection
document.onselectstart = function () {
    return false;
};

function RowClick(currenttr, lock) {
    if (window.event.ctrlKey) {
        toggleRow(currenttr);
    }

    if (window.event.button === 0) {
        if (!window.event.ctrlKey && !window.event.shiftKey) {
            clearAll();
            toggleRow(currenttr);
            $('tr').removeClass('is-selected');
        }

        if (window.event.shiftKey) {
            selectRowsBetweenIndexes([lastSelectedRow.rowIndex, currenttr.rowIndex]);
        }
    }
}

function toggleRow(row) {
    row.className = row.className == 'is-selected' ? '' : 'is-selected';
    lastSelectedRow = row;
}

function selectRowsBetweenIndexes(indexes) {
    indexes.sort(function (a, b) {
        return a - b;
    });

    for (var i = indexes[0]; i <= indexes[1]; i++) {
        trs[i - 1].className = 'is-selected';
    }
}

function clearAll() {
    for (var i = 0; i < trs.length; i++) {
        trs[i].className = '';
    }
}



$('table').click(function () {

    if ($('tr.is-selected').length) {
        $('#table-ma').removeClass('disabled');
    } else {
        $('#table-ma').addClass('disabled');
    }
});

$(function () {

    APP.tableView.init();

});
