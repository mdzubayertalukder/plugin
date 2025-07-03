@extends('core::base.layouts.master')
@section('title')
    {{ translate('Taxes') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <!--Select2-->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--End select2-->
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><a href="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.list') }}" class="black"><i
                    class="icofont-long-arrow-left"></i></a> {{ $zone_info->name }}</h4>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Base Tax') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('plugin.tlcommercecore.ecommerce.update.zone.base.tax') }}">
                        @csrf
                        <input type="hidden" name="zone_id" value="{{ $zone_info->id }}">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black col-12">{{ translate('Base Tax') }} </label>
                            <div class="input-group addon col-lg-4">
                                <input type="text" name="base_tax" class="form-control style--two" placeholder="0.00"
                                    value="{{ $zone_info->base_tax }}">
                                <div class="input-group-append">
                                    <div class="input-group-text px-3 bold">%</div>
                                </div>
                            </div>
                            @if ($errors->has('base_tax'))
                                <div class="invalid-input">{{ $errors->first('base_tax') }}</div>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <button type="submit"
                                    class="btn long update-shipping-from">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ translate('Custom Taxes') }}</h4>
                        <div class="d-flex flex-wrap">
                            <button data-toggle="modal" data-target="#new-tax-modal"
                                class="btn long">{{ translate('New Custom Tax') }}</button>

                        </div>
                    </div>
                </div>
                <div class="card-body pt-30">
                    <div class="table-responsive">
                        <table id="taxTable">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="position-relative mr-2">
                                            <input type="checkbox" name="select_all" class="select-all">
                                            <span class="checkmark"></span>
                                        </label>
                                    </th>
                                    <th>{{ translate('State') }}</th>
                                    <th>{{ translate('Product Collection') }}</th>
                                    <th>{{ translate('Tax Rate') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zone_info->taxes as $key => $tax)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="items[]" class="item-id"
                                                        value="{{ $tax->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($tax->state != null)
                                                {{ $tax->state->translation('name') }}
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tax->product_collection != null)
                                                {{ $tax->product_collection->translation('name') }}
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>{{ $tax->tax }}%</td>
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
                                                    <a href="#" class="edit-tax" data-rate="{{ $tax->tax }}"
                                                        data-tax="{{ $tax->id }}">
                                                        {{ translate('Edit Tax Rate') }}
                                                    </a>
                                                    <a href="#" class="delete-tax"
                                                        data-tax="{{ $tax->id }}">{{ translate('Delete Tax Rate') }}</a>
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
    </div>
    <!--New tax Modal-->
    <div id="new-tax-modal" class="new-tax-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Tax') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-tax-form">
                        <input type="hidden" name="zone_id" value="{{ $zone_info->id }}">
                        <div class="form-row mb-20">
                            <div class="d-flex w-100">
                                <div class="align-items-center d-flex mr-30">
                                    <div class="custom-radio mr-1">
                                        <input type="radio" name="tax_type" id="product-tax" class="select-tax-type"
                                            value="product_tax" checked>
                                        <label for="product-tax"></label>
                                    </div>
                                    <label for="product-tax" class="black font-14 mb-0">{{ translate('Products') }}</label>
                                </div>

                                <div class="align-items-center d-flex">
                                    <div class="custom-radio mr-1">
                                        <input type="radio" name="tax_type" id="shipping-tax" class="select-tax-type"
                                            value="shipping_tax">
                                        <label for="shipping-tax"></label>
                                    </div>
                                    <label for="shipping-tax"
                                        class="black font-14 mb-0">{{ translate('Shipping') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-20 product-input">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Product Collection') }} </label>
                            </div>
                            <div class="col-sm-12">
                                @php
                                    $products_collections = Plugin\TlcommerceCore\Models\ProductCollection::where('status', config('settings.general_status.active'))->get();
                                @endphp
                                <select class="product-select w-100" name="collection">
                                    @foreach ($products_collections as $collection)
                                        <option value="{{ $collection->id }}">
                                            {{ $collection->translation('name', getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2">{{ translate('You can select an existing collection or ') }}<a
                                        href="{{ route('plugin.tlcommercecore.product.collection.list') }}"
                                        class="ml-1 btn-link">{{ translate('Create new collection') }}</a>
                                </p>
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-8">
                                <label class="font-14 bold black">{{ translate('States') }}</label>
                                <select class="city-select w-100" name="state">
                                    <option value="null">{{ translate('Select State') }}</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">
                                            {{ $state->translation('name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="font-14 bold black">{{ translate('Tax') }}</label>
                                <div class="input-group addon">
                                    <input type="number" name="tax" class="form-control style--two"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3 bold">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn long store-tax-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End New tax Modal-->
    <!--Delete Tax Modal-->
    <div id="delete-tax-modal" class="delete-tax-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.ecommerce.zone.tax.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-tax-id" name="id">
                        <input type="hidden" name="zone_id" value="{{ $zone_info->id }}">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Tax Modal-->
    <!--Edit Tax Modal-->
    <div id="edit-tax-modal" class="edit-tax-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Update Tax Rate') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-tax-form">
                        <input type="hidden" id="edit-tax-id" name="id">
                        <input type="hidden" name="zone_id" value="{{ $zone_info->id }}">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Tax Rate') }}</label>
                            <div class="input-group addon">
                                <input type="number" name="tax_rate" id="tax_rate" class="form-control style--two"
                                    placeholder="0.00">
                                <div class="input-group-append">
                                    <div class="input-group-text px-3 bold">%</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit"
                                    class="btn long update-tax-btn">{{ translate('Save Change') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Edit Tax Modal-->
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Tax table
             */
            $("#taxTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#taxTable_wrapper .col-md-6:eq(0)');
            var bulk_actions_dropdown =
                '<div id="bulk-action" class="dataTables_length d-flex"><select class="theme-input-style bulk-action-selection mr-3"><option value="">{{ translate('Bulk Action') }}</option><option value="delete_all">{{ translate('Delete selection') }}</option></select><button class="btn long bulk-action-apply-btn">{{ translate('Apply') }}</button></div>';

            $(bulk_actions_dropdown).insertAfter("#taxTable_wrapper #taxTable_length");
            /**
             *Select product
             *  
             * Select city
             **/
            $(document).ready(function() {
                $('.product-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select Product') }}',
                });
                $('.city-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select Product') }}',
                });
            });
            /**
             * Switch product or shipping wise tax 
             * 
             **/
            $('.select-tax-type').on('change', function(e) {
                let tax_type = $('input[name="tax_type"]:checked').val();
                if (tax_type === 'product_tax') {
                    $('.product-input').removeClass('d-none')
                } else {
                    $('.product-input').addClass('d-none')
                }
            });
            /**
             *Will store new tax
             *  
             **/
            $('.store-tax-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-tax-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.zone.tax.store') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
            /**
             *Edit Tax
             *  
             **/
            $('.edit-tax').on('click', function(e) {
                e.preventDefault();
                let tax_id = $(this).data('tax');
                let rate = $(this).data('rate');
                $("#edit-tax-id").val(tax_id);
                $("#tax_rate").val(rate);
                $("#edit-tax-modal").modal('show');
            });
            /**
             * Update tax
             * 
             **/
            $('.update-tax-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#edit-tax-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.zone.tax.update') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
            /**
             * Delete tax
             * 
             **/
            $('.delete-tax').on('click', function(e) {
                e.preventDefault();
                let tax_id = $(this).data('tax');
                $('#delete-tax-id').val(tax_id);
                $('#delete-tax-modal').modal('show');
            });
            /**
             * 
             * Select all items
             **/
            $('.select-all').on('change', function(e) {
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
                let action = $('.bulk-action-selection').val();
                if (action === 'delete_all') {
                    var selected_items = [];
                    $('input[name^="item"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.ecommerce.zone.tax.delete.bulk') }}', {
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
        })(jQuery);
    </script>
@endsection
