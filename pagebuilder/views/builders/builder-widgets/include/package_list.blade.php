@php
    $plans = getAllPlans();
    $plugins = availablePluginsForTenant();
    $payment_methods = getTenantPaymentGateways();
    $subscribe_btn_text = !empty($subscribe_btn_text) ? $subscribe_btn_text : 'Enroll Now';
@endphp

@foreach ($free_packages as $package)
    @php
        $applicable_plugins = $package->plugins->toArray();
        $applicable_payment_methods = $package->payment_methods->toArray();
    @endphp
    <div class="col-xl-4 col-md-6">
        <div class="price-box">
            <div class="price-head">
                <h3>{{ translatePackageName($package->id) }}</h3>
                <span class="align-items-baseline d-flex">
                    <strong>{{ currencyExchange(0) }}</strong>
                    <span class="d-inline-block">
                        <span class="ml-2">{{ front_translate('For Lifetime') }}</span>
                    </span>
                </span>
            </div>
            <div class="price-body">
                <h5 class="mb-2">{{ front_translate('Applicable Features') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($plugins as $plugin)
                        @if ($plugin->location != 'tlecommercecore')
                            <li>
                                @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                @else
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                @endif

                                {{ str_replace('Tlcommerce', '', $plugin->name) }}
                            </li>
                        @endif
                    @endforeach
                </ul>

                @php
                    $privileges = $package->privileges != null ? $package->privileges : null;
                @endphp
                @if ($privileges != null)
                    <h5 class="mb-2">{{ front_translate('Access Privileges') }}</h5>
                    <ul class="list-unstyled mb-4">
                        @foreach ($privileges as $key => $value)
                            @php
                                $privilege = str_replace('package_privileges_', '', $key);
                                $privilege = ucwords(implode(' ', explode('_', $privilege)));
                            @endphp
                            <li>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h5 class="mb-2">{{ front_translate('Applicable Payment Methods') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($payment_methods as $method => $id)
                        <li>
                            @if (in_array($id, array_column($applicable_payment_methods, 'payment_method')))
                                <i class="fa fa-check" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-times text-danger" aria-hidden="true"></i>
                            @endif
                            @if ($method == 'cod')
                                Cash On Delivery
                            @else
                                {{ ucfirst($method) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="pricing-body-btn">
                    <a href="{{ route('plugin.saas.user.order.plan', ['package' => $package->id, 'plan' => $plan_id]) }}"
                        class="btn-crs s-btn">
                        {{ front_translate($subscribe_btn_text) }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($paid_packages as $package)
    @php
        $applicable_plugins = $package->plugins->toArray();
        $applicable_payment_methods = $package->payment_methods->toArray();
    @endphp
    <div class="col-xl-4 col-md-6">
        <div class="price-box">
            <div class="price-head">
                <h3>{{ $package->name }}</h3>
                <span class="align-items-baseline d-flex">
                    <strong>{{ currencyExchange($package->cost) }}</strong>
                    @if ($package->plan_id != config('saas.plans.lifetime'))
                        <span class="ml-2"> {{ front_translate('For') }} {{ $package->duration }}
                            {{ front_translate('days') }}</span>
                    @else
                        <span class="ml-2">{{ front_translate('For Lifetime') }}</span>
                    @endif
                </span>
            </div>

            <div class="price-body">
                <h5 class="mb-2">{{ front_translate('Applicable Features') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($plugins as $plugin)
                        @if ($plugin->location != 'tlecommercecore')
                            <li>
                                @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                @else
                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                @endif

                                {{ str_replace('Tlcommerce', '', $plugin->name) }}
                            </li>
                        @endif
                    @endforeach
                </ul>

                @php
                    $privileges = $package->privileges != null ? json_decode($package->privileges, true) : null;
                @endphp
                @if ($privileges != null)
                    <h5 class="mb-2">{{ front_translate('Access Privileges') }}</h5>
                    <ul class="list-unstyled mb-4">
                        @foreach ($privileges as $key => $value)
                            @php
                                $privilege = str_replace('package_privileges_', '', $key);
                                $privilege = ucwords(implode(' ', explode('_', $privilege)));
                            @endphp
                            <li>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h5 class="mb-2">{{ front_translate('Applicable Payment Methods') }}</h5>
                <ul class="list-unstyled mb-4">
                    @foreach ($payment_methods as $method => $id)
                        <li>
                            @if (in_array($id, array_column($applicable_payment_methods, 'payment_method')))
                                <i class="fa fa-check" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-times text-danger" aria-hidden="true"></i>
                            @endif
                            @if ($method == 'cod')
                                Cash On Delivery
                            @else
                                {{ ucfirst($method) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="align-items-center d-flex justify-content-between pricing-body-btn">
                    <a href="{{ route('plugin.saas.user.order.plan', ['package' => $package->id, 'plan' => $package->plan_id]) }}"
                        class="btn-crs s-btn">
                        {{ front_translate($subscribe_btn_text) }}
                    </a>
                    @if ($package->trail_period > 0)
                        <a href="{{ route('plugin.saas.user.order.plan', ['package' => $package->id, 'plan' => $package->plan_id, 'is_trial' => 1]) }}"
                            class="btn-link ml-2">
                            Get {{ $package->trail_period }} Days Trial
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
