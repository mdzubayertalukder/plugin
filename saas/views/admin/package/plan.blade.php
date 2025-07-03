@extends('core::base.layouts.master')
@section('title')
    {{ translate('Package Plans') }}
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
        <!-- Package Plans-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Package Plans') }}</h4>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn long float-right" data-toggle="modal"
                                data-target="#createPlan">{{ translate('Add New Plan') }}</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2" id="user_table">
                        <thead>
                            <tr>
                                <td>#</td>
                                <th>{{ translate('Plans') }}</th>
                                <th>{{ translate('Duration') }}s</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($plans as $plan)
                                @if ($plan->id != config('saas.plans.lifetime'))
                                    <tr>
                                        <td>{{ $key++ }}</td>
                                        <td>{{ translate($plan->name) }}</td>
                                        <td>{{ $plan->duration }} {{ translate('Days') }}</td>
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
                                                    <a href="#" data-toggle="modal" data-target="#editPlan"
                                                        onclick="setPlanEditableData('{{ $plan->id }}','{{ $plan->name }}','{{ $plan->duration }}')">{{ translate('Edit') }}</a>
                                                    <a href="#"
                                                        onclick="deleteConfirmation('{{ $plan->id }}')">{{ translate('Delete') }}</a>
                                                </div>
                                            </div>
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
        <!-- Package Plans-->

        <!-- Create Package Plan -->
        <div class="modal fade" id="createPlan" tabindex="-1" role="dialog" aria-labelledby="createPlanLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPlanLabel">{{ translate('Create Package Plan') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="#" id="createPlanForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-form-label">{{ translate('Plan Name') }}</label>
                                <input type="text" class="form-control" name="plan_name"
                                    placeholder="{{ translate('Enter Plan Name') }}">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ translate('Plan Duration (Days)') }}</label>
                                <input type="number" class="form-control" name="plan_duration"
                                    placeholder="{{ translate('Enter Plan Duration') }}">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn long btn-danger"
                                data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn long float-right">{{ translate('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Create Package Plan-->

        <!-- Edit Package Plan -->
        <div class="modal fade" id="editPlan" tabindex="-1" role="dialog" aria-labelledby="editPlanLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlanLabel">{{ translate('Update Package Plan') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="#" id="editPlanForm">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="plan-name" class="col-form-label">{{ translate('Plan Name') }}</label>
                                <input type="text" class="form-control" id="editable-plan-name" name="plan_name">
                            </div>
                            <div class="form-group">
                                <label for="plan-duration"
                                    class="col-form-label">{{ translate('Plan Duration (Days)') }}</label>
                                <input type="number" class="form-control" id="editable-plan-duration"
                                    name="plan_duration">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn long btn-danger"
                                data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn long float-right">{{ translate('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Package Plan-->

        <!--Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                        <form method="POST" action="{{ route('plugin.saas.delete.package.plan') }}">
                            @csrf
                            <input type="hidden" id="pacakge_plan_id" name="id">
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

            var form = $('#createPlanForm');
            form.on('submit', function(e) {
                e.preventDefault();
                $('p.text-danger').remove();
                var token = '{{csrf_token()}}';
                var formData = form.serialize();
                $.ajax({
                    url: '{{route('plugin.saas.store.package.plan')}}',
                    type: 'POST',
                    data: formData + '&_token=' + token,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{translate("Package plan creation successful!")}}');
                            location.reload()
                        } else {
                           toastr.error("{{translate('Package plan creation unsuccessful!')}}");
                        }
                    },
                    error: function(xhr, status, error) {
                       if(xhr.status==422){
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key + '"], select[name="' + key + '"]');
                                inputField.after('<p class="text-danger">' + value[0] + '</p>');
                            });
                       }else{
                           toastr.error("{{translate('Package plan creation unsuccessful!')}}");
                       }
                    }
                });
            });

            var editable_form = $('#editPlanForm');
            editable_form.on('submit', function(e) {
                e.preventDefault();
                 $('p.text-danger').remove();
                var token = '{{csrf_token()}}';
                var formData = editable_form.serialize();
                $.ajax({
                    url: '{{route('plugin.saas.update.package.plan')}}',
                    type: 'POST',
                    data: formData + '&_token=' + token,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{translate("Package plan updated successful!")}}');
                            location.reload()
                        } else {
                            toastr.error("{{translate('Package plan update failed!')}}");
                        }
                    },
                    error: function(xhr, status, error) {
                        if(xhr.status==422){
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key + '"], select[name="' + key + '"]');
                                inputField.after('<p class="text-danger">' + value[0] + '</p>');
                            });
                       }else{
                           toastr.error("{{translate('Package plan update failed!')}}");
                       }
                    }
                });
            });
        })(jQuery);

        /**
         * Will set editable plan data
         */ 
        function setPlanEditableData(id,name,duration) {
            'use strict'
            $('#id').val(id)
            $('#editable-plan-name').val(name)
            $('#editable-plan-duration').val(duration)
        }
        

        /**
         * show delete confirmation modal
         */
        function deleteConfirmation(pacakge_plan_id) {
            "use strict";
            $("#pacakge_plan_id").val(pacakge_plan_id);
            $('#delete-modal').modal('show');
        }
</script>
@endsection
