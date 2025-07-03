<!--Wallet Module-->
@if (auth()->user()->can('Manage Offline Payment Methods') || auth()->user()->can('Manage Wallet Transactions'))
    <li
        class="{{ Request::routeIs(['plugin.wallet.recharge.offline.payment.methods', 'plugin.wallet.configuration', 'plugin.wallet.transaction.list']) ? 'active sub-menu-opened' : '' }}">
        <a href="#">
            <i class="icofont-wallet"></i>
            <span class="link-title">{{ translate('Wallet') }}</span>
        </a>
        @canany(['Manage Wallet Transactions', 'Manage Offline Payment Methods'])
            <ul class="nav sub-menu">
                @can('Manage Wallet Transactions')
                    <li class="{{ Request::routeIs(['plugin.wallet.transaction.list']) ? 'active ' : '' }}">
                        <a href="{{ route('plugin.wallet.transaction.list') }}">{{ translate('Wallet Transactions') }}</a>
                    </li>
                @endcan
                @can('Manage Offline Payment Methods')
                    <li class="{{ Request::routeIs(['plugin.wallet.recharge.offline.payment.methods']) ? 'active ' : '' }}">
                        <a
                            href="{{ route('plugin.wallet.recharge.offline.payment.methods') }}">{{ translate('Offline Payment Methods') }}</a>
                    </li>
                @endcan
            </ul>
        @endcanany
    </li>
@endif
<!--End Wallet Module-->
