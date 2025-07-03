@extends('core::base.layouts.master')
@section('title')
    {{ translate('Categories') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Categories') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('plugin.tlcommercecore.product.category.new') }}"
                                class="btn long">{{ translate('Add New Category') }}</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="categoryTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Parent') }}</th>
                                <th>{{ translate('Icon') }}</th>
                                <th>{{ translate('Featured') }} </th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center mb-3">
                                            <label class="position-relative mr-2">
                                                <input type="checkbox" name="items[]" class="item-id"
                                                    value="{{ $category->id }}">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $category->translation('name', getLocale()) }}</td>
                                    <td>
                                        @if ($category->parentCategory != null)
                                            {{ $category->parentCategory->translation('name', getLocale()) }}
                                        @endif
                                    </td>
                                    <td>
                                        <img src="{{ asset(getFilePath($category->icon)) }}" class="img-45"
                                            alt="{{ $category->name }}">
                                    </td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-featured"
                                                data-category="{{ $category->id }}" @checked($category->is_featured == config('settings.general_status.active'))>
                                            <span class="control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="status" class="change-status"
                                                data-category="{{ $category->id }}" @checked($category->status == config('settings.general_status.active'))>
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
                                                    href="{{ route('plugin.tlcommercecore.product.category.edit', ['id' => $category->id, 'lang' => getDefaultLang()]) }}">
                                                    {{ translate('Edit') }}
                                                </a>
                                                <a href="#" class="delete-category"
                                                    data-cat="{{ $category->id }}">{{ translate('Delete') }}</a>
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.category.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-category-id" name="id">
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
        (function($) {
            "use strict";
            $("#categoryTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#categoryTable_wrapper .col-md-6:eq(0)');
            var bulk_actions_dropdown =
                '<div id="bulk-action" class="dataTables_length d-flex"><select class="theme-input-style bulk-action-selection mr-3"><option value="">{{ translate('Bulk Action') }}</option><option value="delete_all">{{ translate('Delete selection') }}</option></select><button class="btn long apply-bulk-action-btn">{{ translate('Apply') }}</button></div>';

            $(bulk_actions_dropdown).insertAfter("#categoryTable_wrapper #categoryTable_length");
            /**
             * 
             * Select all Items
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            });
            /**
             * 
             * Bulk action
             **/
            $('.apply-bulk-action-btn').on('click', function(e) {
                let action = $('.bulk-action-selection').val();
                if (action === 'delete_all') {
                    var selected_items = [];
                    $('input[name^="items"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });

                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.product.category.delete.bulk') }}', {
                            _token: '{{ csrf_token() }}',
                            data: selected_items
                        }, function(data) {
                            location.reload();
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}', "Error!");
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}', "Error!");
                }
            });
            /**
             * 
             * Change featured  status 
             * 
             * */
            $(document).on('click', '.change-featured', function(e) {
                let $this = $(this);
                let id = $this.data('category');
                $.post('{{ route('plugin.tlcommercecore.product.category.featured.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success(
                            '{{ translate('Category featured status updated successfully') }}',
                            "Success");
                    } else {
                        toastr.error('{{ translate('Category featured status update failed') }}',
                            "Error!");
                    }
                });
            });
            /**
             * 
             * Change  status 
             * 
             * */
            $(document).on('click', '.change-status', function(e) {
                let $this = $(this);
                let id = $this.data('category');
                $.post('{{ route('plugin.tlcommercecore.product.category.status.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success('{{ translate('Category status updated successfully') }}',
                            "Success");
                    } else {
                        toastr.error('{{ translate('Category status update failed') }}', "Error!");
                    }
                });
            });
            /**
             * 
             * Delete category
             * 
             * */
            $(document).on('click', '.delete-category', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('cat');
                $("#delete-category-id").val(id);
                $('#delete-modal').modal('show');

            });
        })(jQuery);
    </script>
@endsection
