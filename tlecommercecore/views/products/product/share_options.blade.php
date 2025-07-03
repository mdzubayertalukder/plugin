@extends('core::base.layouts.master')
@section('title')
    {{ translate('Share Options') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Share Options') }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="shareOtionsTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($share_options as $key => $option)
                                <tr>
                                    <td>
                                        {{ $key + 1 }}
                                    </td>
                                    <td>{{ $option->network_name }}</td>
                                    <td>
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" class="change-status" data-option="{{ $option->id }}"
                                                {{ $option->status == '1' ? 'checked' : '' }}>
                                            <span class="control"></span>
                                        </label>
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.conditions.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-condition-id" name="id">
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
            /**
             * Product share options table
             */
            $(function() {
                $("#shareOtionsTable").DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": false,
                })
            });
            /**
             * 
             * Change status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('option');
                $.post('{{ route('plugin.tlcommercecore.products.share.options.update.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })
            });
        })(jQuery);
    </script>
@endsection
