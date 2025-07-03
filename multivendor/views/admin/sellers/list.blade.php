@extends('core::base.layouts.master')
@section('title')
    {{ translate('Sellers') }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/css/ratings.css') }}" rel="stylesheet" />
    <style>
        .product-title {
            max-width: 150px;
            display: inline-block;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Sellers') }}</h4>
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Filter area-->
                    <form method="get" action="{{ route('plugin.multivendor.admin.seller.list') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="seller_status">
                            <option value="">{{ translate('Seller Status') }}</option>
                            <option value="{{ config('settings.general_status.active') }}" @selected(request()->has('seller_status') && request()->get('seller_status') == config('settings.general_status.active'))>
                                {{ translate('Active') }}
                            </option>
                            <option value="{{ config('settings.general_status.in_active') }}" @selected(request()->has('seller_status') && request()->get('seller_status') == config('settings.general_status.in_active'))>
                                {{ translate('Inactive') }}
                            </option>
                        </select>
                        <input type="text" name="search_key" class="theme-input-style mb-2"
                            value="{{ request()->has('search_key') ? request()->get('search_key') : '' }}"
                            placeholder="Type Name , phone or Email">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>
                    @if (request()->has('search_key') || request()->has('payment_status'))
                        <a class="btn long btn-danger" href="{{ route('plugin.multivendor.admin.seller.list') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->

                </div>
                <div class="table-responsive">
                    <table id="productTable1" class="hoverable">
                        <thead>
                            <tr>
                                <th>{{ translate('Seller Info') }}</th>
                                <th>{{ translate('Shop Info') }}</th>
                                <th>{{ translate('Current Balance') }}</th>
                                <th>{{ translate('Seller Status') }} </th>
                                <th>{{ translate('Shop Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($sellers->count() > 0)
                                @foreach ($sellers as $key => $seller)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar img-50 mr-10">
                                                    <a
                                                        href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $seller->id]) }}">
                                                        <img src="{{ asset(getFilePath($seller->image)) }}"
                                                            alt="{{ $seller->name }}" class="img-fluid radius-50 w-100">
                                                    </a>
                                                </div>
                                                <div class="info">
                                                    <a
                                                        href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $seller->id]) }}">
                                                        <h4 class="name">{{ $seller->name }}</h4>
                                                    </a>
                                                    @if ($seller->shop != null)
                                                        <p class="phone mb-0">{{ translate('Phone') }}:
                                                            {{ $seller->shop->seller_phone }}</p>
                                                    @endif
                                                    <p class="email">{{ translate('Email') }}: {{ $seller->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex lign-items-center">
                                                @if ($seller->shop != null)
                                                    <div class="avatar img-50 mr-10">
                                                        <img src="{{ asset(getFilePath($seller->shop->logo)) }}"
                                                            alt="{{ $seller->shop->shop_name }}"
                                                            class="img-fluid radius-50 w-100">
                                                    </div>
                                                    <div class="info">
                                                        <h4 class="name">{{ $seller->shop->shop_name }}</h4>
                                                        <p class="phone mb-0">{{ translate('Phone') }}:
                                                            {{ $seller->shop->shop_phone }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            {!! currencyExchange($seller->sellerWithdrawableBalance()) !!}
                                        </td>

                                        <td>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" class="change-seller-status"
                                                    data-seller="{{ $seller->id }}"
                                                    {{ $seller->status == '1' ? 'checked' : '' }}>
                                                <span class="control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            @if ($seller->shop != null)
                                                <label class="switch glow primary medium">
                                                    <input type="checkbox" class="change-shop-status"
                                                        data-shop="{{ $seller->shop->id }}"
                                                        {{ $seller->shop->status == '1' ? 'checked' : '' }}>
                                                    <span class="control"></span>
                                                </label>
                                            @endif
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
                                                        href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $seller->id]) }}">
                                                        {{ translate('Details') }}
                                                    </a>
                                                    <a href="/shop/{{ $seller->shop->shop_slug }}" target="_blank">
                                                        {{ translate('Visit Shop') }}
                                                    </a>
                                                    <a href="#" class="delete-seller"
                                                        data-seller="{{ $seller->id }}">{{ translate('Delete') }}</a>
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
                        {{ $sellers->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') }}
                    </div>
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
                    <form method="POST" action="{{ route('plugin.multivendor.admin.seller.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-seller-id" name="id">
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
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Change shop  status 
             * 
             * */
            $('.change-shop-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('shop');
                $.post('{{ route('plugin.multivendor.admin.seller.list.change.shop.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success("Status updated successfully");
                        location.reload();
                    } else {
                        toastr.error('Status update failed');
                    }
                })

            });
            /**
             * 
             * Change seller status 
             * 
             * */
            $('.change-seller-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('seller');
                $.post('{{ route('plugin.multivendor.admin.seller.list.change.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success("Status updated successfully");
                        location.reload();
                    } else {
                        toastr.error('Status update failed');
                    }
                })

            });
            /**
             * Display delete modal
             * 
             **/
            $('.delete-seller').on('click', function(e) {
                e.preventDefault();
                let seller_id = $(this).data('seller');
                $("#delete-seller-id").val(seller_id);
                $("#delete-modal").modal('show');

            });
        })(jQuery);
    </script>
@endsection
