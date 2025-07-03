@extends('core::base.layouts.master')
@section('title')
    {{ translate('Currencies') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="row">
        <!-- Currency List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Currencies') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.add.currency') }}"
                                class="btn long">{{ translate('Add Currency') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="currency_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Currency Name') }}</th>
                                <th>{{ translate('Currency Symbol') }}</th>
                                <th>{{ translate('Currency code ') }}</th>
                                <th>{{ translate('Conversion Rate') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($all_currencies as $currency)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $currency->name }}</td>
                                    <td class="currency-font">{{ $currency->symbol }}</td>
                                    <td>{{ $currency->code }}</td>
                                    <td>{{ $currency->conversion_rate }}</td>
                                    <td>
                                        <label class="switch medium">
                                            <input type="checkbox" class="currency_status"
                                                id="currency_status_{{ $currency->id }}" name="status"
                                                {{ $currency->status == 1 ? 'checked' : '' }}
                                                onchange="updateCurrencyStatus('{{ $currency->id }}')">
                                            <span class="control"></span>
                                        </label>
                                    </td>
                                    <td>
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
                                                    href="{{ route('plugin.tlcommercecore.ecommerce.edit.currency', $currency->id) }}">Edit</a>
                                                <a href="#"
                                                    onclick="deleteConfirmation('{{ $currency->id }}')">Delete</a>
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
        <!-- Currency List-->

        <!--Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                        <form method="POST" action="{{ route('plugin.tlcommercecore.ecommerce.currency.delete') }}">
                            @csrf
                            <input type="hidden" id="currency_id" name="id">
                            <button type="button" class="btn long mt-2 btn-danger"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Delete Modal-->
    </div>
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script type="application/javascript">
       (function($) {
            "use strict";
            $("#currency_table").DataTable();
        })(jQuery);    

        /**
         * Will request to update currency status
         */
        function updateCurrencyStatus(currency_id) {
            "use strict";
            let status = 2
            if ($('#currency_status_' + currency_id).is(":checked")) {
                status = 1
            }
            $.post("{{ route('plugin.tlcommercecore.ecommerce.update.currency.status') }}", {
                    _token: '{{ csrf_token() }}',
                    id: currency_id,
                    status: status
                },
                function(data, status) {
                    if(data.success){
                        toastr.success(data.message);
                    }else{
                        toastr.error(data.message);
                        location.reload();
                    }
                    
                }).fail(function(xhr, status, error) {
                toastr.error("Unable to update currency status");
            });
        }

        /**
         * show delete confirmation modal
         */
        function deleteConfirmation(currency_id) {
            "use strict";
            $("#currency_id").val(currency_id);
            $('#delete-modal').modal('show');
        }
</script>
@endsection
