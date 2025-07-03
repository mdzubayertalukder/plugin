@extends('core::base.layouts.master')
@section('title')
    {{ translate('Products Wishlist Report') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12 col-lg-10 m-auto">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Products Wishlist Report') }}</h4>
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Filter area-->
                    <form method="get" action="{{ route('plugin.tlcommercecore.reports.products.wishlist') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="category">
                            <option value="">{{ translate('Product category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request()->has('category') && $category->id == request()->get('category'))>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="search_key" class="theme-input-style mb-2"
                            value="{{ request()->has('search_key') ? request()->get('search_key') : '' }}"
                            placeholder="Enter product name">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>

                    @if (request()->has('search_key') || request()->has('category'))
                        <a class="btn long btn-danger"
                            href="{{ route('plugin.tlcommercecore.reports.products.wishlist') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->

                </div>
                <div class="table-responsive">
                    <table id="conditionTable" class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Num of wish') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data->count() > 0)
                                @php
                                    $total_wishlist = 0;
                                @endphp
                                @foreach ($data as $key => $product)
                                    @php
                                        $total_wishlist += $product->total_wish;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ $product->name }}
                                        </td>
                                        <td>
                                            {{ $product->total_wish }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2" class="text-right border-0">{{ translate('Total Wishlist') }}</th>
                                    <th class="border-0">{{ $total_wishlist }}</th>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $data->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
