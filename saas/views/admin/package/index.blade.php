@extends('core::base.layouts.master')

@section('title')
    {{ translate('Package List') }}
@endsection

@section('main_content')
    <section id="pricing">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="d-flex justify-content-center mb-5 row">
                    @foreach ($package_plans as $plan)
                        <button class="btn btn-info sm mr-1 package-plan mt-2" id="{{ strtolower($plan->id) }}"
                            onclick="getPackagesAccordingToPlan('{{ $plan->id }}')"> {{ translate($plan->name) }}
                        </button>
                    @endforeach
                    <a href="{{ route('plugin.saas.create.package') }}" class="btn  sm mr-1  mt-2">
                        {{ translate('Create New Package') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="row" id="packages"></div>
        <!--Delete Modal-->
        <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                        <form method="POST" action="{{ route('plugin.saas.delete.package') }}">
                            @csrf
                            <input type="hidden" id="package_id" name="id">
                            <button type="button" class="btn btn-danger long mt-2" id="close_modal"
                                data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Delete Modal-->
    </section>
@endsection
@section('custom_scripts')
    <script>
        $(document).ready(function() {
            'use strict'
            getPackagesAccordingToPlan('{{ $first_plan }}')

            $('#close_modal').on('click', function() {
                $('#delete-modal').hide()
            })
        });

        /**
         * Delete package confirmation modal 
         **/
        function deletePackage(package_id) {
            'use strict'
            $('#package_id').val(package_id)
            $('#delete-modal').show()
        }

        /**
         * Will request for packages according to plan
         */
        function getPackagesAccordingToPlan(plan_id) {
            'use strict'
            $('#pricing').addClass('disabled-section');
            $('#packages').html('<p>Loading......</p>');
            $.post("{{ route('plugin.saas.get.packages.according.to.plan') }}", {
                    _token: '{{ csrf_token() }}',
                    plan_id: plan_id
                })
                .done(function(data) {
                    $('#packages').html(data)
                    $('.package-plan').removeClass('btn-success')
                    $('.package-plan').addClass('btn-info')
                    $('#' + plan_id).addClass('btn-success')
                    $('#pricing').removeClass('disabled-section')
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#pricing').removeClass('disabled-section');
                    $('#packages').html('<p class="text-danger">Something went wrong.</p>');
                });
        }
    </script>
@endsection
