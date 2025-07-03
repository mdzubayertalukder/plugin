@php
    $user_type = Auth::user()->user_type;
@endphp

@if ($user_type != config('saas.user_type.subscriber'))
    @if (auth()->user()->can('Manage Packages'))
        <li
            class="{{ Request::routeIs(['plugin.saas.package.plans', 'plugin.saas.create.package', 'plugin.saas.edit.package', 'plugin.saas.packages']) ? 'active sub-menu-opened' : '' }}">
            <a href="#">
                <i class="icofont-package"></i>
                <span class="link-title">{{ translate('Packages') }}</span>
            </a>
            <ul class="nav sub-menu">
                <li class="{{ Request::routeIs('plugin.saas.create.package') ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.create.package') }}">{{ translate('Add New Package') }}</a>
                </li>
                <li class="{{ Request::routeIs('plugin.saas.packages') ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.packages') }}">{{ translate('All Packages') }}</a>
                </li>
                <li class="{{ Request::routeIs('plugin.saas.package.plans') ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.package.plans') }}">{{ translate('Package Plans') }}</a>
                </li>
            </ul>
        </li>
    @endif

    @if (auth()->user()->can('Manage Coupons'))
        <li
            class="{{ Request::routeIs(['plugin.saas.create.coupons', 'plugin.saas.edit.coupon', 'plugin.saas.create.coupons', 'plugin.saas.coupons']) ? 'active sub-menu-opened' : '' }}">
            <a href="#">
                <i class="icofont-gift"></i>
                <span class="link-title">{{ translate('Coupons') }}</span>
            </a>
            <ul class="nav sub-menu">
                <li class="{{ Request::routeIs('plugin.saas.create.coupons') ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.create.coupons') }}">{{ translate('Create New Coupon') }}</a>
                </li>
                <li class="{{ Request::routeIs('plugin.saas.coupons') ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.coupons') }}">{{ translate('All Coupons') }}</a>
                </li>
            </ul>
        </li>
    @endif

    @if (auth()->user()->can('Manage Payments'))
        <li class="{{ Request::routeIs(['plugin.saas.payments.methods']) ? 'active sub-menu-opened' : '' }}">
            <a href="#">
                <i class="icofont-money"></i>
                <span class="link-title">{{ translate('Payments') }}</span>
            </a>
            <ul class="nav sub-menu">
                <li class="{{ Request::routeIs(['plugin.saas.payments.methods']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.payments.methods') }}">{{ translate('Payment Methods') }}</a>
                </li>
            </ul>
        </li>
    @endif

    @if (auth()->user()->can('Manage SAAS Settings'))
        <li
            class="{{ Request::routeIs(['plugin.saas.edit.currency', 'plugin.saas.add.currency', 'plugin.saas.all.currencies', 'plugin.saas.general.settings', 'plugin.saas.notification.settings']) ? 'active sub-menu-opened' : '' }}">
            <a href="#">
                <i class="icofont-settings"></i>
                <span class="link-title">{{ translate('SAAS Settings') }}</span>
            </a>
            <ul class="nav sub-menu">
                <li
                    class="{{ Request::routeIs(['plugin.saas.edit.currency', 'plugin.saas.add.currency', 'plugin.saas.all.currencies']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.all.currencies') }}">{{ translate('Currencies') }}</a>
                </li>
                <li class="{{ Request::routeIs(['plugin.saas.general.settings']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.general.settings') }}">{{ translate('General Settings') }}</a>
                </li>
                <li class="{{ Request::routeIs(['plugin.saas.notification.settings']) ? 'active ' : '' }}">
                    <a
                        href="{{ route('plugin.saas.notification.settings') }}">{{ translate('Notification Settings') }}</a>
                </li>
            </ul>
        </li>
    @endif

    @if (auth()->user()->can('Manage Subscriptions'))
        <li
            class="{{ Request::routeIs(['plugin.saas.subscriber.edit', 'plugin.saas.store.details', 'plugin.saas.subscriber.details', 'plugin.saas.all.stores', 'plugin.saas.admin.payment.history', 'plugin.saas.customers.list', 'plugin.saas.admin.custom.domain.request']) ? 'active sub-menu-opened' : '' }}">
            <a href="#">
                <i class="icofont-users-alt-1"></i>
                <span class="link-title">{{ translate('Subscriptions') }}</span>
            </a>
            <ul class="nav sub-menu">
                <li class="{{ Request::routeIs(['plugin.saas.customers.list']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.customers.list') }}">{{ translate('All Subscribers') }}</a>
                </li>
                <li class="{{ Request::routeIs(['plugin.saas.all.stores']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.saas.all.stores') }}">{{ translate('All Stores') }}</a>
                </li>
                <li class="{{ Request::routeIs(['plugin.saas.admin.payment.history']) ? 'active ' : '' }}">
                    <a
                        href="{{ route('plugin.saas.admin.payment.history') }}">{{ translate('Payment Histories') }}</a>
                </li>
                <li class="{{ Request::routeIs(['plugin.saas.admin.custom.domain.request']) ? 'active ' : '' }}">
                    <a
                        href="{{ route('plugin.saas.admin.custom.domain.request') }}">{{ translate('Custom Domains') }}</a>
                </li>
            </ul>
        </li>
    @endif
@else
    @include('plugin/saas::user.panel.layouts.navbar')
@endif
