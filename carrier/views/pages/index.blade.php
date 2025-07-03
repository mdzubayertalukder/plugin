@extends('core::base.layouts.master')
@section('title')
    {{ translate('Shipping Carriers') }}
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-vehicle-delivery-van"></i> {{ translate('Shipping & Delivery') }}</h4>
    </div>
    <div class="row">
        <!--3rd party courier-->
        <div class="col-12" id="ShippingCarriers">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ translate('Shipping Carriers') }}</h4>
                        <div class="d-flex align-items-center gap-15">
                            <a href="#" class="btn long mr-2" data-toggle="modal"
                                data-target="#new-courier-modal">{{ translate('Create new Carrier') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">

                        <table class="dh-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Logo') }}</th>
                                    <th>{{ translate('Name') }}</th>
                                    <th>{{ translate('Tracking url') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th class="text-right">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($couriers) > 0)
                                    @foreach ($couriers as $key => $courier)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><img src="{{ asset(getFilePath($courier['logo'])) }}" class="img-45"
                                                    alt="{{ $courier['name'] }}"></td>
                                            <td>{{ $courier['name'] }}</td>
                                            <td>{{ $courier['tracking_url'] }}</td>
                                            <td>
                                                <label class="switch glow primary medium">
                                                    <input type="checkbox" class="courier-status"
                                                        data-courier="{{ $courier['id'] }}"
                                                        @if ($courier['status'] == config('settings.general_status.active')) checked @endif>
                                                    <span class="control"></span>
                                                </label>
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
                                                        <a href="#" data-courier="{{ $courier['id'] }}"
                                                            class="edit-courier">{{ translate('Edit') }}</a>
                                                        <a href="#" data-courier="{{ $courier['id'] }}"
                                                            class="delete-courier">{{ translate('Delete') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--End 3rd Party courier-->
    </div>
    <!--New Courier Modal-->
    <div id="new-courier-modal" class="new-courier-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Shipping Courier') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-courier-form">
                        @csrf
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                            <input type="text" name="name"
                                placeholder="{{ translate('Type name') }}"class="theme-input-style">

                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Tracking url') }} </label>
                            <input type="text" name="tracking_url" placeholder="{{ translate('Type url') }}"
                                class="theme-input-style">

                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black col-12">{{ translate('Logo') }} </label>
                            @include('core::base.includes.media.media_input', [
                                'input' => 'logo',
                                'data' => old('logo'),
                            ])

                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn long store-courier-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End New Courier Modal-->
    <!--Edit Courier Modal-->
    <div id="edit-courier-modal" class="edit-courier-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Shipping Courier Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body edit-courier-data">

                </div>
            </div>
        </div>
    </div>
    <!--End Edit Courier Modal-->

    <!--Delete Courier Modal-->
    <div id="delete-courier-modal" class="delete-courier-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.carrier.shipping.courier.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-courier-id" name="id">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Courier Modal-->
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            initDropzone()
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()
            });
            /** 
             * Will Store new courier
             *   
             **/
            $('.store-courier-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-courier-form').serialize(),
                    url: '{{ route('plugin.carrier.shipping.courier.store') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
            /**
             * Change courier status
             **/
            $('.courier-status').on('click', function(e) {
                let $this = $(this);
                let id = $this.data('courier');
                $.post('{{ route('plugin.carrier.shipping.courier.status.update') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })

            });
            /**
             * Edit curier
             * 
             **/
            $('.edit-courier').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('courier');
                let data = {
                    id: id,
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: data,
                    url: '{{ route('plugin.carrier.shipping.courier.edit') }}',
                    success: function(data) {
                        $('.edit-courier-data').html(data)
                        $('#edit-courier-modal').modal('show')
                    }
                });
            });
            /**
             * Will delete courier
             * 
             **/
            $('.delete-courier').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('courier');
                $('#delete-courier-id').val(id);
                $("#delete-courier-modal").modal('show');
            });
            /**
             * Activate courier
             * 
             **/
            $('.activate-courier').on('click', function(e) {
                e.preventDefault();
                $.post('{{ route('plugin.carrier.shipping.courier.module.status.update') }}', {
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    location.reload();
                })
            });
        })(jQuery);
    </script>
@endsection
