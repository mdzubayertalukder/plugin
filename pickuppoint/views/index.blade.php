@extends('core::base.layouts.master')
@section('title')
    {{ translate('Pickup Points') }}
@endsection
@section('custom_css')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/data-table/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/fontawsome/css/all.min.css') }}">
    <!-- ======= END BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
@endsection
@section('main_content')
    <div class="row">
        <!-- pickup point list -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20 ">{{ translate('Pickup Points') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.pickuppoint.create.pickup.points') }}" class="btn long">
                                {{ translate('Add New Pickup Point') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pickup_points" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Pickup Point') }}</th>
                                <th>{{ translate('Location') }}</th>
                                <th>{{ translate('Phone') }}</th>
                                <th>{{ translate('Country') }}</th>
                                <th>{{ translate('State') }}</th>
                                <th>{{ translate('City') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /pickup point list -->

        <!--Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                        <form method="POST" action="{{ route('plugin.pickuppoint.delete.pickup.point') }}">
                            @csrf
                            <input type="hidden" id="pickup_id" name="id">
                            <button type="button" class="btn long mt-2"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn btn-danger long mt-2">{{ translate('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            <!--Delete Modal-->
        </div>
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/data-table/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>

    <script src="{{ asset('/public/backend/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                //Datatable initialization
                var table = $('#pickup_points').DataTable({
                    processing: true,
                    serverSide: true,
                    dom: 'Blfrtip',
                    responsfive: true,
                    buttons: [{
                        extend: 'copyHtml5',
                        text: '<i class="icofont-copy-invert"></i>',
                        titleAttr: 'Copy',
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        }
                    }, {
                        extend: 'excelHtml5',
                        text: '<i class="icofont-file-excel"></i>',
                        titleAttr: 'Excel',
                        margin: [10, 10, 10, 0],
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        },
                    }, {
                        extend: 'csvHtml5',
                        text: '<i class="icofont-file-excel"></i>',
                        titleAttr: 'CSV',
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        }
                    }, {
                        extend: 'pdfHtml5',
                        text: '<i class="icofont-file-pdf"></i>',
                        titleAttr: 'PDF',
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        },
                        orientation: 'landscape',
                        pageSize: 'A4',
                        margin: [0, 0, 0, 12],
                        alignment: 'center',
                        header: true,
                        customize: function(doc) {
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: 'center',
                                image: "data:image/png;base64," + $("#logo_img")
                                    .val()
                            });
                        }

                    }, {
                        extend: 'print',
                        text: '<i class="icofont-printer"></i>',
                        titleAttr: 'Print',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    }, {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i>',
                        postfixButtons: ['colvisRestore']
                    }],
                    ajax: "{{ route('plugin.pickuppoint.pickup.point.list') }}",
                    columns: [{
                            data: 'pickup_id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return data ? '<div class="d-flex align-items-center">\n' +
                                    '<label class="position-relative">\n' +
                                    '<input type="checkbox" name="pickup_id[]" class="pickup-id mt-2" value="' +
                                    data + '">\n' +
                                    '<span class="checkmark"></span>\n' +
                                    '</label>\n' +
                                    '</div>' : '';
                            },
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'location'
                        },
                        {
                            data: 'phone'
                        },
                        {
                            data: 'country'
                        },
                        {
                            data: 'state'
                        },
                        {
                            data: 'city'
                        },
                        {
                            data: 'status',
                            render: function(data, type, row, meta) {
                                let checked = "";
                                if (data == 1) {
                                    checked = "checked"
                                }
                                return '<label class="switch glow primary medium">\n' +
                                    '<input type="checkbox" name="status" id="pickup_status_' +
                                    row.pickup_id + '" ' + checked + ' value="' + data +
                                    '" onchange="updatePickupPointStatus(' + row.pickup_id +
                                    ')">\n' +
                                    '<span class="control"></span>\n' +
                                    '</label>'
                            }
                        }, {
                            data: null,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return data ? '<div class="dropdown-button">\n' +
                                    '<a href="#" class="d-flex align-items-center justify-content-end" data-toggle="dropdown">\n' +
                                    '<div class="menu-icon mr-0">\n' +
                                    '<span></span>\n' +
                                    '<span></span>\n' +
                                    '<span></span>\n' +
                                    '</div>\n' +
                                    '</a>\n' +
                                    '<div class="dropdown-menu dropdown-menu-right">\n' +
                                    '<a href="{{ route('plugin.pickuppoint.edit.pickup.point') }}?id=' +
                                    row.pickup_id +
                                    '" type="button">{{ translate('Edit') }}</a>\n' +
                                    '<a type="button" onclick="deletePickupPint(' + row
                                    .pickup_id +
                                    ')">{{ translate('Delete') }}</a>\n' +
                                    '</div>\n' +
                                    '</div>' : '';
                            },
                        }
                    ],
                });

                var bulk_actions_dropdown =
                    '<div id="bulk-action" class="dataTables_length d-flex"><select class="theme-input-style bulk-action-selection mr-3"><option value="">{{ translate('Bulk Action') }}</option><option value="delete_all">{{ translate('Delete selection') }}</option></select><button class="btn long bulk-action-apply-btn" onclick="bulkAction">{{ translate('Apply') }}</button></div>';

                $(bulk_actions_dropdown).insertAfter("#pickup_points_wrapper #pickup_points_length");
            });


            /**
             * Select all table item
             */
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".pickup-id").prop("checked", true);
                } else {
                    $(".pickup-id").prop("checked", false);
                }
            });

        })(jQuery);
        /**
         * Bulk action
         */
        function bulkAction() {
            "use strict";
            let action = $('.bulk-action-selection').val();
            if (action === 'delete_all') {
                var selected_items = [];
                $('input[name^="pickup_id"]:checked').each(function() {
                    selected_items.push($(this).val());
                });
                if (selected_items.length > 0) {
                    $.post('{{ route('plugin.pickuppoint.delete.bulk.pickup.point') }}', {
                            _token: '{{ csrf_token() }}',
                            data: selected_items
                        },
                        function(data) {
                            location.reload();
                        })
                } else {
                    toastr.error('{{ translate('"Error!"') }}');
                }
            } else {
                toastr.error('{{ translate('"Error!"') }}');
            }
        }
        /**
         * Showing pickup point deleting modal
         */
        function deletePickupPint(log_id) {
            "use strict";
            $("#pickup_id").val(log_id);
            $('#delete-modal').modal('show');
        }
        /**
         * Will request to update pickup point status
         */
        function updatePickupPointStatus(pickup_id) {
            "use strict";
            let status = 2
            if ($('#pickup_status_' + pickup_id).is(':checked')) {
                status = 1
            }
            $.post("{{ route('plugin.pickuppoint.update.pickup.point.status') }}", {
                    _token: '{{ csrf_token() }}',
                    id: pickup_id,
                    status: status
                },
                function(data, status) {
                    toastr.success("Pickup point status updated successfully", "Success!");
                }).fail(function(xhr, status, error) {
                toastr.error("Unable to update pickup point status", "Error!");
            });
        }
    </script>
@endsection
