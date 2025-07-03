@extends('core::base.layouts.master')
@section('title')
    {{ translate('Cities') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Cities') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.tlcommercecore.shipping.locations.cities.add.new') }}"
                                class="btn long">{{ translate('Add New City') }}</a>
                        </div>
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Filter area-->
                    <form method="get" action="{{ route('plugin.tlcommercecore.shipping.locations.cities.list') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="10" @selected(request()->has('per_page') && request()->get('per_page') == '10')>10</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>{{ translate('All') }}</option>
                        </select>
                        <input type="text" name="search_key" class="theme-input-style mb-2"
                            value="{{ request()->has('search_key') ? request()->get('search_key') : '' }}"
                            placeholder="Enter city name">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>

                    @if (request()->has('search_key'))
                        <a class="btn long btn-danger"
                            href="{{ route('plugin.tlcommercecore.shipping.locations.cities.list') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->
                    <!--Bulk actions-->
                    <select class="theme-input-style bulk-action-selection">
                        <option value="null">{{ translate('Bulk Action') }}</option>
                        <option value="active">{{ translate('Make Active') }}</option>
                        <option value="in_active">{{ translate('Make Inactive') }}</option>
                        <option value="delete_all">{{ translate('Delete selection') }}</option>
                    </select>
                    <button class="btn long btn-warning fire-bulk-action">{{ translate('Apply') }}
                    </button>
                    <!--End bulk actions-->
                </div>
                <div class="table-responsive">
                    <table id="cities_table" class="hoverable text-nowrap">
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
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('State') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($cities->count() > 0)
                                @foreach ($cities as $key => $city)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="item_id[]" class="item-id"
                                                        value="{{ $city->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{ $city->translation('name') }}</td>
                                        <td>{{ $city->state->translation('name') }}</td>
                                        <td>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" class="change-status"
                                                    data-city="{{ $city->id }}" @checked($city->status == config('settings.general_status.active'))>
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
                                                        href="{{ route('plugin.tlcommercecore.shipping.locations.cities.edit', ['id' => $city->id, 'lang' => getDefaultLang()]) }}">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                    <a href="#" class="delete-city"
                                                        data-city="{{ $city->id }}">{{ translate('Delete') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="pgination px-3">
                        {!! $cities->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.locations.cities.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-city-id" name="id">
                        <button type="button" class="btn long btn-danger mt-2"
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
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Change city status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('city');
                $.post('{{ route('plugin.tlcommercecore.shipping.locations.cities.status.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })
            });
            /**
             * 
             * Delete city
             * 
             * */
            $('.delete-city').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('city');
                $("#delete-city-id").val(id);
                $('#delete-modal').modal('show');
            });
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
                        $.post('{{ route('plugin.tlcommercecore.shipping.locations.cities.bulk.action') }}', {
                            _token: '{{ csrf_token() }}',
                            items: selected_items,
                            action: action
                        }, function(data) {
                            if (data.success) {
                                toastr.success('{{ translate('Action Applied Successfully') }}');
                                location.reload();
                            }
                            if (!data.success) {
                                toastr.error('{{ translate('Action Failed') }}');
                            }
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}');
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}');
                }
            });
        })(jQuery);
    </script>
@endsection
