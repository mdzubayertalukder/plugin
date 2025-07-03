@extends('core::base.layouts.master')
@section('title')
    {{ translate('Custom Notifications') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Custom Notifications') }}</h4>
                        <a href="{{ route('plugin.tlcommercecore.marketing.custom.notification.create.new') }}"
                            class="btn long">{{ translate('Compose') }}</a>
                    </div>

                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Bulk actions-->
                    <select class="theme-input-style" id="bulkActionSelector">
                        <option value="">
                            {{ translate('Bulk Action') }}
                        </option>
                        <option value="all-delete">
                            {{ translate('Delete Selected') }}
                        </option>
                    </select>
                    <button class="btn long btn-danger fire-bulk-action"
                        href="{{ route('plugin.tlcommercecore.orders.inhouse') }}" type="submit">{{ translate('Apply') }}
                    </button>
                    <!--End bulk actions-->
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('To') }}</th>
                                <th>{{ translate('Type') }}</th>
                                <th>{{ translate('Sender') }}</th>
                                <th>{{ translate('Message') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($notifications->count() > 0)
                                @foreach ($notifications as $key => $notification)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="items[]" class="item-id"
                                                        value="{{ $notification->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($notification->to == config('tlecommercecore.custom_notification_receiver_type.all_customers'))
                                                {{ translate('All Customers') }}
                                            @endif
                                            @if ($notification->to == config('tlecommercecore.custom_notification_receiver_type.specific_customer'))
                                                {{ translate('Specific Customers') }}
                                            @endif
                                            @if ($notification->to == config('tlecommercecore.custom_notification_receiver_type.all_users'))
                                                {{ translate('All Users') }}
                                            @endif
                                            @if ($notification->to == config('tlecommercecore.custom_notification_receiver_type.specific_user'))
                                                {{ translate('Specific Users') }}
                                            @endif
                                            @if ($notification->to == config('tlecommercecore.custom_notification_receiver_type.specific_user_role'))
                                                {{ translate('Specific User Roles') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($notification->type == config('tlecommercecore.custom_notification_type.dashboard'))
                                                {{ translate('Dashbaord') }}
                                            @endif
                                            @if ($notification->type == config('tlecommercecore.custom_notification_type.email'))
                                                {{ translate('Email') }}
                                            @endif
                                            @if ($notification->type == config('tlecommercecore.custom_notification_type.email_dashboard'))
                                                {{ translate('Dashbaord & Email') }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $user = \Core\Models\User::where('id', $notification->sender)->first();
                                            @endphp
                                            @if ($user != null)
                                                {{ $user->name }}
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn sm"
                                                onclick="viewDetails({{ json_encode($notification->details) }})">{{ translate('View Message') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing Found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $notifications->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Details Modal-->
    <div id="details-modal" class="details-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6">{{ translate('Message') }}</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
    <!--Details Modal End-->
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Select all Items for bulk action
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            })
            /**
             * Bulk actions
             * 
             **/
            $('.fire-bulk-action').on('click', function(e) {
                let action = $("#bulkActionSelector").val();
                if (action != "") {
                    let selected_items = [];
                    $('input[name^="items"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    let data = {
                        'action': action,
                        'selected_items': selected_items
                    }
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.marketing.custom.notification.bulk.action') }}', {
                            _token: '{{ csrf_token() }}',
                            data: data
                        }, function(data) {
                            if (data.success) {
                                toastr.success(
                                    '{{ translate('Selected items deleted successfully') }}',
                                    "Success");
                                location.reload();

                            } else {
                                toastr.error('{{ translate('Action Failed') }}', "Error!");
                            }
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}', "Error!");
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}', "Error!");
                }

            });
        })(jQuery);
        /**
         *View details on notification
         * 
         **/
        function viewDetails(data) {
            "use strict";
            $('#details-modal').find('.modal-body').html(data);
            $('#details-modal').modal('show');
        }
    </script>
@endsection
