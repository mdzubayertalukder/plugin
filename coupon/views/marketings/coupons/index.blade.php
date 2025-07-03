@extends('core::base.layouts.master')
@section('title')
    {{ translate('Coupons') }}
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
                        <h4 class="font-20">{{ translate('Coupons') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.tlcommercecore.marketing.coupon.create.new') }}"
                                class="btn long">{{ translate('Add New Coupon') }}</a>
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
                                <th>{{ translate('Code') }}</th>
                                <th>{{ translate('Description') }}</th>
                                <th>{{ translate('Amount Type') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Usage / Limit') }}</th>
                                <th>{{ translate('Expiry Date') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $key => $coupon)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center mb-3">
                                            <label class="position-relative mr-2">
                                                <input type="checkbox" name="coupon_id[]" class="coupon-id"
                                                    value="{{ $coupon->id }}">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->description }}</td>
                                    <td>
                                        @if ($coupon->discount_type == config('tlecommercecore.amount_type.percent'))
                                            <p>{{ translate('Percentage discount') }}</p>
                                        @else
                                            <p>{{ translate('Flat discount') }}</p>
                                        @endif
                                    </td>
                                    <td>{{ $coupon->discount_amount }}</td>
                                    <td>
                                        0/
                                        @if ($coupon->usage_limit_per_coupon > 0)
                                            {{ $coupon->usage_limit_per_coupon }}
                                        @else
                                            &#8734;
                                        @endif

                                    </td>
                                    <td>{{ $coupon->expire_date }}</td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-status" data-coupon="{{ $coupon->id }}"
                                                {{ $coupon->status == '1' ? 'checked' : '' }}>
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
                                                    href="{{ route('plugin.tlcommercecore.marketing.coupon.edit', $coupon->id) }}">
                                                    {{ translate('Edit') }}
                                                </a>
                                                <a href="#" class="delete-coupon"
                                                    data-coupon="{{ $coupon->id }}">{{ translate('Delete') }}</a>
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.marketing.coupon.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-coupon-id" name="id">
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
                '<div id="bulk-action" class="dataTables_length d-flex"><select class="theme-input-style bulk-action-selection mr-3"><option value="">{{ translate('Bulk Action') }}</option><option value="delete_all">{{ translate('Delete selection') }}</option></select><button class="btn long bulk-action-apply-btn" >{{ translate('Apply') }}</button></div>';
            $(bulk_actions_dropdown).insertAfter("#couponTable_wrapper #couponTable_length");

            /**
             * 
             * Change  status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('coupon');
                $.post('{{ route('plugin.tlcommercecore.marketing.coupon.update.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })

            });
            /**
             * 
             * Delete Coupon
             * 
             * */
            $('.delete-coupon').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('coupon');
                $("#delete-coupon-id").val(id);
                $('#delete-modal').modal('show');
            });
            /**
             * 
             * Select all couopns
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".coupon-id").prop("checked", true);
                } else {
                    $(".coupon-id").prop("checked", false);
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
                    $('input[name^="coupon_id"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.marketing.coupon.bulk.delete') }}', {
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
            })
        })(jQuery);
    </script>
@endsection
