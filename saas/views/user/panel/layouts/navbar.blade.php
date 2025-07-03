<!-- Dashboard -->
<li class="{{ Request::routeIs('plugin.saas.user.dashboard') ? 'active ' : '' }}">
    <a href="{{ route('plugin.saas.user.dashboard') }}">
        <i class="icofont-dashboard"></i>
        <span class="link-title">{{ translate('Dashboard') }}</span>
    </a>
</li>
<!-- Dashboard -->

<!-- All Stores -->
<li class="{{ Request::routeIs('plugin.saas.user.stores') ? 'active ' : '' }}">
    <a href="{{ route('plugin.saas.user.stores') }}">
        <i class="icofont-database"></i>
        <span class="link-title">{{ translate('All Stores') }}</span>
    </a>
</li>
<!-- All Stores -->

<!-- Redeem Coupon -->
<li class="{{ Request::routeIs('plugin.saas.redeem.coupon') ? 'active ' : '' }}">
    <a href="{{ route('plugin.saas.redeem.coupon') }}">
        <i class="icofont-gift"></i>
        <span class="link-title">{{ translate('Redeem Coupons') }}</span>
    </a>
</li>
<!-- Redeem Coupon -->

<!-- Payment Histories -->
<li class="{{ Request::routeIs('plugin.saas.payment.history') ? 'active ' : '' }}">
    <a href="{{ route('plugin.saas.payment.history') }}">
        <i class="icofont-pay"></i>
        <span class="link-title">{{ translate('Payment History') }}</span>
    </a>
</li>
<!-- Payment Histories -->

<!-- Custom Domain -->
<li class="{{ Request::routeIs('plugin.saas.custom.domain') ? 'active ' : '' }}">
    <a href="{{ route('plugin.saas.custom.domain') }}">
        <i class="icofont-ui-home"></i>
        <span class="link-title">{{ translate('Custom Domains') }}</span>
    </a>
</li>
<!-- Custom Domain -->
