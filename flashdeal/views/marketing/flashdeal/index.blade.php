@extends('core::base.layouts.master')
@section('title')
    {{ translate('Flash Deal') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Flash Deal') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.flashdeal.add.new') }}"
                                class="btn long">{{ translate('Add New Flash Deal') }}</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="couponTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Title') }}</th>
                                <th>{{ translate('Start Date') }}</th>
                                <th>{{ translate('Expiry date') }}</th>
                                <th>{{ translate('Products') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deals as $key => $deal)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center mb-3">
                                            <label class="position-relative mr-2">
                                                <input type="checkbox" name="items[]" class="item-id"
                                                    value="{{ $deal->id }}">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $deal->translation('title', getLocale()) }}</td>
                                    <td>{{ $deal->start_date }}</td>
                                    <td>{{ $deal->end_date }}</td>
                                    <td><a href="{{ route('plugin.flashdeal.products', $deal->id) }}">{{ count($deal->deal_products) }}
                                            {{ translate('Products') }}
                                    </td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-status" data-deal="{{ $deal->id }}"
                                                {{ $deal->status == '1' ? 'checked' : '' }}>
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
                                                    href="{{ route('plugin.flashdeal.edit', ['id' => $deal->id, 'lang' => getDefaultLang()]) }}">
                                                    {{ translate('Edit') }}
                                                </a>
                                                <a href="#" class="delete-deal"
                                                    data-deal="{{ $deal->id }}">{{ translate('Delete') }}</a>
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
    </div>

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.flashdeal.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-deal-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
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
            $("#couponTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#couponTable_wrapper .col-md-6:eq(0)');
            var bulk_actions_dropdown =
                '<div id="bulk-action" class="dataTables_length d-flex"><select class="theme-input-style bulk-action-selection mr-3"><option value="">{{ translate('Bulk Action') }}</option><option value="delete_all">{{ translate('Delete selection') }}</option></select><button class="btn long bulk-action-apply-btn">{{ translate('Apply') }}</button></div>';

            $(bulk_actions_dropdown).insertAfter("#couponTable_wrapper #couponTable_length");
            /**
             * 
             * Change  status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('deal');
                $.post('{{ route('plugin.flashdeal.update.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })

            });
            /**
             * 
             * Select all deals
             **/
            $('.select-all').on('change', function() {
                if ($('.select-all').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            });
            /**
             * 
             * Bulk action
             **/
            $('.bulk-action-apply-btn').on('click', function(e) {
                e.preventDefault();
                let action = $('.bulk-action-selection').val();
                if (action === 'delete_all') {
                    var selected_items = [];
                    $('input[name^="items"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.flashdeal.delete.bulk') }}', {
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
            });
            /**
             * 
             * Delete Coupon
             * 
             * */
            $('.delete-deal').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('deal');
                $("#delete-deal-id").val(id);
                $('#delete-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
