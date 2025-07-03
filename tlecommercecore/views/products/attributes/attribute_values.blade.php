@extends('core::base.layouts.master')
@section('title')
    {{ translate('Attributes Values') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Attribute Values') }}</h4>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Attribute Values') }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="attributeValueTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attribute_details->attribute_values as $key => $attribute)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $attribute->name }}</td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-status"
                                                data-attribute="{{ $attribute->id }}"
                                                {{ $attribute->status == '1' ? 'checked' : '' }}>
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
                                                <a
                                                    href="{{ route('plugin.tlcommercecore.product.attributes.values.edit', $attribute->id) }}">
                                                    {{ translate('Edit') }}
                                                </a>
                                                <a href="#" class="delete-attribute-value"
                                                    data-attribute="{{ $attribute->id }}">{{ translate('Delete') }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New Value') }}</h4>
                <form action="{{ route('plugin.tlcommercecore.product.attributes.values.store') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Attribute') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="attribute_name" readonly class="theme-input-style"
                                value="{{ $attribute_details->name }}">
                            <input type="hidden" name="attribute_id" value="{{ $attribute_details->id }}">
                            @if ($errors->has('attribute_id'))
                                <div class="invalid-input">{{ $errors->first('attribute_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Value') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="theme-input-style " value="{{ old('name') }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.attributes.values.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-attribute-value-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
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
        (function($) {
            "use strict";
            /**
             * Attribute values data table
             */
            $("#attributeValueTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#attributeValueTable_wrapper .col-md-6:eq(0)');
            /**
             * 
             * Change status 
             * 
             * */
            $(document).on('click', '.change-status', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('attribute');
                $.post('{{ route('plugin.tlcommercecore.product.attributes.value.status.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })
            });
            /**
             * 
             * Delete attribute value
             * 
             * */
            $('.delete-attribute-value').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('attribute');
                $("#delete-attribute-value-id").val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
