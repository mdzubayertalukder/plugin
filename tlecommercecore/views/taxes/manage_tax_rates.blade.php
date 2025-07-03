@extends('core::base.layouts.master')
@section('title')
    {{ translate('Tax Rates') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <style>
        .select2-container {
            width: 100% !important;
        }

        .item-toggle-btn.collapsed {
            transform: rotate(180deg);
        }

        .input-group {
            min-width: 150px !important;
        }

        .location-box {
            min-height: 400px;
        }

        .edit-location-box {
            min-height: 400px;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-money"></i>
            {{ translate('Tax Rates') }}
            <span>[{{ translate('Profile:') }} {{ $profile->title }}]</span>
        </h4>
    </div>
    <div class="row">
        <div class="col-12 mb-20">
            @if (getEcommerceSetting('enable_tax_in_checkout') != config('settings.general_status.active'))
                <p class="alert alert-danger font-13">
                    {{ translate('Product tax is disabled. You can enable tax from') }}
                    <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}"
                        class="btn-link">{{ translate('Tax Settings') }}
                    </a>
                </p>
            @else
                <p class="alert alert-info font-13">
                    {{ translate('You can disable tax from') }}
                    <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}"
                        class="btn-link">{{ translate('Tax Settings') }}
                    </a>
                </p>
            @endif
        </div>
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Tax Rates') }}</h4>
                        <div class="d-flex flex-wrap">
                            <button class="btn long open-tax-rate-create-modal">{{ translate('Create Tax Rates') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="px-2 filter-area d-flex align-items-center">
                        <!--Filter area-->
                        <form method="get"
                            action="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.manage.rates', ['id' => $profile->id]) }}">
                            <select class="theme-input-style mb-2" name="per_page">
                                <option value="">{{ translate('Per page') }}</option>
                                <option value="10" @selected(request()->has('per_page') && request()->get('per_page') == '10')>10</option>
                                <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                                <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                                <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>{{ translate('All') }}</option>
                            </select>
                            <input type="text" name="search_key" class="theme-input-style mb-2"
                                value="{{ request()->has('search_key') ? request()->get('search_key') : '' }}"
                                placeholder="Enter country name, Post Code or Tax Name">
                            <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                        </form>

                        @if (request()->has('search_key'))
                            <a class="btn long btn-danger"
                                href="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.manage.rates', ['id' => $profile->id]) }}">
                                {{ translate('Clear Filter') }}
                            </a>
                        @endif
                        <!--End filter area-->
                        <!--Bulk actions-->
                        <select class="theme-input-style bulk-action-selection">
                            <option value="null">{{ translate('Bulk Action') }}</option>
                            <option value="change_rate">{{ translate('Change Tax Rate') }}</option>
                            <option value="change_post_code">{{ translate('Change Post Code') }}</option>
                            <option value="change_name">{{ translate('Change Tax Name') }}</option>
                            <option value="delete_all">{{ translate('Delete selection') }}</option>
                        </select>
                        <button class="btn long btn-warning fire-bulk-action">{{ translate('Apply') }}
                        </button>
                        <!--End bulk actions-->
                    </div>
                    <div class="table-responsive">

                        <table class="hoverable text-nowrap">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <label class="position-relative">
                                                <input type="checkbox" name="select_all" class="checked-all-items">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </th>
                                    <th>{{ translate('Tax Name') }}</th>
                                    <th>{{ translate('Tax Rate') }}</th>
                                    <th>{{ translate('Country') }}</th>
                                    <th>{{ translate('State') }}</th>
                                    <th>{{ translate('City') }}</th>
                                    <th>{{ translate('Postal Code') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($tax_rates) > 0)
                                    @foreach ($tax_rates as $key => $rate)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center mb-3">
                                                    <label class="position-relative mr-2">
                                                        <input type="checkbox" name="item_id[]" class="item-id"
                                                            value="{{ $rate->id }}">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ $rate->tax_name }}</td>
                                            <td>{{ $rate->tax_rate }}%</td>
                                            <td>
                                                {{ $rate->country != null ? $rate->country->translation('name') : '' }}
                                            </td>
                                            <td>
                                                {{ $rate->state != null ? $rate->state->translation('name') : '' }}
                                            </td>
                                            <td>
                                                {{ $rate->city != null ? $rate->city->translation('name') : '' }}
                                            </td>
                                            <td>{{ $rate->postal_code }}</td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7">
                                            <p class="alert alert-danger text-center">{{ translate('No tax rates found') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="pgination px-3 mt-20">
                            {{ $tax_rates->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Tax Rates Create Modal-->
    <div id="tax-rate-create-modal" class="tax-rate-create-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Create Tax Rates') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-tax-rates-create-form">
                        @csrf
                        <div class="form-row mb-10">
                            <div class="col-sm-12">
                                <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                                <label class="font-14 bold black mb-0">{{ translate('Tax Name') }} </label>
                                <input type="text" name="tax_name" class="theme-input-style"
                                    placeholder="{{ translate('Enter Tax Name') }}">
                            </div>
                        </div>
                        <div class="form-row mb-10">
                            <label class="font-14 bold black mb-0">{{ translate('Tax Rate') }} </label>
                            <input type="number" name="tax_rate" class="theme-input-style"
                                placeholder="{{ translate('Enter Tax Rate') }}">
                        </div>
                        <div class="form-row mb-10">
                            <label class="font-14 bold black mb-0">{{ translate('Post Code') }} </label>
                            <input type="text" name="postal_code" class="theme-input-style"
                                placeholder="{{ translate('Enter Post Code') }}">
                        </div>
                        <div class="form-row mb-10">
                            <div class="col-sm-12 my-auto">
                                <label class="font-14 bold black">{{ translate('Search Location') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <div class="input-group addon ov-hidden">
                                    <input type="text" name="location_search" id="location_search"
                                        class="form-control style--two" value=""
                                        placeholder="{{ translate('Search') }}">
                                    <div class="input-group-append search-btn">
                                        <span class="input-group-text bg-light pointer">
                                            <i class="icofont-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12 location-box">
                                <ul class="cl-start-wrap pl-1 location-options">

                                </ul>
                                <div class="d-flex justify-content-center loader">
                                    <button type="button" class="btn sm">{{ translate('Load More') }}</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long create-tax-rates-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax Rates Create Modal-->

    <!--Tax Rate  edit Modal-->
    <div id="tax-rate-edit-modal" class="tax-rate-edit-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Change Tax Rate') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tax-rate-value-update-form">
                        <div class="form-row mb-20">
                            <input type="hidden" name="id" value="{{ $profile->id }}">
                            <input type="hidden" name="selected_items" id="changeAbleTaxValueItems">
                            <label class="font-14 bold black">{{ translate('Tax Rate') }} </label>
                            <input type="number" name="rate" class="theme-input-style"
                                placeholder="{{ translate('Enter Tax Rate') }}">
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long update-tax-rate-value-btn">
                                    {{ translate('Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax Rate  edit Modal-->

    <!--Tax Rate Name Edit Modal-->
    <div id="tax-rate-name-edit-modal" class="tax-rate-name-edit-modal modal fade show" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Update Tax Name') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tax-rate-name-update-form">
                        <div class="form-row mb-20">
                            <input type="hidden" name="id" value="{{ $profile->id }}">
                            <input type="hidden" name="selected_items" id="changeAbleTaxNameItems">
                            <label class="font-14 bold black">{{ translate('Tax Name') }} </label>
                            <input type="text" name="name" class="theme-input-style"
                                placeholder="{{ translate('Enter Tax Name') }}">
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long update-tax-rate-name-btn">
                                    {{ translate('Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax Rate Name Edit Modal-->

    <!--Tax Rate  edit Modal-->
    <div id="tax-post-code-edit-modal" class="tax-post-code-edit-modal modal fade show" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Change Post Code') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tax-post-code-value-update-form">
                        <div class="form-row mb-20">
                            <input type="hidden" name="id" value="{{ $profile->id }}">
                            <input type="hidden" name="selected_items" id="changeAblePostCodeItems">
                            <label class="font-14 bold black">{{ translate('Post Code') }} </label>
                            <input type="number" name="post_code" class="theme-input-style"
                                placeholder="{{ translate('Enter Post Code') }}">
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long update-tax-post-code-value-btn">
                                    {{ translate('Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax Rate  edit Modal-->

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST"
                        action="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.delete.profile') }}">
                        @csrf
                        <input type="hidden" id="delete-id" name="id">
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
        let location_page_number = 1;
        let searched_location_page_number = 1;
        let searched_location_all_page_count = 0;
        /**
         * Vat and tax table
         */
        (function($) {
            "use strict";
            $("#taxTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#taxTable_wrapper .col-md-6:eq(0)');

            $(document).ready(function() {
                //Location List Expand
                $(document).on('click', '.cl-item:not(.cl-item-no-sub) > .cl-label-wrap .cl-label-tools',
                    function() {
                        $(this).parent().parent().parent().toggleClass('cl-item-open');
                    });
            });

            //Open create tax rate modal
            $('.open-tax-rate-create-modal').on('click', function(e) {
                e.preventDefault();
                location_page_number = 1;
                searched_location_page_number = 1;
                searched_location_all_page_count = 0;
                $('.location-options').html('');
                getCountriesOptions();
            });
            /**
             * Get Location options
             * 
             **/
            function getCountriesOptions() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        page: location_page_number,
                        perPage: 1,
                    },
                    url: '{{ route('plugin.tlcommercecore.shipping.location.ul.list') }}',
                    success: function(response) {
                        if (response.success) {
                            $('.location-options').append(response.list);
                            location_page_number = location_page_number + 1;
                            $('.tax-rate-create-modal').modal('show');
                        }
                    }
                });
            }
            /**
             * 
             * Checked all items
             **/
            $('.checked-all-items').on('change', function(e) {
                if ($('.checked-all-items').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            });

            // Search field keyup event ajax call
            $('#location_search').on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let value = $(this).val();
                    searched_location_page_number = 1;
                    if (value && value.length > 0) {
                        getSearchedLocations(value);
                    } else {
                        getCountriesOptions();
                    }
                }
            });

            // search button click ajax call
            $('.search-btn').on('click', function() {
                let value = $('#location_search').val();
                searched_location_page_number = 1;
                if (value && value.length > 0) {
                    getSearchedLocations(value);
                }
            })
            /**
             * Load location box
             **/
            $(document).on('click', '.loader button', function() {
                let searchKey = $('#location_search').val();
                if (searchKey && searchKey.length > 0) {
                    if (searched_location_all_page_count == 0 || searched_location_page_number <=
                        searched_location_all_page_count) {
                        getSearchedLocations(searchKey);
                    }
                } else {
                    getCountriesOptions();
                }
            });

            /**
             * Get Searched Location options
             * 
             **/
            function getSearchedLocations(searchKey) {
                if (searched_location_page_number == 1) {
                    $('.location-options').html('');
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        page: searched_location_page_number,
                        perPage: 1,
                        key: searchKey,
                    },
                    url: '{{ route('plugin.tlcommercecore.shipping.search.location.ul.list') }}',
                    success: function(response) {
                        if (response.success) {
                            if (response.found) {
                                $('.location-options').append(response.list);
                                searched_location_page_number = searched_location_page_number + 1;
                                searched_location_all_page_count = response.totalPage;

                                if (searched_location_page_number > response.totalPage) {
                                    $('.loader > button').prop('disabled', true);
                                } else {
                                    $('.loader > button').prop('disabled', false);
                                }
                            } else {
                                let notFoundKey = "{{ translate('Not Found') }}";
                                $('.location-options').html(`
                                <div class="text-center mt-5"> ${notFoundKey} </div>
                            `);
                            }
                        }
                    }
                });
            }


            /**
             *create new tax rates
             * 
             **/
            $('.create-tax-rates-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-tax-rates-create-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.store.rates') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New tax rates created successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('New tax rates create failed');
                        }
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
             * 
             * Bulk action
             **/
            $('.fire-bulk-action').on('click', function(e) {
                let action = $('.bulk-action-selection').val();
                if (action != 'null') {
                    var selected_items = [];
                    $('input[name^="item_id"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });

                    if (selected_items.length > 0) {

                        if (action == 'change_rate') {
                            $("#changeAbleTaxValueItems").val(JSON.stringify(selected_items))
                            $("#tax-rate-edit-modal").modal('show');
                        }

                        if (action == 'change_post_code') {
                            $("#changeAblePostCodeItems").val(JSON.stringify(selected_items))
                            $("#tax-post-code-edit-modal").modal('show');
                        }

                        if (action == 'change_name') {
                            $("#changeAbleTaxNameItems").val(JSON.stringify(selected_items))
                            $("#tax-rate-name-edit-modal").modal('show');
                        }

                        if (action === 'delete_all') {
                            $.post('{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.rates.bulk.action') }}', {
                                _token: '{{ csrf_token() }}',
                                items: JSON.stringify(selected_items),
                                action: action
                            }, function(data) {
                                if (data.success) {
                                    toastr.success('Selected items deleted successfully');
                                    location.reload();
                                }
                                if (!data.success) {
                                    toastr.error('Action failed');
                                }
                            })
                        }

                    } else {
                        toastr.error('{{ translate('No Item Selected') }}');
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}');
                }
            });

            /**
             *Will update tax rate value
             * 
             **/
            $('.update-tax-rate-value-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#tax-rate-value-update-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.update.rates.value') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Tax rate updated successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('Tax rate update failed');
                        }
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
             *Will update post code
             * 
             **/
            $('.update-tax-post-code-value-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#tax-post-code-value-update-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.update.rates.post.code') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Post code updated successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('Post code update failed');
                        }
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
             *Will update tax name
             * 
             **/
            $('.update-tax-rate-name-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#tax-rate-name-update-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.update.rates.name') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Tax name updated successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('Tax name update failed');
                        }
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
