@php
    $request = app('request');
    $current_domain = $request->getHost();
@endphp
@extends('core::base.layouts.master')
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
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Custom Domains') }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2" id="domain_request_history">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }} </th>
                                <th>{{ translate('Requested For Domain') }} </th>
                                <th>{{ translate('Requested Domain') }} </th>
                                <th>{{ translate('Duration') }} </th>
                                <th>{{ translate('Action') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($domain_request as $domain)
                                @php
                                    $status = '';
                                    $class = 'badge-danger';
                                    if ($domain->status == 0) {
                                        $status = 'Pending';
                                        $class = 'badge-warning';
                                    }
                                    if ($domain->status == 1) {
                                        $status = 'Approved';
                                        $class = 'badge-success';
                                    }
                                    if ($domain->status == 2) {
                                        $status = 'Cancelled';
                                        $class = 'badge-danger';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $domain->saasAccount->store_name }}</td>
                                    <td>{{ $domain->current_domain }} <span
                                            class="badge mb-2 {{ $class }}">{{ $status }}</span></td>
                                    <td>{{ $domain->requested_domain }}</td>
                                    <td>
                                        @if ($domain->status == 0)
                                            -
                                        @endif
                                        @if ($domain->status == 1)
                                            {{ $domain->approved_date }} - {{ translate('Present') }}
                                        @endif
                                        @if ($domain->status == 2)
                                            {{ $domain->approved_date }} - {{ $domain->cancelled_date }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center justify-content-end"
                                                data-toggle="dropdown">
                                                <div class="menu-icon mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="update-domain-status-btn"
                                                    data-status="{{ $domain->status }}" data-id="{{ $domain->id }}">
                                                    {{ translate('Update Status') }}
                                                </a>
                                                <a href="#" class="delete-custom-domain-btn"
                                                    data-id="{{ $domain->id }}">
                                                    {{ translate('Delete') }}
                                                </a>
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update-status-modal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Update Status') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('plugin.saas.admin.update.custom.domain') }}">
                    @csrf
                    <input type="hidden" id="update-custom-domain-id" name="id">
                    <div class="modal-body">
                        <div class="form-group mb-20">
                            <select class="theme-input-style" id="domain-status" name="domain_status">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary sm"
                            data-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit" class="btn btn-primary sm">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this custom domain') }}?</p>
                    <form method="POST" action="{{ route('plugin.saas.admin.delete.custom.domain') }}">
                        @csrf
                        <input type="hidden" id="delete-custom-domain-id" name="id">
                        <div class="form-row d-flex justify-content-between">
                            <button type="button" class="btn long mt-2 btn-danger"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                        </div>
                    </form>
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

        /**
         * Custom domain delete confirmation
         */ 
        $('.delete-custom-domain-btn').on('click',function(e) {
            "use strict";
            e.preventDefault();
            let id = $(this).data('id');
            $('#delete-custom-domain-id').val(id);
            $("#delete-modal").modal("show");
        })

        $('.update-domain-status-btn').on('click',function(e) {
            "use strict";
            let status = $(this).data('status');
            let id = $(this).data('id');
            let html = ""
            if(status == 0){
                html = `<option value="1">{{ translate('Approved') }}</option>
                            <option value="2">{{ translate('Cancelled') }}</option>
                            <option value="0" selected>{{ translate('Pending') }}</option>`
            }
            if(status == 1){
                html = `<option value="1" selected>{{ translate('Approved') }}</option>
                            <option value="2">{{ translate('Cancelled') }}</option>`
            }
            if(status == 2){
                html = `<option value="1">{{ translate('Approved') }}</option>
                            <option value="2" selected>{{ translate('Cancelled') }}</option>`
            }

            $('#domain-status').html(html)
            $('#update-custom-domain-id').val(id)
            $("#update-status-modal").modal("show");
        })

    </script>
@endsection
