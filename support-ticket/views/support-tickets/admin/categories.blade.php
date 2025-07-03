@extends('core::base.layouts.master')
@section('title')
    {{ translate('Support Ticket Categories') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <!-- Category List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('All Ticket Categories') }}</h4>
                        <div class="d-flex flex-wrap">
                            <a href="#" class="btn long" data-toggle="modal"
                                data-target="#addCategory">{{ translate('Create') }}</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="category_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $key }}.</td>
                                    <td> {{ $category->name }} </td>
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
                                                <a href="#"
                                                    onclick="editCategory(`{{ $category->id }}`,`{{$category->name}}`)">{{ translate('Edit') }}</a>
                                                <a href="#"
                                                    onclick="deleteConfirmation('{{ $category->id }}')">{{ translate('Delete') }}</a>
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
                        {!! $categories->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- Category List-->

        <!-- Add Category -->
        <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="addCategory"
            aria-hidden="true">
            <form method="POST" action="{{ route('store.ticket.category') }}">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ translate('Add Category') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row mb-20">
                                <input type="text" name="category_name" id="category_name" class="form-control"
                                    value="" placeholder="{{ translate('Category Name') }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary sm"
                                data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn btn-primary sm">{{ translate('Save changes') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /Add Category -->

        <!-- Update Category -->
        <div class="modal fade" id="updateCategory" tabindex="-1" role="dialog" aria-labelledby="updateCategory"
            aria-hidden="true">
            <form method="POST" action="{{ route('update.ticket.category') }}">
                @csrf
                <input type="hidden" id="editable_category_id" name="category_id">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ translate('Update Category') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row mb-20">
                                <input type="text" name="category_name" id="editable_category_name" class="form-control"
                                    value="" placeholder="{{ translate('Category Name') }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary sm"
                                data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn btn-primary sm">{{ translate('Update changes') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /Update Category -->

        <!--Category Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">
                            {{ translate('Are you sure to delete this category ? Once you delete, all data related to this will be deleted') }}.
                        </p>
                        <form method="POST" action="{{ route('ticket.category.delete') }}"
                            id="category-delete-form">
                            @csrf
                            <input type="hidden" id="category_id_to_delete" name="category_id">
                            <button type="button" class="btn long mt-2  btn-danger"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2" id="categorys-delete-btn">
                                {{ translate('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Category Delete Modal End-->

    </div>
@endsection
@section('custom_scripts')
    <script>
        /**
         * show category delete confirmation modal
         */
        function deleteConfirmation(id) {
            "use strict";
            $("#category_id_to_delete").val(id);
            $('#delete-modal').modal('show');
        }

        /**
         * Will show category editable form 
         */
        function editCategory(id,name) {
            "use strict";
            $("#editable_category_id").val(id);
            $("#editable_category_name").val(name);
            $('#updateCategory').modal('show');
        }
    </script>
@endsection
