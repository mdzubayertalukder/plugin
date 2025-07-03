@extends('core::base.layouts.master')
@section('title')
    {{ translate('Coupons') }}
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

    <style>
        .hover-hand {
            cursor: pointer;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <!-- Coupon List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Coupons') }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="user_table">
                        <thead>
                            <tr>
                                <td>#</td>
                                <th>{{ translate('Code') }}</th>
                                <th>{{ translate('Type') }}</th>
                                <th>{{ translate('Packages') }}</th>
                                <th>{{ translate('Discount') . '(%)' }}</th>
                                <th>{{ translate('Valid For(days)') }}</th>
                                <th>{{ translate('Valid Untill') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>
                                        <span>
                                            <code id="coupon-{{ $key }}">{{ $coupon->coupon_code }}</code>
                                        </span>
                                        <i class="icofont-copy hover-hand"
                                            onclick="copyInnerHTML('coupon-{{ $key }}')"></i>
                                    </td>
                                    <td>{{ $coupon->coupon_type }}</td>
                                    <td>
                                        @forelse ($coupon->packages as $package)
                                            <span class="badge badge-primary">{{ $package->name }}</span>
                                        @empty
                                            <span class="badge badge-danger">No Package Found</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $coupon->discount }}</td>
                                    <td>{{ $coupon->valid_for_days }}</td>
                                    <td>{{ $coupon->valid_till }}</td>
                                    <td>
                                        @if ($coupon->status == 0)
                                            <span class="badge badge-success">{{ translate('Unused') }}</span>
                                        @else
                                            <span
                                                class="badge badge-warning">{{ translate('Used') }}{{ '(' . $coupon->total_used . ')' }}</span>
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
                                                <a
                                                    href="{{ route('plugin.saas.edit.coupon', $coupon->id) }}">{{ translate('Edit') }}</a>
                                                <a href="#"
                                                    onclick="deleteConfirmation('{{ $coupon->id }}')">{{ translate('Delete') }}</a>
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
        <!-- Coupon List-->

        <!--Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                        <form method="POST" action="{{ route('plugin.saas.delete.coupon') }}">
                            @csrf
                            <input type="hidden" id="coupon_id" name="id">
                            <button type="button" class="btn long mt-2"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2 btn-danger">{{ translate('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Delete Modal-->
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
            $("#user_table").DataTable();
        })(jQuery);

        /**
         * show delete confirmation modal
         */
        function deleteConfirmation(coupon_id) {
            "use strict";
            $("#coupon_id").val(coupon_id);
            $('#delete-modal').modal('show');
        }

        /**
         * Copy coupon code
         */ 
         function copyInnerHTML(id) {
            "use strict";

            // Get the inner HTML of the tag
            var innerHTML = $("#" + id).html();

            // Create a temporary textarea element
            var tempTextArea = $("<textarea>");

            // Set the value of the textarea to the inner HTML
            tempTextArea.val(innerHTML);

            // Append the textarea to the document
            $("body").append(tempTextArea);

            // Select the content of the textarea
            tempTextArea.select();

            // Copy the selected content to the clipboard
            document.execCommand("copy");

            // Remove the temporary textarea from the document
            tempTextArea.remove();

            // Log the copied HTML (optional)
            toastr.success("You have successfully copied the coupon code", "Success!");
        }
</script>
@endsection
