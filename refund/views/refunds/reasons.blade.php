@extends('core::base.layouts.master')
@section('title')
    {{ translate('Refund Reasons') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Refund Reasons') }}</h4>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20 mb-2">{{ translate('Refund Reasons') }}</h4>
                        <div id="bulk-action" class="dataTables_length d-flex">
                            <select class="theme-input-style bulk-action-selection mr-3">
                                <option value="">{{ translate('Bulk Action') }}</option>
                                <option value="delete_all">{{ translate('Remove selection') }}</option>
                            </select>
                            <button class="btn long" onclick="bulkAction()">{{ translate('Apply') }}</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="attributeValueTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all" onchange="selectAll()">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Reason') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reasons as $key => $reason)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center mb-3">
                                            <label class="position-relative mr-2">
                                                <input type="checkbox" name="items[]" class="item-id"
                                                    value="{{ $reason->id }}">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $reason->translation('name') }}</td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-status" data-reason="{{ $reason->id }}"
                                                {{ $reason->status == '1' ? 'checked' : '' }}>
                                            <span class="control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center justify-content-end"
                                                data-toggle="dropdown">
                                                <div class="menu-icon mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a
                                                    href="{{ route('plugin.refund.reason.edit', ['id' => $reason->id, 'lang' => getDefaultLang()]) }}">
                                                    {{ translate('Edit') }}
                                                </a>
                                                <a href="#" class="delete-reason"
                                                    data-reason="{{ $reason->id }}">{{ translate('Delete') }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <div class="mb-3">
                <p class="alert alert-info">You are inserting <strong>"{{ getLanguageNameByCode(getDefaultLang()) }}"</strong> version</p>
            </div>
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New Refund Reasons') }}</h4>
                <form action="{{ route('plugin.refund.reason.store') }}" method="POST">
                    @csrf

                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Reason') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="theme-input-style " value="{{ old('name') }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.refund.reason.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-reason-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script>
        (function($) {
            "use strict";
            /**
             * Attribute values data table
             */
            $(function() {
                "use strict";
                $("#attributeValueTable").DataTable({
                    "responsive": false,
                    "scrolX": true,
                    "lengthChange": true,
                    "autoWidth": false,
                }).buttons().container().appendTo('#attributeValueTable_wrapper .col-md-6:eq(0)');
            });

            /**
             * 
             * Change status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('reason');
                $.post('{{ route('plugin.refund.reason.status.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })
            });
            /**
             * 
             * Delete reason
             * 
             * */
            $('.delete-reason').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('reason');
                $("#delete-reason-id").val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
        /**
         * 
         * Bulk action
         **/
        function bulkAction() {
            "use strict";
            let action = $('.bulk-action-selection').val();
            if (action == 'delete_all') {
                var selected_items = [];
                $('input[name^="items"]:checked').each(function() {
                    selected_items.push($(this).val());
                });

                if (selected_items.length > 0) {
                    $.post('{{ route('plugin.refund.reason.delete.bulk') }}', {
                        _token: '{{ csrf_token() }}',
                        data: selected_items
                    }, function(data) {
                        location.reload();
                    })
                } else {
                    toastr.error('{{ translate('No Item Selected') }}', "Error!");
                }
            } else {
                toastr.error('{{ translate('No Action Selected') }}', "Error!");
            }
        }

        /**
         * 
         * Select all Items
         **/
        function selectAll() {
            "use strict";
            if ($('.select-all').is(":checked")) {
                $(".item-id").prop("checked", true);
            } else {
                $(".item-id").prop("checked", false);
            }
        }
    </script>
@endsection
