@php
    $user_type = Auth::user()->user_type;
    $route_extension = $user_type == config('saas.user_type.subscriber') ? 'subscriber.' : '';
@endphp

<!--Refunds Module-->
@if (auth()->user()->can('Manage Support Ticket') || auth()->user()->user_type == config('saas.user_type.subscriber'))
    <li
        class="{{ Request::routeIs(['support.ticket.categories', 'create.' . $route_extension . 'support.ticket', 'saas.' . $route_extension . 'support.tickets']) ? 'active sub-menu-opened' : '' }}">
        <a href="#">
            <i class="icofont-support"></i>
            <span class="link-title">{{ translate('Support Ticket') }}</span>
        </a>
        <ul class="nav sub-menu">
            <li class="{{ Request::routeIs(['create.' . $route_extension . 'support.ticket']) ? 'active ' : '' }}">
                <a href="{{ route('create.' . $route_extension . 'support.ticket') }}">{{ translate('Create Ticket') }}</a>
            </li>
            <li class="{{ Request::routeIs(['saas.' . $route_extension . 'support.tickets']) ? 'active ' : '' }}">
                <a href="{{ route('saas.' . $route_extension . 'support.tickets') }}">{{ translate('All Tickets') }}</a>
            </li>
            @if ($user_type != config('saas.user_type.subscriber'))
                <li class="{{ Request::routeIs(['support.ticket.categories']) ? 'active ' : '' }}">
                    <a href="{{ route('support.ticket.categories') }}">{{ translate('Categories') }}</a>
                </li>
            @endif
        </ul>
    </li>
    <!--End Refunds Module-->
@endif
