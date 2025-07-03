@extends('core::base.layouts.master')
@section('title')
    {{ translate('Tax Profiles') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-money"></i> {{ translate('Tax Profiles') }}</h4>
    </div>
    <div class="row">
        <div class="col-12 mb-20">
            @if (getEcommerceSetting('enable_tax_in_checkout') != config('settings.general_status.active'))
                <p class="alert alert-danger font-13">
                    {{ translate('Product tax is disabled. You can enable tax from') }}
                    <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}"
                        class="btn-link">{{ translate('Tax Settings') }}
                    </a>
                </p>
            @else
                <p class="alert alert-info font-13">
                    {{ translate('You can disable tax from') }}
                    <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}"
                        class="btn-link">{{ translate('Tax Settings') }}
                    </a>
                </p>
            @endif
        </div>
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Tax Profiles') }}</h4>
                        <div class="d-flex flex-wrap">
                            <button class="btn long open-zone-create-modal" data-toggle="modal"
                                data-target="#tax-profile-form-modal">{{ translate('Create Tax Profile') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (count($tax_profiles) > 0)
                            <table id="taxTable" class="hoverable text-nowrap border-top2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ translate('Tax Profile') }}</th>
                                        <th>{{ translate('Created At') }}</th>
                                        <th>{{ translate('Status') }}</th>
                                        <th>{{ translate('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tax_profiles as $key => $profile)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $profile->title }}</td>
                                            <td>{{ $profile->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if ($profile->status == config('settings.general_status.active'))
                                                    <p class="badge badge-success">{{ translate('Active') }}</p>
                                                @else
                                                    <p class="badge badge-danger">{{ translate('Inactive') }}</p>
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
                                                        <a
                                                            href="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.manage.rates', ['id' => $profile->id]) }}">
                                                            {{ translate('Manage Tax Rates') }}
                                                        </a>
                                                        <a href="#" class="edit-profile"
                                                            data-id="{{ $profile->id }}"
                                                            data-title="{{ $profile->title }}"
                                                            data-status="{{ $profile->status }}">
                                                            {{ translate('Edit') }}
                                                        </a>
                                                        <a href="#" class="delete-profile"
                                                            data-id="{{ $profile->id }}">
                                                            {{ translate('Delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        @else
                            <p class="alert alert-danger text-center">{{ translate('No tax profile found') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Tax profile create Modal-->
    <div id="tax-profile-form-modal" class="tax-profile-form-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('New Tax profile') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tax-profile-form">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Title') }} </label>
                            <input type="text" name="title" class="theme-input-style"
                                placeholder="{{ translate('Enter Title') }}">
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Status') }} </label>
                            <select name="status" class="theme-input-style">
                                <option value="{{ config('settings.general_status.active') }}">{{ translate('Active') }}
                                </option>
                                <option value="{{ config('settings.general_status.in_active') }}">
                                    {{ translate('Inactive') }}
                                </option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long store-new-tax-profie-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax profile create Modal-->

    <!--Tax profile edit Modal-->
    <div id="tax-profile-edit-modal" class="tax-profile-edit-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Tax Profile Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tax-profile-update-form">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Title') }} </label>
                            <input type="hidden" name="id" id="tax-profile-id">
                            <input type="text" name="title" id="tax-profile-title" class="theme-input-style"
                                placeholder="{{ translate('Enter Title') }}">
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Status') }} </label>
                            <select name="status" class="theme-input-style" id="status">
                                <option value="{{ config('settings.general_status.active') }}">{{ translate('Active') }}
                                </option>
                                <option value="{{ config('settings.general_status.in_active') }}">
                                    {{ translate('Inactive') }}
                                </option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long update-tax-profie-btn">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Tax profile edit Modal-->
    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST"
                        action="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.delete.profile') }}">
                        @csrf
                        <input type="hidden" id="delete-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script>
        /**
         * Vat and tax table
         */
        (function($) {
            "use strict";
            $("#taxTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#taxTable_wrapper .col-md-6:eq(0)');

            /**
             *create new tax profile
             * 
             **/
            $('.store-new-tax-profie-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#tax-profile-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.store.profile') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New tax profile created successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('New tax profile create failed');
                        }
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
             * 
             * Edit item
             * 
             * */
            $('.edit-profile').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('id');
                let title = $this.data('title');
                let status = $this.data('status');
                $("#tax-profile-id").val(id);
                $("#tax-profile-title").val(title);
                $("#status").val(status);
                $('#tax-profile-edit-modal').modal('show');
            });
            /**
             *Update tax profile
             * 
             **/
            $('.update-tax-profie-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#tax-profile-update-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.update.profile') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Tax profile updated successfully');
                            location.reload();
                        }
                        if (!response.success) {
                            toastr.error('Tax profile update failed');
                        }
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
             * 
             * Delete item
             * 
             * */
            $('.delete-profile').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('id');
                $("#delete-id").val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
