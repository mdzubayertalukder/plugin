@php
    $categories = getAllTicketCategories();
    $staffs = getAllUsers();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Support Tickets') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <!-- All Tickets-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('All Tickets') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center mb-3">
                    <!--Filter area-->
                    <form method="get" action="{{ route('saas.support.tickets') }}">
                        <select class="theme-input-style mb-2 col-md-3" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="5" @selected(request()->has('per_page') && request()->get('per_page') == '5')>5</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="ticket_category">
                            <option value="-1">{{ translate('All Categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request()->has('ticket_category') && request()->get('ticket_category') == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select class="theme-input-style mb-2" name="user" id="user">
                            <option value="">{{ translate('Select User') }}</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}" @selected(request()->has('user') && request()->get('user') == $staff->id)>{{ $staff->name }}
                                </option>
                            @endforeach
                        </select>

                        <select class="theme-input-style mb-2" name="ticket_priority">
                            <option value="-1" @selected(request()->has('ticket_priority') && request()->get('ticket_priority') == '-1')>{{ translate('All Priorities') }}</option>
                            <option value="high" @selected(request()->has('ticket_priority') && request()->get('ticket_priority') == 'high')>{{ translate('High') }}</option>
                            <option value="urgent" @selected(request()->has('ticket_priority') && request()->get('ticket_priority') == 'urgent')>{{ translate('Urgent') }}</option>
                            <option value="medium" @selected(request()->has('ticket_priority') && request()->get('ticket_priority') == 'medium')>{{ translate('Medium') }}</option>
                            <option value="low" @selected(request()->has('ticket_priority') && request()->get('ticket_priority') == 'low')>{{ translate('Low') }}</option>
                        </select>
                        <input type="text" name="text_search" class="theme-input-style  mb-2"
                            value="{{ request()->has('text_search') ? request()->get('text_search') : '' }}"
                            placeholder="Enter Text">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>

                    @if (request()->has('ticket_category') ||
                            request()->has('user') ||
                            request()->has('ticket_priority') ||
                            request()->has('text_search'))
                        <a class="btn long btn-danger" href="{{ route('saas.support.tickets') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->
                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="ticket_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Short Details') }}</th>
                                <th>{{ translate('Opened Time') }}</th>
                                <th>{{ translate('Assigned') }}</th>
                                <th>{{ translate('Last Replay') }}</th>
                                <th>{{ translate('Last Relpay Time') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>{{ $key }}.</td>
                                    <td>
                                        <div>
                                            <span>
                                                <b>{{ translate('Subject : ') }}</b>
                                                <a
                                                    href="{{ route('support.ticket.details', $ticket->id) }}">{{ $ticket->subject }}</a>
                                            </span><br>
                                            <span>
                                                <b>{{ translate('Ticket User: ') }}</b>
                                                {{ $ticket->createdBy != null ? $ticket->createdBy->name : '-' }}

                                            </span><br>
                                            <span class="fz-14">
                                                <b>{{ translate('Type : ') }}</b>
                                                <span
                                                    class="{{ $ticket->priority_class() }}">{{ ucwords($ticket->priority) }}</span>
                                            </span><br>
                                            <span>
                                                <b>{{ translate('Category : ') }}</b>
                                                {{ $ticket->categoryDetails->name }}
                                            </span><br>
                                            <span>
                                                <b>{{ translate('Status : ') }}</b>
                                                <span class="badge badge-info">
                                                    {{ $ticket->status_name() }}
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                    <td>
                                        {{ !empty($ticket->assigned_to) ? $ticket->assignedTo->name : '' }}
                                    </td>
                                    <td>
                                        @if (!empty($ticket->lastReply()))
                                            {{ $ticket->lastReply()->repliedBy->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($ticket->lastReply()))
                                            {{ $ticket->lastReply()->replied_at->diffForHumans() }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right w-20">
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                                                <div class="menu-icon style--two mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a
                                                    href="{{ route('edit.support.tickets', $ticket->id) }}">{{ translate('Edit') }}</a>
                                                <a
                                                    href="{{ route('support.ticket.details', $ticket->id) }}">{{ translate('Details') }}</a>
                                                <a href="#"
                                                    onclick="deleteConfirmation('{{ $ticket->id }}')">{{ translate('Delete') }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $tickets->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- All Tickets-->

        <!--ticket Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">
                            {{ translate('Are you sure to delete this ticket ? Once you delete, all data related to this will be deleted') }}.
                        </p>
                        <form method="POST" action="{{ route('admin.support.ticket.delete') }}" id="ticket-delete-form">
                            @csrf
                            <input type="hidden" id="ticket_id_to_delete" name="ticket_id">
                            <button type="button" class="btn long mt-2  btn-danger"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2" id="ticket-delete-btn">
                                {{ translate('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--ticket Delete Modal End-->
    </div>
@endsection
@section('custom_scripts')
    <script>
        /**
         * show ticket delete confirmation modal
         */
        function deleteConfirmation(id) {
            "use strict";
            $("#ticket_id_to_delete").val(id);
            $('#delete-modal').modal('show');
        }
    </script>
@endsection
