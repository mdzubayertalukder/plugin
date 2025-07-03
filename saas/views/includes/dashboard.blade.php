@if (auth()->user()->can('Manage Dashboard'))
    @php

        $total_stores = \Plugin\Saas\Models\SaasAccount::count();
        $total_customers = \Core\Models\User::where('user_type', config('saas.user_type.subscriber'))->count();
        $total_packages = \Plugin\Saas\Models\Package::count();
        $total_plans = \Plugin\Saas\Models\PackagePlan::count();
        $total_amount = \Plugin\Saas\Models\PaymentHistory::where('status', 'paid')->sum('final_amount');

        $new_customers = \Core\Models\User::where('user_type', config('saas.user_type.subscriber'))
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
        $domain_request = \Plugin\Saas\Models\CustomDomain::OrderBy('id', 'desc')->take(7)->get();

        $match_case = [];
        $data = [
            'tl_saas_accounts.id',
            'tl_saas_accounts.status',
            'tl_saas_accounts.package_id',
            'tl_saas_accounts.package_plan as plan_id',
            'tl_saas_accounts.membership_type',
            'tl_saas_accounts.store_name',
            'tl_saas_accounts.valid_till',
            'tl_saas_accounts.tenant_id',

            'tl_saas_packages.name as package',
            'tl_saas_package_plans.name as plan',
            DB::raw('2 as subscription_status'),
        ];

        $sub_repo = new \Plugin\Saas\Repositories\SubscriptionRepository();
        $stores = $sub_repo->getSaasAccountDetails($match_case, $data)->take(7)->get();

        $notify_before_expired_days = \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting(
            'notify_before_expired_days',
        );

        foreach ($stores as $store) {
            if ($store->valid_till != null) {
                $current_date = new DateTime(); // Current date and time
                $valid_till = new DateTime($store->valid_till);

                $interval = $current_date->diff($valid_till);
                $diffInDays = $interval->days;

                if ($diffInDays <= $notify_before_expired_days) {
                    $store->subscription_status = 1;
                }

                if ($diffInDays <= 0) {
                    $store->subscription_status = 0;
                }
            }
            $domain = DB::table('domains')
                ->where('tenant_id', '=', $store->tenant_id)
                ->first();
            $store->domain = $domain != null ? $domain->domain : null;
        }

        $dashboard_data = [
            'total_stores' => $total_stores,
            'total_customers' => $total_customers,
            'total_packages' => $total_packages,
            'total_plans' => $total_plans,
            'total_amount' => $total_amount,
            'new_customers' => $new_customers,
            'stores' => $stores,
            'domain_request' => $domain_request,
        ];
        $dashboard_details = $dashboard_data;
        $total_stores = $dashboard_details['total_stores'];
        $total_customers = $dashboard_details['total_customers'];
        $total_packages = $dashboard_details['total_packages'];
        $total_plans = $dashboard_details['total_plans'];
        $total_amount = $dashboard_details['total_amount'];

        $new_customers = $dashboard_details['new_customers'];
        $stores = $dashboard_details['stores'];
        $domain_request = $dashboard_details['domain_request'];
    @endphp
    @push('head')
        {{-- Push custom script or style into head tag --}}
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

            .s-height {
                height: 400px;
            }
        </style>
    @endpush
    @push('script')
        {{-- Push custom script or style bottom of body tag --}}
    @endpush
    <div class="row">
        <!--Total Customers-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-primary text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Subscribers') }}</p>
                            <h2>{{ $total_customers }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total customers-->

        <!--Total Orders-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-info text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Stores') }}</p>
                            <h2>
                                {{ $total_stores }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total Orders-->

        <!--Total Products-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-danger text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Packages') }}</p>
                            <h2>{{ $total_packages }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Products-->

        <!--Total Sales-->
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-30 bg-success text-white">
                <div class="state">
                    <div class="align-items-center d-flex justify-content-center">
                        <div class="state-content text-center">
                            <p class="font-14 mb-2">{{ translate('Total Amount') }}</p>
                            <h2>{{ currencyExchange($total_amount, true, null, false) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End total Sales-->

        <!--Sales Reports-->
        <div class="col-md-8 col-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start align-items-sm-center media">
                        <div
                            class="d-flex justify-content-start justify-content-sm-between align-items-start align-items-sm-center flex-column flex-sm-row mb-sm-n3 media-body">
                            <div class="title-content mb-4 mr-sm-5 mb-sm-0">
                                <h4 class="">{{ translate('Payment Reports') }}</h4>
                            </div>
                            <ul class="list-inline list-button m-0 mr-sm-4">
                                <li class="active chart-switcher" data-type="monthly">{{ translate('Monthly') }}</li>
                                <li class="chart-switcher" data-type="daily">{{ translate('Daily') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="apex_sales_report_chart"></div>
            </div>
        </div>
        <!--End Sales Reports-->

        <!-- New Customers -->
        <div class="col-md-4 col-12 mb-30">
            <div class="card mb-30 h-100">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-1">{{ translate('Latest Subscribers') }}</h4>
                </div>
                <div class="card-body">

                    <div class="customer-list">
                        @if ($new_customers->count() > 0)
                            @foreach ($new_customers as $customer)
                                <div class="product-list-item mb-20 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="img mr-3">
                                            <img src="{{ asset(getFilePath($customer->image, true)) }}"
                                                alt="{{ $customer->name }}">
                                        </div>
                                        <div class="content">
                                            <a href="{{ route('plugin.saas.subscriber.details', ['id' => $customer->id]) }}"
                                                class="black mb-1">{{ $customer->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="alert alert-danger text-center">{{ translate('Nothing Found') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- End New Customers -->

        <!-- Latest Stores -->
        <div class="col-md-6 col-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Latest Stores') }}</h4>
                    </div>
                </div>
                <div class="table-responsive s-height">
                    <table class="hoverable text-nowrap border-top2 " id="store_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }}</th>
                                <th>{{ translate('Package Name') }}</th>
                                <th>{{ translate('Plan Name') }}</th>
                                <th>{{ translate('Valid Untill') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($stores as $store)
                                <tr>
                                    <td>{{ $key }}.</td>
                                    <td> {{ $store->store_name }} </td>
                                    <td>{{ translatePackageName($store->package_id) }}</td>
                                    <td>
                                        @if ($store->plan == null)
                                            <i class="icofont-ban"></i>
                                        @else
                                            {{ translate($store->plan) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($store->valid_till == null || $store->plan_id == config('saas.plans.lifetime'))
                                            {{ translate('Lifetime') }}
                                        @else
                                            {{ $store->valid_till }}
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Latest Stores-->

        <!-- Latest custom domain request -->
        <div class="col-md-6 col-12 mb-20">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Latest Domains') }}</h4>
                    </div>
                </div>
                <div class="table-responsive s-height">
                    <table class="hoverable text-nowrap border-top2" id="domain_request_history">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }} </th>
                                <th>{{ translate('Requested For Domain') }} </th>
                                <th>{{ translate('Requested Domain') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($domain_request as $domain)
                                @php
                                    $status = '';
                                    $class = 'badge-danger';
                                    if ($domain->status == 0) {
                                        $status = 'Pending';
                                        $class = 'badge-warning';
                                    }
                                    if ($domain->status == 1) {
                                        $status = 'Approved';
                                        $class = 'badge-success';
                                    }
                                    if ($domain->status == 2) {
                                        $status = 'Cancelled';
                                        $class = 'badge-danger';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $domain->saasAccount->store_name }}</td>
                                    <td>{{ $domain->current_domain }} <span
                                            class="badge mb-2 {{ $class }}">{{ $status }}</span></td>
                                    <td>{{ $domain->requested_domain }}</td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Latest Custom domain request -->
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
                let chart_data_type = "monthly";
                let categories = [];

                //chart options
                var sales_chart_options = {
                    series: [],
                    chart: {
                        height: 440,
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
                        labels: {
                            formatter: function(val, index) {
                                return val.toFixed(2);
                            }
                        }
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
                        url: '{{ route('plugin.saas.dash.sales.chart') }}',
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
            })(jQuery);
        </script>
    @endpush
@endif
