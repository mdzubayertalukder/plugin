{{-- Dropshipping Plugin Navigation --}}
@php
// Custom check for dropshipping plugin that works for both super admin and tenant
// For super admin: Check plugin activation directly (bypass type restriction)
// For tenant: Use standard plugin check
$dropshippingActive = false;

if (!isTenant()) {
// Super Admin: Check dropshipping plugin activation directly
try {
$dropshippingPlugin = \Core\Models\Plugin::where('location', 'dropshipping')
->where('is_activated', config('settings.general_status.active'))
->first();
$dropshippingActive = ($dropshippingPlugin !== null);
} catch (\Exception $e) {
$dropshippingActive = false;
}
} else {
// Tenant: Use standard plugin check which respects package assignments
$dropshippingActive = isActivePluging('dropshipping', true);
}
@endphp

@if($dropshippingActive)

{{-- Super Admin Navigation (Main Admin Panel) --}}
@if(!isTenant())
<li class="{{ Request::routeIs(['admin.dropshipping.*']) ? 'active sub-menu-opened' : '' }}">
    <a href="#">
        <i class="icofont-truck"></i>
        <span class="link-title">{{ translate('Dropshipping') }}</span>
    </a>
    <ul class="nav sub-menu">
        <li class="{{ Request::routeIs(['admin.dropshipping.dashboard']) ? 'active' : '' }}">
            <a href="{{ route('admin.dropshipping.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="{{ Request::routeIs(['admin.dropshipping.woocommerce.*']) ? 'active' : '' }}">
            <a href="{{ route('admin.dropshipping.woocommerce.index') }}">{{ translate('WooCommerce Stores') }}</a>
        </li>
        <li class="{{ Request::routeIs(['admin.dropshipping.plan-limits.*']) ? 'active' : '' }}">
            <a href="{{ route('admin.dropshipping.plan-limits.index') }}">{{ translate('Plan Limits') }}</a>
        </li>
        <li class="{{ Request::routeIs(['admin.dropshipping.reports.*']) ? 'active' : '' }}">
            <a href="#">{{ translate('Reports') }}</a>
            <ul class="nav sub-menu">
                <li class="{{ Request::routeIs(['admin.dropshipping.reports.imports']) ? 'active' : '' }}">
                    <a href="{{ route('admin.dropshipping.reports.imports') }}">{{ translate('Import Reports') }}</a>
                </li>
                <li class="{{ Request::routeIs(['admin.dropshipping.reports.usage']) ? 'active' : '' }}">
                    <a href="{{ route('admin.dropshipping.reports.usage') }}">{{ translate('Usage Reports') }}</a>
                </li>
            </ul>
        </li>
        <li class="{{ Request::routeIs(['admin.dropshipping.settings.*']) ? 'active' : '' }}">
            <a href="{{ route('admin.dropshipping.settings.index') }}">{{ translate('Settings') }}</a>
        </li>
    </ul>
</li>
@endif

{{-- Tenant Navigation (Tenant Store Dashboard) --}}
@if(isTenant())
<li class="{{ Request::routeIs(['dropshipping.*', 'user.dropshipping.*']) ? 'active sub-menu-opened' : '' }}">
    <a href="#">
        <i class="icofont-truck"></i>
        <span class="link-title">{{ translate('Dropshipping') }}</span>
    </a>
    <ul class="nav sub-menu">
        <li class="{{ Request::routeIs(['dropshipping.my.products']) ? 'active' : '' }}">
            <a href="{{ route('dropshipping.my.products') }}">{{ translate('My Products') }}</a>
        </li>
        <li class="{{ Request::routeIs(['dropshipping.products.all', 'dropshipping.products', 'user.dropshipping.products']) ? 'active' : '' }}">
            <a href="{{ route('dropshipping.products.all') }}">{{ translate('Browse Products') }}</a>
        </li>
        <li class="{{ Request::routeIs(['dropshipping.import.history', 'user.dropshipping.history']) ? 'active' : '' }}">
            <a href="{{ route('dropshipping.import.history') }}">{{ translate('Import History') }}</a>
        </li>
    </ul>
</li>
@endif

@endif