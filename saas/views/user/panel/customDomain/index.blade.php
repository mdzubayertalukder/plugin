@php
    $request = app('request');
    $current_domain = $request->getHost();
@endphp
@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ translate('Custom Domains') }}
@endsection
@section('custom_css')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/data-table/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- ======= END BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
@endsection
@section('main_content')
    <div class="alert alert-danger">
        {{ translate('*** Please be mindful that once your custom domain request is granted, any previously linked domains will become inactive.') }}
    </div>
    <div class="alert alert-info">
        {{ translate('*** Before requesting a custom domain, make sure to add the following CNAME records to your domain registrar account.') }}
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="pb-3">
                        <h4 class="font-20">{{ translate('CNAME Record') }}</h4>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">{{ translate('Name') }}</th>
                                <th scope="col">{{ translate('TTL') }}</th>
                                <th scope="col">{{ translate('Type') }}</th>
                                <th scope="col">{{ translate('Record') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>www</td>
                                <td>{{ translate('Default Value') }}</td>
                                <td>CNAME</td>
                                <td>{{ $current_domain }}</td>
                            </tr>
                            <tr>
                                <td>@</th>
                                <td>{{ translate('Default Value') }}</td>
                                <td>CNAME</td>
                                <td>{{ $current_domain }}</td>
                            </tr>
                            <tr>
                                <td>@</th>
                                <td>{{ translate('Default Value') }}</td>
                                <td>A</td>
                                <td>{{ gethostbyname($current_domain) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Custom Domains') }}</h4>
                        <button class="btn sm" data-toggle="modal" data-target="#customDomainModal">
                            {{ translate('Request Custom Domain') }}
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2" id="domain_request_history">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }} </th>
                                <th>{{ translate('Subdomain') }} </th>
                                <th>{{ translate('Custom Domain') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($domain_request as $domain)
                                @if ($domain->saasAccount->user_id == auth()->id())
                                    @php
                                        $requested_domain = $domain->requested_domain;
                                        $status = $domain->status;
                                        $approved_date = $domain->approved_date;
                                        $cancelled_date = $domain->cancelled_date;
                                    @endphp
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $domain->saasAccount->store_name }}</td>
                                        <td>{{ $domain->current_domain }}</td>
                                        <td class="p-0">
                                            <table>
                                                <tbody>
                                                    @for ($i = 0; $i < sizeof($requested_domain); $i++)
                                                        <tr>
                                                            <td class="border-0">{{ $i + 1 }}.
                                                                {{ $requested_domain[$i] }}</td>
                                                            <td class="border-0">
                                                                @if ($status[$i] == 0)
                                                                    <span class="badge badge-warning mb-2">
                                                                        {{ translate('Pending') }}
                                                                    </span>
                                                                @endif
                                                                @if ($status[$i] == 1)
                                                                    <span class="badge badge-success mb-2">
                                                                        {{ translate('Approved') }}
                                                                    </span>
                                                                @endif
                                                                @if ($status[$i] == 2)
                                                                    <span class="badge badge-danger mb-2">
                                                                        {{ translate('Cancelled') }}
                                                                    </span>
                                                                @endif
                                                            </td>

                                                            <td class="border-0">
                                                                @if ($status[$i] == 0)
                                                                    -
                                                                @endif
                                                                @if ($status[$i] == 1)
                                                                    {{ $approved_date[$i] }} -
                                                                    {{ translate('Present') }}
                                                                @endif
                                                                @if ($status[$i] == 2)
                                                                    {{ $approved_date[$i] }} -
                                                                    {{ $cancelled_date[$i] }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="customDomainModal" tabindex="-1" role="dialog" aria-labelledby="customDomainModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Request Custom Domain') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="domain-request-error"></div>
                    <div class="form-group mb-20">
                        <label for="current_domain"
                            class="mb-2 font-14 bold black">{{ translate('Select Subdomain') }}<span class="text-danger"> *
                            </span></label>
                        <select class="theme-input-style" id="current_domain" name="current_domain">
                            <option>{{ translate('Select Subdomain') }}</option>
                            @for ($i = 0; $i < sizeof($saas_account); $i++)
                                @if (!$saas_account[$i]->hasPendingDomainRequest() && $saas_account[$i]->status == 1)
                                    <option value="{{ $saas_account[$i]->domain->id }}">
                                        {{ $saas_account[$i]->domain->main_domain }}
                                    </option>
                                @endif
                            @endfor
                        </select>
                    </div>

                    <div class="form-group mb-20">
                        <label for="custom_domain" class="mb-2 font-14 bold black">{{ translate('Custom Domain') }}<span
                                class="text-danger">
                                *
                            </span>
                        </label>
                        <input type="text" id="custom_domain" name="custom_domain" class="theme-input-style"
                            placeholder="Enter Domain">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary sm"
                        data-dismiss="modal">{{ translate('Close') }}</button>
                    <button type="button" class="btn btn-primary sm"
                        id="send_request">{{ translate('Send Request') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/data-table/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>

    <script type="application/javascript">
        (function($) {
            "use strict";
            $("#domain_request_history").DataTable();
        })(jQuery);


        /**
         * Will send custom domain request 
         */ 
        $('#send_request').on('click',function(){
            'use strict'
            let current_domain = $('#current_domain').val()
            let custom_domain = $('#custom_domain').val()
            let param = {
                _token: '{{ csrf_token() }}',
                current_domain: current_domain,
                custom_domain: custom_domain
            }
            
            $.post("{{ route('plugin.saas.request.custom.domain') }}", param,
            function(data, status) {
                $('#domain-request-error').addClass('d-none')
                toastr.success("{{ translate('Your request hasbeen sent') }}");
                location.reload()
            })
            .fail(function(xhr, status, error) {
                let error_response = JSON.parse(xhr.responseText)
                let error_message = error_response.message

                $('#domain-request-error').removeClass('d-none')
                $('#domain-request-error').html(error_message)
            });
        })

    </script>
@endsection
