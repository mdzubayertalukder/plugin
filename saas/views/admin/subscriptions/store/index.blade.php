@php
    $package_repository = new \Plugin\Saas\Repositories\PackageRepository();
    $packages = $package_repository->getAllActivePackages();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Stores') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <!-- Store List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('All Stores') }}</h4>
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center mb-3">
                    <!--Filter area-->
                    <form method="get" action="{{ route('plugin.saas.all.stores') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="5" @selected(request()->has('per_page') && request()->get('per_page') == '5')>5</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="subscription_status">
                            <option value="all" @selected(request()->has('subscription_status') && request()->get('subscription_status') == 'all')>{{ translate('Subscription status') }}
                            </option>
                            <option value="0" @selected(request()->has('subscription_status') && request()->get('subscription_status') == 0)>
                                {{ translate('Pending') }}
                            </option>
                            <option value="1" @selected(request()->has('subscription_status') && request()->get('subscription_status') == 1)>
                                {{ translate('Active') }}
                            </option>
                        </select>
                        <input type="text" class="theme-input-style mb-2" id="store_creation_date"
                            placeholder="Store creation date" name="store_creation_date" readonly>
                        <input type="text" name="store_name" class="theme-input-style  mb-2"
                            value="{{ request()->has('store_name') ? request()->get('store_name') : '' }}"
                            placeholder="Enter store name">
                        <input type="text" name="subscriber_name" class="theme-input-style  mb-2"
                            value="{{ request()->has('subscriber_name') ? request()->get('subscriber_name') : '' }}"
                            placeholder="Enter subscriber name">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>

                    @if (request()->has('store_creation_date') || request()->has('subscription_status') || request()->has('store_name'))
                        <a class="btn long btn-danger" href="{{ route('plugin.saas.all.stores') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="store_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Subscriber') }}</th>
                                <th>{{ translate('Store Info') }}</th>
                                <th>{{ translate('Subcription') }}</th>
                                <th>{{ translate('Validity') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th class="text-center">{{ translate('Update') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                                $jobStatus = jobExistsInQueue();
                            @endphp
                            @forelse ($stores as $store)
                                @php
                                    $class = '';
                                    if ($store->is_notifiable() == 0) {
                                        $class = 'table-danger';
                                    }
                                    if ($store->is_notifiable() == 1) {
                                        $class = 'table-warning';
                                    }
                                    $valid_till = $store->valid_till;

                                    $currentDate = (new DateTime())->format('Y-m-d'); // creates a DateTime object representing the current date and time
                                    $expired_date = (new DateTime($valid_till))->format('Y-m-d'); // creates a DateTime object from the date string

                                    $disable_status =
                                        $store->is_db_created == 0 ||
                                        $store->is_db_updated == 0 ||
                                        $store->is_plugin_db_updated == 0 ||
                                        $store->is_system_db_updated == 0 ||
                                        $expired_date < $currentDate;
                                    $update_plan =
                                        $store->is_db_created == 1 &&
                                        $store->is_db_updated == 1 &&
                                        $store->is_plugin_db_updated == 1 &&
                                        $store->is_system_db_updated == 1;

                                    $is_loading = $jobStatus > 0;

                                @endphp
                                <tr class="{{ $class }}">
                                    <td>{{ $key }}.</td>
                                    <td>
                                        <p class="mb-0 bold">{{ $store->user->name }}</p>
                                        <p class="mb-0 font-weight-light"><i
                                                class="icofont-email"></i>{{ $store->user->email }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 bold">{{ $store->store_name }}</p>
                                        @if ($store->domain != null)
                                            <a href="https://{{ $store->domain->domain }}" class="font-weight-light"
                                                target="_blank">
                                                <i class="icofont-link"></i> https://{{ $store->domain->domain }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $store->package != null ? $store->package->translation('name') : '' }}
                                        @if ($store->plan != null)
                                            - {{ $store->plan->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($store->is_expired())
                                            @if ($store->is_trial() == 1)
                                                <p class="badge badge-danger mb-0">{{ translate('Trail Expried') }}</p>
                                            @else
                                                <p class="badge badge-danger">{{ translate('Expried') }}</p>
                                            @endif
                                        @else
                                            @if ($store->valid_till == null || $store->plan_id == config('saas.plans.lifetime'))
                                                {{ translate('Lifetime') }}
                                            @else
                                                @if ($store->is_trial() == 1)
                                                    {{ translate('Trial Ends At') }} {{ $store->valid_till }}
                                                @else
                                                    {{ $store->valid_till }}
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="status" id="change-status-{{ $store->id }}"
                                                class="change-status" data-storeid="{{ $store->id }}"
                                                data-tenant="{{ $store->domain == null ? 0 : 1 }}"
                                                data-database="{{ $store->database }}"
                                                data-isdbcreated="{{ $store->is_db_created }}" @checked($store->status == 1)
                                                @disabled($disable_status)>
                                            <span class="control"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        @if ($store->is_db_created == 0)
                                            <button class="btn btn-success sm change-status"
                                                data-storeid="{{ $store->id }}"
                                                data-tenant="{{ $store->domain == null ? 0 : 1 }}"
                                                data-database="{{ $store->tenant != null ? $store->tenant->tenancy_db_name : '' }}"
                                                data-isdbcreated="{{ $store->is_db_created }}"
                                                {{ $is_loading ? 'disabled' : '' }}
                                                @if ($is_loading) data-toggle="tooltip" data-placement="top" title="{{ translate('Please wait fiew minites and reload the page') }}" @endif>
                                                {{ translate('Create Tenant Databse') }}
                                            </button>
                                        @elseif($store->is_db_updated == 0)
                                            <button class="btn btn-success sm update-database"
                                                data-storeid="{{ $store->id }}" {{ $is_loading ? 'disabled' : '' }}
                                                @if ($is_loading) data-toggle="tooltip" data-placement="top" title="{{ translate('Please wait fiew minites and reload the page') }}" @endif>
                                                {{ translate('Update Databse') }}
                                            </button>
                                        @elseif($store->is_system_db_updated == 0)
                                            <button class="btn btn-success sm update-system"
                                                data-storeid="{{ $store->id }}" {{ $is_loading ? 'disabled' : '' }}
                                                @if ($is_loading) data-toggle="tooltip" data-placement="top" title="{{ translate('Please wait fiew minites and reload the page') }}" @endif>
                                                {{ translate('Update System') }}
                                            </button>
                                        @elseif($store->is_plugin_db_updated == 0)
                                            <button class="btn btn-success sm update-plugin"
                                                data-storeid="{{ $store->id }}" {{ $is_loading ? 'disabled' : '' }}
                                                @if ($is_loading) data-toggle="tooltip" data-placement="top" title="{{ translate('Please wait fiew minites and reload the page') }}" @endif>
                                                {{ translate('Update Plugin') }}
                                            </button>
                                        @else
                                            <i class="icofont-ban" title="No Action"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                                                <div class="menu-icon style--two mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="{{ route('plugin.saas.store.details', $store->id) }}">
                                                    {{ translate('Store Details') }}
                                                </a>
                                                @if ($store->domain != null)
                                                    <a href="https://{{ $store->domain->domain }}/admin">
                                                        {{ translate('Store Admin Panel') }}
                                                    </a>
                                                    <a href="https://{{ $store->domain->domain }}">
                                                        {{ translate('Store Frontend') }}
                                                    </a>
                                                @endif
                                                @if ($update_plan)
                                                    <a href="#" class="update-store-plan"
                                                        data-storeid="{{ $store->id }}"
                                                        data-subscriber="{{ $store->user->name }}"
                                                        data-store="{{ $store->store_name }}"
                                                        data-customerid="{{ $store->user_id }}">
                                                        {{ translate('Update Plan') }}
                                                    </a>
                                                @endif
                                                <a href="#" onclick="deleteConfirmation('{{ $store->id }}')">
                                                    {{ translate('Delete Store') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @empty
                                <tr>
                                    <td class="text-center" colspan="8">
                                        <p class="mb-0 alert">
                                            No data found in this table
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $stores->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>

            </div>
        </div>
        <!-- Store List-->

        <!-- Create Tenant Database Modal-->
        <div class="modal fade" id="create_tenant_database" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('plugin.saas.tenant.database.create') }}" method="post"
                        id="create-tenant-database-form">
                        @csrf
                        <input type="hidden" name="store_id" id="store_id" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ translate('Create Tenant Database') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>{{ translate('Before clicking Connect Database, please create a database manually with following credentials - ') }}
                            </h6>
                            <table class="table-bordered mt-3">
                                <tr>
                                    <td>
                                        Database Name
                                    </td>
                                    <td>
                                        <p id="database"></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Host
                                    </td>
                                    <td>
                                        {{ env('DB_HOST') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Database User Name
                                    </td>
                                    <td>
                                        {{ env('DB_USERNAME') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Database User Password
                                    </td>
                                    <td>
                                        {{ env('DB_PASSWORD') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Database Port
                                    </td>
                                    <td>
                                        {{ env('DB_PORT') }}
                                    </td>
                                </tr>

                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary long" data-dismiss="modal"
                                id="create-database">
                                {{ translate('Close') }}
                            </button>
                            <button type="submit" class="btn long">
                                <span class="create-database-btn-label">{{ translate('Connect Database') }}</span>
                                <div class="spinner">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End Create Teant Database Modal-->

        <!-- Update Plan-->
        <div class="modal fade" id="updatePlan" tabindex="-1" role="dialog" aria-labelledby="updatePlanLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createStoreLabel">{{ translate('Update Store Plan') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="#" method="post" id="update-plan-form">
                        @csrf
                        <input type="hidden" name="subscriber_id" id="subscriber_id">
                        <input type="hidden" name="store_id" id="update_store_id">
                        <div class="modal-body">
                            <div class="form-group mb-20">
                                <label class="mb-2 font-14 bold black">{{ translate('Store') }}</label>
                                <input type="text" name="store_name" class="theme-input-style store_name" readonly>
                            </div>
                            <div class="form-group mb-20">
                                <label class="mb-2 font-14 bold black">{{ translate('Subscriber') }}</label>
                                <input type="text" name="subscriber_name" class="theme-input-style subscriber_name"
                                    readonly>
                            </div>
                            <div class="form-group mb-20">
                                <label for="package" class="mb-2 font-14 bold black">{{ translate('Package') }}<span
                                        class="text-danger"> *
                                    </span></label>
                                <select class="theme-input-style package-selector" name="package_id" id="package"
                                    onchange="getAllPlansOfPackage()">
                                    <option>{{ translate('Select Package') }}</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}">
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-20">
                                <label for="plan" class="mb-2 font-14 bold black">{{ translate('Plan') }}
                                    <span class="text-danger"> *</span>
                                </label>
                                <select class="theme-input-style" name="plan_id" id="plans">
                                    <option value=""> {{ translate('Select Plan') }} </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary long"
                                data-dismiss="modal">{{ translate('Close') }}
                            </button>
                            <button type="submit" class="btn long" id="update-plan">
                                <span class="update-plan-btn-label">{{ translate('Update Plan') }}</span>
                                <div class="spinner">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Update Plan-->

        <!--Store Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">
                            {{ translate('Are you sure to delete this store ? Once you delete, all data related to this will be deleted') }}.
                        </p>
                        <form method="POST" action="{{ route('plugin.saas.store.delete') }}" id="delete-store">
                            @csrf
                            <input type="hidden" id="store_id_to_delete" name="store_id">
                            <button type="button" class="btn long mt-2 btn-danger"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2" id="delete-store">
                                <span class="store-delete-btn-label">{{ translate('Delete') }}</span>
                                <div class="spinner">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Store Delete Modal End-->
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/public/backend/assets/plugins/daterangepicker/daterangepicker.js') }}">
    </script>
    <script>
        (function($) {
            "use strict";

            //Delete a store
            $('#delete-store').submit(function(event) {
                event.preventDefault()
                $('.store-delete-btn-label').html('');
                $('.spinner').addClass('lds-ellipsis');
                this.submit();
            });

            //Connect tenant with database
            $('#create-tenant-database-form').submit(function(event) {
                event.preventDefault()
                $('.create-database-btn-label').html('');
                $('.spinner').addClass('lds-ellipsis');
                this.submit();
            });

            //Will load plan update modal
            $('.update-store-plan').on('click', function(e) {
                e.preventDefault();
                let subscriber_id = $(this).data('customerid')
                let store_id = $(this).data('storeid');
                let subscriber = $(this).data('subscriber');
                let store = $(this).data('store');

                $('#subscriber_id').val(subscriber_id);
                $('#update_store_id').val(store_id);
                $('.subscriber_name').val(subscriber)
                $('.store_name').val(store)

                $('#updatePlan').modal('show');
            });

            //Will update store plan
            $('#update-plan-form').submit(function(event) {
                event.preventDefault();
                $('.update-plan-btn-label').html('');
                $('.spinner').addClass('lds-ellipsis');
                $('p.text-danger').remove();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('plugin.saas.store.plan.update') }}',
                    data: $("#update-plan-form").serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ translate('Plan Updated Successfully') }}');
                            location.reload()
                        } else {
                            toastr.error(response.message);
                            $(".update-plan-btn-label").html('Update Plan');
                            $('.spinner').removeClass("lds-ellipsis");
                        }
                    },
                    error: function(xhr, status, error) {
                        $(".update-plan-btn-label").html('Update Plan');
                        $('.spinner').removeClass("lds-ellipsis");
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key +
                                    '"], select[name="' + key + '"]');
                                var message = '<p class="text-danger mb-0">' + value[0] +
                                    '</p>';
                                inputField.after(message);
                            });
                        } else {
                            toastr.error("{{ translate('Plan Update Failed') }}");
                        }
                    }

                });

            });

            //Change store  status 
            $('.change-status').on('click', function(e) {
                let $this = $(this);
                let id = $this.data('storeid');
                let tenant = $this.data('tenant')
                let database = $this.data('database')
                let isdbcreated = $this.data('isdbcreated')

                if (isdbcreated == 0) {
                    $('#store_id').val(id);
                    $('#database').html(database);
                    $('#create_tenant_database').modal('show');
                }
                if (isdbcreated == 1) {
                    $.post('{{ route('plugin.saas.update.store.status') }}', {
                        _token: '{{ csrf_token() }}',
                        id: id
                    }, function(data) {
                        if (data.success) {
                            toastr.success(
                                '{{ translate('Store status updated successfully') }}',
                                "Success");
                        } else {
                            toastr.error('{{ translate('Store status update failed') }}',
                                "Error!");
                        }
                    })
                }
            });

            /**
             * 
             * update database 
             * 
             * */
            $('.update-database').on('click', function(e) {
                let $this = $(this);
                let id = $this.data('storeid');

                $.post('{{ route('plugin.saas.update.tenant') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        $this.prop('disabled', true)
                        toastr.success(
                            '{{ translate('Store database updated successfully') }}',
                            "Success");
                        location.reload()
                    } else {
                        toastr.error('{{ translate('Store database update failed') }}',
                            "Error!");
                    }
                })
            });

            /**
             * Update plugin
             */
            $('.update-plugin').on('click', function(e) {
                let $this = $(this);
                let id = $this.data('storeid');

                $.post('{{ route('plugin.saas.update.tenant.plugin') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        $this.prop('disabled', true)
                        toastr.success(
                            '{{ translate('Store database updated successfully') }}',
                            "Success");
                        location.reload()
                    } else {
                        toastr.error('{{ translate('Store database update failed') }}',
                            "Error!");
                    }
                })
            });

            /**
             * Update System
             */
            $('.update-system').on('click', function(e) {
                let $this = $(this);
                let id = $this.data('storeid');

                $.post('{{ route('plugin.saas.update.tenant.system') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        $this.prop('disabled', true)
                        toastr.success(
                            '{{ translate('Store database updated successfully') }}',
                            "Success");
                        location.reload()
                    } else {
                        toastr.error('{{ translate('Store database update failed') }}',
                            "Error!");
                    }
                })
            });

            //Will get plan options 
            $(".package-selector").on('change', function(e) {
                'use strict';
                $("#register").prop("disabled", true);
                let selected_package = $('#package').val();
                $('#selected_package').val(selected_package)
                $.ajax({
                    url: '{{ route('plugin.saas.get.plans.according.to.package') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        package_id: selected_package
                    },
                    success: function(response) {
                        $("#register").prop("disabled", false);
                        if (response.success) {
                            let is_redeem_coupon = $('#is_redeem_coupon').val()
                            let lifetime_plan = '{{ config('saas.plans.lifetime') }}'

                            let plans = response.plans

                            let html = ``

                            for (let i = 0; i < plans.length; i++) {
                                html = html + `<option value='` + plans[i]['id'] + `'>` + plans[i][
                                        'name'
                                    ] +
                                    `</option>`
                            }
                            $('#plans').html(html)

                            if (is_redeem_coupon == '1') {
                                $('#plans').html(`<option value="` + lifetime_plan +
                                    `" selected> {{ translate('Lifetime Plan') }} </option>`)
                                $('#plans').prop('disabled', true);
                            } else {
                                if (html == '') {
                                    $('#plans').html(
                                        `<option value="-1"> {{ translate('Select Plan') }} </option>`
                                    )
                                }
                            }
                        } else {
                            toastr.error("{{ translate('No plan found with this package !') }}");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $("#register").prop("disabled", false);
                        toastr.error("{{ translate('No plan found with this package !') }}");
                    }
                });
            });
            // Filter date range
            function cb(start, end) {

                let initVal =
                    '{{ request()->has('store_creation_date') ? request()->get('store_creation_date') : '' }}';
                $('#store_creation_date').val(initVal);
            }

            var start = moment().subtract(0, 'days');
            var end = moment();

            $('#store_creation_date').on('apply.daterangepicker', function(ev, picker) {
                let val = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD')
                $('#store_creation_date').val(val);
            });

            $('#store_creation_date').daterangepicker({
                startDate: start,
                endDate: end,
                showCustomRangeLabel: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD', // Adjust the format as needed
                }
            }, cb);

            cb(start, end);
        })(jQuery);

        /**
         * show store delete confirmation modal
         */
        function deleteConfirmation(id) {
            "use strict";
            $("#store_id_to_delete").val(id);
            $('#delete-modal').modal('show');
        }
    </script>
@endsection
