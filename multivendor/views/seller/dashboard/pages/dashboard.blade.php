@php
    $order_repository = new Plugin\TlcommerceCore\Repositories\OrderRepository();
    $total_products = \Plugin\TlcommerceCore\Models\Product::select('id')
        ->where('supplier', auth()->user()->id)
        ->get()
        ->count();
    
    $total_sales = \Plugin\TlcommerceCore\Models\OrderHasProducts::where('seller_id', auth()->user()->id)
        ->select('unit_price', 'quantity')
        ->get()
        ->sum(function ($sale) {
            return $sale->unit_price * $sale->quantity;
        });
    
    $recent_orders_query = \Plugin\TlcommerceCore\Models\Orders::with([
        'customer_info',
        'guest_customer',
        'products' => function ($query) {
            $query->where('seller_id', auth()->user()->id)->select('order_id', 'seller_id', 'quantity', 'delivery_cost', 'unit_price', 'tax');
        },
    ])
        ->select('order_code', 'id', 'created_at', 'total_payable_amount', 'customer_id')
        ->orderBy('id', 'DESC');
    
    $recent_orders_query = $recent_orders_query->whereHas('products', function ($q) {
        $q->where('seller_id', auth()->user()->id);
    });
    $recent_orders = $recent_orders_query->take(6)->get();
    
    $top_products = \Plugin\TlcommerceCore\Models\Product::select(['id', 'name', 'permalink', 'thumbnail_image'])
        ->where('supplier', auth()->user()->id)
        ->withCount(['orders'])
        ->withSum('orders', 'unit_price')
        ->withSum('orders', 'quantity')
        ->orderBy('orders_sum_quantity', 'DESC')
        ->take(5)
        ->get();
    
    $total_earning = \Plugin\Multivendor\Models\SellerEarnings::where('seller_id', auth()->user()->id)
        ->where('status', config('tlecommercecore.seller_earning_status.approve'))
        ->sum('earning');
    
@endphp
@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Dashboard') }}
@endsection
@section('custom_css')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/apex/apexcharts.css') }}">
    <!-- ======= END BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <style>
        .summary-card {
            background: url('/public/backend/assets/img/summery-bg1.png');
            background-size: auto
        }

        .overflow-text {
            display: block;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dash-image {
            min-width: 60px !important;
        }

        .order-couter-item {
            padding: 13px 0px;
        }

        .apexcharts-toolbar {
            top: -30px !important;
        }

        .img-20 {
            width: 20px !important;
            height: 20px !important;
        }
    </style>
@endsection

@section('seller_main_content')
    <div class="row">
        <!--Total Products-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-danger text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Total Products') }}</p>
                            <h2>{{ $total_products }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Products-->
        <!--Total Orders-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-info text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Orders') }}</p>
                            <h2>
                                {{ $order_repository->statusWiseOrderCounter(null, auth()->user()->id) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total Orders-->
        <!--Total Earning-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-light-blue text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Total Earning') }}</p>
                            <h2>{!! currencyExchange($total_earning) !!}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total Earning-->
        <!--Total Sales-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-success text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Total Sales') }}</p>
                            <h2>{!! currencyExchange($total_sales) !!}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total Sales-->
        <!--Sales Reports-->
        <div class="col-xl-8 col-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start align-items-sm-center media">
                        <div
                            class="d-flex justify-content-start justify-content-sm-between align-items-start align-items-sm-center flex-column flex-sm-row mb-sm-n3  media-body">
                            <div class="title-content mb-4 mr-sm-5 mb-sm-0">
                                <h4 class="">{{ translate('Sale Reports') }}</h4>
                            </div>
                            <!-- List Button -->
                            <ul class="list-inline list-button m-0 mr-sm-4">
                                <li class="active chart-switcher" data-type="monthly">{{ translate('Monthly') }}</li>
                                <li class="chart-switcher" data-type="daily">{{ translate('Daily') }}</li>
                            </ul>
                            <!-- End List Button -->
                        </div>
                    </div>
                </div>
                <div id="apex_sales_report_chart"></div>
            </div>
        </div>
        <!--End Sales Reports-->
        <!--Order Counter-->
        <div class="col-xl-4 col-lg-5 grid-item">
            <!-- Card -->
            <div class="card mb-30">
                <div class="card-body p-30">
                    <!-- Transaction History -->
                    <div class="trans-history">
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-list font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Pending') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.pending'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-tick-boxed font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Approved') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.processing'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-box font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Ready to Ship') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.ready_to_ship'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-fast-delivery font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Shipped') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.shipped'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-tick-mark font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Delivered') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.delivered'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                        <!-- Transaction History Item -->
                        <div class="align-items-center border-bottom d-flex justify-content-between mb-2 order-couter-item">
                            <div class="d-flex align-items-center">
                                <div class="img mr-3">
                                    <i class="icofont-close font-20"></i>
                                </div>
                                <div class="content">
                                    <h5>{{ translate('Cancelled') }}</h5>
                                </div>
                            </div>
                            <div class="">
                                <h5>
                                    {{ $order_repository->statusWiseOrderCounter(config('tlecommercecore.order_delivery_status.cancelled'), auth()->user()->id) }}
                                </h5>
                            </div>
                        </div>
                        <!-- End Transaction History Item -->
                    </div>
                    <!-- End Transaction History -->
                </div>
            </div>
            <!-- End Card -->
        </div>
        <!--End order counter-->
        <!--Recent Orders-->
        <div class="col-xl-8 col-lg-7 col-12 mb-20">
            <!-- Card -->
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <div class="title-content">
                            <h4 class="mb-2">{{ translate('Recent Orders') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="style--three table-centered text-nowrap">
                        <thead>
                            <tr>
                                <th>{{ translate('Order ID') }}</th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Total Amount') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($recent_orders->count() > 0)
                                @foreach ($recent_orders as $order)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('plugin.multivendor.seller.dashboard.order.details', ['id' => $order->id]) }}">{{ $order->order_code }}</a>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y h:i A') }}</td>
                                        <td>
                                            @if ($order->customer_info != null)
                                                <p>{{ $order->customer_info->name }}</p>
                                            @else
                                                <p>{{ $order->guest_customer->name }}
                                                    <span class="badge badge-info ml-1">{{ translate('Guest') }}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $total_payable_amount = 0;
                                                foreach ($order->products as $product) {
                                                    $total_payable_amount = $total_payable_amount + $product->totalPayableAmount();
                                                }
                                            @endphp
                                            {!! currencyExchange($total_payable_amount) !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.multivendor.seller.dashboard.order.details', ['id' => $order->id]) }}"
                                                class="details-btn">
                                                Details
                                                <i class="icofont-arrow-right"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Card -->
        </div>
        <!--End recents orders-->
        <!--Top Products-->
        <div class="col-xl-4 col-lg-6">
            <!-- Card -->
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-end justify-content-between mb-3">
                        <div class="">
                            <h4 class="mb-1">{{ translate('Top Products') }}</h4>
                        </div>
                    </div>
                    <div class="product-list">
                        @if ($top_products->count() > 0)
                            @foreach ($top_products as $product)
                                <div class="product-list-item mb-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="img mr-3">
                                            <img src="{{ asset(getFilePath($product->thumbnail_image)) }}"
                                                alt="{{ $product->translation('name', getLocale()) }}"
                                                class="dash-image">
                                        </div>
                                        <div class="content">
                                            <p class="black mb-1 overflow-text text-capitalize">
                                                {{ $product->translation('name', getLocale()) }}</p>
                                            <span class="c3 bold font-14">{!! currencyExchange($product->orders_sum_unit_price * $product->orders_sum_quantity) !!}</span>
                                        </div>
                                    </div>
                                    <p class="font-14">
                                        {{ $product->orders_count != null ? $product->orders_count : 0 }}
                                        {{ $product->orders_count > 1 ? 'Sales' : 'Sale' }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="alert alert-danger text-center">{{ translate('Nothing Found') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>
        <!--End Top Products-->
    </div>
@endsection

@section('custom_scripts')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS ======= -->
    <script src="{{ asset('/public/backend/assets/plugins/apex/apexcharts.min.js') }}"></script>
    <!-- ======= End BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS ======= -->
    <script>
        (function($) {
            "use strict";
            let chart_data_type = "monthly";
            let categories = [];
            //change chart data type
            $(".chart-switcher").on('click', function(e) {
                e.preventDefault();
                $('.chart-switcher').removeClass('active');
                $(this).addClass('active');
                chart_data_type = $(this).data('type');
                getChartData();
            });

            //Get data from api
            function getChartData() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        type: chart_data_type
                    },
                    url: '{{ route('plugin.multivendor..seller.reports.sales.chart') }}',
                    success: function(data) {
                        if (data.success) {
                            categories = data.times;
                            sales_chart.updateSeries([{
                                name: 'Sales',
                                data: data.sales
                            }])

                            sales_chart.updateOptions({
                                xaxis: {
                                    categories: data.times
                                }
                            })
                        }
                    }
                });
            }
            //chart options
            var sales_chart_options = {
                series: [],
                chart: {
                    height: 340,
                    type: 'line',
                    toolbar: {
                        show: true,
                    },
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    dashArray: 3
                },
                colors: ['#FFBA5A', '#8381FD'],
                grid: {
                    borderColor: '#f5f5f5',
                },
                markers: {
                    size: 7,
                    colors: ["#67CF94"],
                    hover: {
                        size: 8,
                    }
                },
                xaxis: {
                    categories: [],
                },
                yaxis: {
                    tickAmount: 4,
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        markers: {
                            size: 5,
                            colors: ["#67CF94"],
                            hover: {
                                size: 5,
                            }
                        },
                    }
                }],
            };
            //Render chart
            var sales_chart = new ApexCharts(document.querySelector(
                "#apex_sales_report_chart"), sales_chart_options);
            sales_chart.render();

            $(document).ready(function() {
                getChartData();
            });
        })(jQuery);
    </script>
@endsection
