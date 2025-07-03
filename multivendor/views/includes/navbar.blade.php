<!--Seller Module-->
@canany([
    'Manage Sellers',
    'Manage Payouts',
    'Manage Payouts Requests',
    'Manage Earning History',
    'Manage Seller
    Settings',
    ])
    <li
        class="{{ Request::routeIs(['plugin.multivendor.admin.seller.earning.list', 'plugin.multivendor.admin.seller.settings', 'plugin.multivendor.admin.seller.details', 'plugin.multivendor.admin.seller.payouts.list', 'plugin.multivendor.admin.seller.payout.requests.list', 'plugin.multivendor.admin.seller.list']) ? 'active sub-menu-opened' : '' }}">
        <a href="#">
            <i class="icofont-users-alt-2"></i>
            <span class="link-title">{{ translate('Sellers') }}</span>
        </a>
        <ul class="nav sub-menu">
            @can('Manage Sellers')
                <li class="{{ Request::routeIs(['plugin.multivendor.admin.seller.list']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.multivendor.admin.seller.list') }}">{{ translate('Sellers') }}</a>
                </li>
            @endcan
            @can('Manage Payouts')
                <li class="{{ Request::routeIs(['plugin.multivendor.admin.seller.payouts.list']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.multivendor.admin.seller.payouts.list') }}">{{ translate('Payouts') }}</a>
                </li>
            @endcan
            @can('Manage Payouts Requests')
                <li class="{{ Request::routeIs(['plugin.multivendor.admin.seller.payout.requests.list']) ? 'active ' : '' }}">
                    <a
                        href="{{ route('plugin.multivendor.admin.seller.payout.requests.list') }}">{{ translate('Payouts Requests') }}</a>
                </li>
            @endcan
            @can('Manage Earning History')
                <li class="{{ Request::routeIs(['plugin.multivendor.admin.seller.earning.list']) ? 'active ' : '' }}">
                    <a
                        href="{{ route('plugin.multivendor.admin.seller.earning.list') }}">{{ translate('Earning History') }}</a>
                </li>
            @endcan
            @can('Manage Seller Settings')
                <li class="{{ Request::routeIs(['plugin.multivendor.admin.seller.settings']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.multivendor.admin.seller.settings') }}">{{ translate('Settings') }}</a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany
<!--End Seller Module-->
