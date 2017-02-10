AntaresTableView = function () {};
AntaresTableView.datatables = AntaresTableView.datatables || {};

AntaresTableView.prototype.init = function () {
    var self = this;

    (function datatables() {
        oTable = $('[data-table-init="true"]').DataTable(self.datatables.options);
        self.datatables.filterSearch();
        self.datatables.filterTags();
        self.datatables.selectMechanics();
        self.datatables.contextMenuMechanics();
        self.datatables.zeroData();

    }());

}

AntaresTableView.prototype.datatables = {
    variables: inject.variables,
    options: inject.options,
    filterSearch: function () {

        $('.card-ctrls .search-box input').keypress(function (e) {
            var val = $(this).val();
            if (e.which == 13) {
                e.preventDefault();
                oTable.search(val).draw();
            }

        });

    },
    filterTags: function () {
        $('.card-filter__sgl i').click(function (e) {
            $(this).closest('.card-filter__sgl').hide();
        });

    },
    contextMenuItems: {
        clientsOverview: {
            name: "Client Overview",
            callback: function (key, opt) {
                alert("Client Overview!");
            },
            icon: 'group',
        },
        clientContacts: {
            name: "Client Contacts",
            callback: function (key, opt) {
                alert("Client Contacts!");
            },
            icon: 'rss',
        },
        logInAsClient: {
            name: "Log in As Client",
            callback: function (key, opt) {
                alert("Log in As Client!");
            },
            icon: 'case',
        },
        archiveClient: {
            name: "Archive Client",
            callback: function (key, opt) {
                alert("Archive Client!");
            },
            icon: 'cast',
        },
    },
    contextMenuMechanics: function () {

        $.contextMenu({
            selector: '.billevo-table tr',
            build: function (trigger, e) {
                var elements = {};
                trigger.find('div.mass-actions-menu a').each(function (index, item) {
                    name = $(item).html();
                    callback = function (key, opt) {
                        if ($(item).hasClass('triggerable')) {
                            var description = $(item).attr('data-description');
                            var title = $(item).attr('data-title');
                            var href = $(item).attr('href');
                            if (description !== undefined && title !== undefined && href !== undefined) {
                                swal($.extend({}, APP.swal.cb1Warning(), {
                                    title: title,
                                    text: description,
                                }), function (isConfirm) {
                                    if (isConfirm) {
                                        $('.sweet-alert').LoadingOverlay('show');
                                        window.location.href = href;
                                    }
                                });
                                return false;
                            }
                            $(item).trigger("click");
                        } else {
                            window.location.href = $(item).attr('href');
                        }
                        return false;
                    };

                    elements[name] = {'name': name, 'icon': $(item).attr('data-icon'), 'href': $(item).attr('href'), 'callback': callback}

                });

                return {
                    items: elements
                };
            },
            events: {
                show: function () {
                    $(this).addClass('is-selected');
                },
            },
        });

        $.contextMenu({
            selector: '.billevo-table tr td.mass-actions',
            trigger: 'left',
            className: 'is-selected',
            build: function (trigger, e) {
                var elements = {};
                trigger.find('div.mass-actions-menu a').each(function (index, item) {
                    name = $(item).html();
                    callback = function (key, opt) {
                        if ($(item).hasClass('triggerable')) {
                            var description = $(item).attr('data-description');
                            var title = $(item).attr('data-title');
                            var href = $(item).attr('href');
                            if (description !== undefined && title !== undefined && href !== undefined) {
                                swal($.extend({}, APP.swal.cb1Warning(), {
                                    title: title,
                                    text: description,
                                }), function (isConfirm) {
                                    if (isConfirm) {
                                        $('.sweet-alert').LoadingOverlay('show');
                                        window.location.href = href;
                                    }
                                });
                                return false;
                            }
                            $(item).trigger("click");
                        } else {
                            window.location.href = $(item).attr('href');
                        }
                        return false;
                    };

                    elements[name] = {'name': name, 'icon': $(item).attr('data-icon'), 'href': $(item).attr('href'), 'callback': callback}

                });

                return {
                    items: elements
                };
            },
            events: {
                show: function () {

                    $(this).addClass('ui-selected');

                },
            },
        });

    },
    selectMechanics: function () {
        (function ($, c, b) {
            $.map("click dblclick mousemove mousedown mouseup mouseover mouseout change select submit keydown keypress keyup".split(" "), function (d) {
                a(d)
            });
            a("focusin", "focus" + b);
            a("focusout", "blur" + b);
            $.addOutsideEvent = a;
            function a(g, e) {
                e = e || g + b;
                var d = $(), h = g + "." + e + "-special-event";
                $.event.special[e] = {setup: function () {
                        d = d.add(this);
                        if (d.length === 1) {
                            $(c).bind(h, f)
                        }
                    }, teardown: function () {
                        d = d.not(this);
                        if (d.length === 0) {
                            $(c).unbind(h)
                        }
                    }, add: function (i) {
                        var j = i.handler;
                        i.handler = function (l, k) {
                            l.target = k;
                            j.apply(this, arguments)
                        }
                    }};
                function f(i) {
                    $(d).each(function () {
                        var j = $(this);
                        if (this !== i.target && !j.has(i.target).length) {
                            j.triggerHandler(e, [i.target])
                        }
                    })
                }}
        }
        )(jQuery, document, "outside");
        $('.tbl-c').selectable({
            delay: 100,
            distance: 100,
            stop: function (event, ui) {
                $(this).find('.ui-selected').removeClass('ui-selected').addClass('is-selected');
            },
        });

        $('.tbl-c tbody').multiSelect({
            unselectOn: false,
            keepSelection: true,
            selected: 'is-selected',
        });
        $('.billevo-table').on('click', function () {

            var self = $(this);

            setTimeout(function () {

                if (self.find('tr.is-selected').length > 1) {
                    self.closest('.tbl-c').find('#table-ma').removeClass('is-disabled');
                } else {
                    self.closest('.tbl-c').find('#table-ma').addClass('is-disabled');
                }

            }, 150);

        });
        $('#table-ma').on('click', function () {
            if ($(this).hasClass('is-disabled')) {
                $(this).siblings('.ddown__content').css('display', 'none');
            } else {
                $(this).siblings('.ddown__content').css('display', 'block');
            }
        });

        $('#table-ma').closest('.ddown').find('.ddown__menu').find('li:nth-child(1)').on('click', function () {
            alert('Client Overview')
        });

        $('#table-ma').closest('.ddown').find('.ddown__menu').find('li:nth-child(2)').on('click', function () {
            alert('Clients Contacts')
        });

        $('#table-ma').closest('.ddown').find('.ddown__menu').find('li:nth-child(3)').on('click', function () {
            alert('Log In As Client')
        });

        $('#table-ma').closest('.ddown').find('.ddown__menu').find('li:nth-child(4)').on('click', function () {
            alert('Archive Client')
        });
        $('body').on('dblclick', '.tbl-c .billevo-table tbody tr', function () {
            anchor = $(this).find('.mass-actions-menu a:first');
            description = anchor.attr('data-description');
            title = anchor.attr('data-title');
            href = anchor.attr('href');
            if (description !== undefined && title !== undefined && href !== undefined) {
                swal($.extend({}, APP.swal.cb1Warning(), {
                    title: title,
                    text: description
                }), function (isConfirm) {
                    if (isConfirm) {
                        $('.sweet-alert').LoadingOverlay('show');
                        window.location.href = href;
                    }
                });
                return false;
            } else {
                $('body').LoadingOverlay('show');
                window.location.href = href;
            }
        });
        $(".tbl-c").bind("clickoutside", function (event) {
            $(this).find('tr').removeClass('is-selected');
            $('.tbl-c #table-ma').addClass('is-disabled');
        });


    },
    disableSelection: function () {

    },
    generateRows: function () {

        for (var i = 1; i <= 5; ++i) {
            $('.billevo-table tbody tr').clone().appendTo('table');
        }
    },
    zeroData: function () {

        var bTable = $('.billevo-table');
        var cell = $('.billevo-table td');
        var zeroElement = $('.billevo-table .dataTables_empty');

        if (cell.length === 1 && zeroElement.length) {
            bTable.closest('.tbl-c').addClass('tbl-c--zd');
        }

    }

};

$.fn.multiSelect = function (o) {
    var defaults = {
        multiselect: true,
        selected: 'is-selected',
        filter: ' > *',
        unselectOn: false,
        keepSelection: true,
        list: $(this).selector,
        e: null,
        element: null,
        start: false,
        stop: false,
        unselecting: false
    }
    return this.each(function (k, v) {
        var options = $.extend({}, defaults, o || {});
        $(document).on('mousedown', options.list + options.filter, function (e) {
            if (e.which == 1) {
                if (options.handle != undefined && !$(e.target).is(options.handle)) {
                }
                options.e = e;
                options.element = $(this);
                multiSelect(options);
            }
            return true;
        });

        if (options.unselectOn) {

            $(document).on('mousedown', options.unselectOn, function (e) {
                if (!$(e.target).parents().is(options.list) && e.which != 3) {
                    $(options.list + ' .' + options.selected).removeClass(options.selected);
                    if (options.unselecting != false) {
                        options.unselecting();
                    }
                }
            });

        }

    });


}
function multiSelect(o) {

    var target = o.e.target;
    var element = o.element;
    var list = o.list;

    if ($(element).hasClass('ui-sortable-helper')) {
        return false;
    }

    if (o.start != false) {
        var start = o.start(o.e, $(element));
        if (start == false) {
            return false;
        }
    }

    if (o.e.shiftKey && o.multiselect) {
        $(element).addClass(o.selected);
        first = $(o.list).find('.' + o.selected).first().index();
        last = $(o.list).find('.' + o.selected).last().index();
        if (last < first) {
            firstHolder = first;
            first = last;
            last = firstHolder;
        }

        if (first == -1 || last == -1) {
            return false;
        }

        $(o.list).find('.' + o.selected).removeClass(o.selected);

        var num = last - first;
        var x = first;

        for (i = 0; i <= num; i++) {
            $(list).find(o.filter).eq(x).addClass(o.selected);
            x++;
        }
    } else if ((o.e.ctrlKey || o.e.metaKey) && o.multiselect) {
        if ($(element).hasClass(o.selected)) {


            $(element).removeClass('ui-selected');
            setTimeout(function () {
                $(element).removeClass(o.selected);
            }, 100);
        } else {
            $(element).addClass(o.selected);
        }
    } else {
        if (o.keepSelection && !$(element).hasClass(o.selected)) {
            $(list).find('.' + o.selected).removeClass(o.selected);
            $(element).addClass(o.selected);
        } else {
            $(list).find('.' + o.selected).removeClass(o.selected);
            $(element).addClass(o.selected);
        }

    }

    if (o.stop != false) {
        o.stop($(list).find('.' + o.selected), $(element));
    }

}
/*!
 Page length control via links for DataTables
 2014 SpryMedia Ltd - datatables.net/license
 */
(function (i, j, a) {
    a.fn.dataTable.LengthLinks = function (d) {
        var c = new a.fn.dataTable.Api(d),
                f = c.settings()[0],
                e = a("<div></div>").addClass(f.oClasses.sLength),
                h = null;
        this.container = function () {
            return e[0]
        };
        e.on("click.dtll", "a", function (b) {
            b.preventDefault();
            c.page.len(1 * a(this).data("length")).draw(!1)
        });
        c.on("draw", function () {
            if (c.page.len() !== h) {
                var b = f.aLengthMenu,
                        d = 2 === b.length && a.isArray(b[0]) ? b[1] : b,
                        g = 2 === b.length && a.isArray(b[0]) ? b[0] : b,
                        b = a.map(g, function (b, a) {
                            return b == c.page.len() ? '<a class="active" data-length="' +
                                    g[a] + '">' + d[a] + "</a>" : '<a data-length="' + g[a] + '">' + d[a] + "</a>"
                        });
                e.html(f.oLanguage.sLengthMenu.replace("_MENU_", b.join(" ")));
                h = c.page.len()
            }
        });
        c.on("destroy", function () {
            e.off("click.dtll", "a")
        })
    };
    a.fn.dataTable.ext.feature.push({
        fnInit: function (d) {
            return (new a.fn.dataTable.LengthLinks(d)).container()
        },
        cFeature: "L",
        sFeature: "LengthLinks"
    })
})(window, document, jQuery);

$(function () {
    window.AntaresTableView = new AntaresTableView();
    AntaresTableView.init();
});