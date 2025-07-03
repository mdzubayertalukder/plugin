@php
    $plans = getAllPlans();
    $plugins = availablePluginsForTenant();
    $payment_methods = getTenantPaymentGateways();
@endphp
@foreach ($free_packages as $package)
    @php
        $applicable_plugins = $package->plugins->toArray();
        $applicable_payment_methods = $package->payment_methods->toArray();
    @endphp
    <div class="col-lg-4">
        <div class="price-box mb-30">
            <div class="price-header bg-primary">
                <div class="p_header-img text-white w-100 flex-column align-items-start">
                    <h4 class="text-center mb-2">{{ translatePackageName($package->id) }}</h4>
                    <div class="d-flex align-items-end mt-2">
                        <h1>{{ currencyExchange(0) }}</h1>
                        <p class="ml-2">{{ translate('For Lifetime') }}</p>
                    </div>
                </div>
            </div>
            <div class="price-body">
                <h5 class="mb-3">{{ translate('Applicable Features') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($plugins as $plugin)
                        @if ($plugin->type != 'saas' && $plugin->location != 'tlecommercecore')
                            <li>
                                @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                    <i class="icofont-check"></i>
                                @else
                                    <i class="icofont-close text-danger"></i>
                                @endif

                                {{ $plugin->name }}
                            </li>
                        @endif
                    @endforeach
                </ul>

                @php
                    $privileges = $package->privileges != null ? $package->privileges : null;
                @endphp
                @if ($privileges != null)
                    <h5 class="mb-3">{{ translate('Access Privileges') }}</h5>
                    <ul class="list-unstyled mb-4">
                        @foreach ($privileges as $key => $value)
                            @php
                                $privilege = str_replace('package_privileges_', '', $key);
                                $privilege = ucwords(implode(' ', explode('_', $privilege)));
                            @endphp
                            <li>
                                <i class="icofont-check"></i>
                                {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                            </li>
                        @endforeach
                    </ul>
                @endif


                <h5 class="mb-3">{{ translate('Applicable Payment Methods') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($payment_methods as $method => $id)
                        <li>
                            @if (in_array($id, array_column($applicable_payment_methods, 'payment_method')))
                                <i class="icofont-check"></i>
                            @else
                                <i class="icofont-close text-danger"></i>
                            @endif
                            @if ($method == 'cod')
                                Cash On Delivery
                            @else
                                {{ ucfirst($method) }}
                            @endif
                        </li>
                    @endforeach
                </ul>

                <a class="btn btn-warning sm"
                    href="{{ route('plugin.saas.edit.package', $package->id) }}">{{ translate('Edit') }}</a>
                <a class="btn btn-danger sm" href="#"
                    onclick="deletePackage('{{ $package->id }}')">{{ translate('Delete') }}</a>
            </div>
        </div>
    </div>
@endforeach
@foreach ($paid_packages as $package)
    @php
        $applicable_plugins = $package->plugins->toArray();
        $applicable_payment_methods = $package->payment_methods->toArray();
    @endphp
    <div class="col-lg-4">
        <div class="price-box mb-30">
            <div class="price-header bg-primary">
                <div class="p_header-img text-white w-100 flex-column align-items-start">
                    <h4 class="text-center">{{ translatePackageName($package->id) }}</h4>
                    <div class="d-flex align-items-end mt-2">
                        <h1>{{ currencyExchange($package->cost) }}</h1>
                        @if ($package->plan_id != config('saas.plans.lifetime'))
                            <p class="ml-2"> {{ translate('For') }} {{ $package->duration }}
                                {{ translate('days') }}</p>
                        @else
                            <p class="ml-2">{{ translate('For Lifetime') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="price-body">
                <h5 class="mb-3">{{ translate('Applicable Features') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($plugins as $plugin)
                        @if ($plugin->type != 'saas' && $plugin->location != 'tlecommercecore')
                            <li>
                                @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                    <i class="icofont-check"></i>
                                @else
                                    <i class="icofont-close text-danger"></i>
                                @endif

                                {{ $plugin->name }}
                            </li>
                        @endif
                    @endforeach
                </ul>

                @php
                    $privileges = $package->privileges != null ? json_decode($package->privileges, true) : null;
                @endphp
                @if ($privileges != null)
                    <h5 class="mb-3">{{ translate('Access Privileges') }}</h5>
                    <ul class="list-unstyled mb-4">
                        @foreach ($privileges as $key => $value)
                            @php
                                $privilege = str_replace('package_privileges_', '', $key);
                                $privilege = ucwords(implode(' ', explode('_', $privilege)));
                            @endphp
                            <li>
                                <i class="icofont-check"></i>
                                {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h5 class="mb-3">{{ translate('Applicable Payment Methods') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($payment_methods as $method => $id)
                        <li>
                            @if (in_array($id, array_column($applicable_payment_methods, 'payment_method')))
                                <i class="icofont-check"></i>
                            @else
                                <i class="icofont-close text-danger"></i>
                            @endif
                            @if ($method == 'cod')
                                Cash On Delivery
                            @else
                                {{ ucfirst($method) }}
                            @endif
                        </li>
                    @endforeach
                </ul>

                <a class="btn btn-warning sm"
                    href="{{ route('plugin.saas.edit.package', $package->id) }}">{{ translate('Edit') }}</a>
                <a class="btn btn-danger sm" href="#" onclick="deletePackage('{{ $package->id }}')">
                    {{ translate('Delete') }}
                </a>
                @if (isActivePluging('paddle-recurring'))
                    @php
                        $plan_info = DB::table('tl_saas_package_has_plans')
                            ->where('package_id', $package->id)
                            ->where('plan_id', $package->plan_id)
                            ->select('paddle_plan_id')
                            ->first();
                    @endphp
                    @if ($plan_info != null)
                        @if ($plan_info->paddle_plan_id != null)
                            <p class="btn btn-success sm">{{ translate('Connected With Padddle') }}</p>
                        @else
                            <a href="{{ route('paddle.recurring.create.subscriptions.plan', ['package' => $package->id, 'plan' => $package->plan_id]) }}"
                                class="btn btn-info sm">
                                {{ translate('Connect With Padddle') }}
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
@endforeach
