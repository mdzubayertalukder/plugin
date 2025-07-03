@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ translate('Subscribe Now') }}
@endsection
@section('main_content')
    <section id="pricing">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-center mb-5 row">
                    @foreach ($package_plans as $plan)
                        <button class="btn btn-info sm mr-1 package-plan mt-2" id="{{ strtolower($plan->id) }}"
                            onclick="getPackagesAccordingToPlan('{{ $plan->id }}')">
                            {{ translate($plan->name) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row" id="packages"></div>
    </section>
@endsection
@section('custom_scripts')
    <script>
        $(document).ready(function() {
            'use strict'
            getPackagesAccordingToPlan('{{ $first_plan }}')
        });

        /**
         * Get all packages according to selected plan
         */
        function getPackagesAccordingToPlan(plan_id) {
            'use strict'
            $('#packages').html('<p>Loading.....</p>');
            $('#pricing').addClass('disabled-section');
            $.post("{{ route('plugin.saas.get.packages.according.to.plan') }}", {
                    _token: '{{ csrf_token() }}',
                    plan_id: plan_id,
                    is_for_payment: 1,
                    store_id: '{{ isset($store_id) ? $store_id : 'null' }}'
                })
                .done(function(data) {
                    $('#packages').html(data);
                    $('.package-plan').removeClass('btn-success')
                    $('.package-plan').addClass('btn-info')
                    $('#' + plan_id).addClass('btn-success')
                    $('#pricing').removeClass('disabled-section')
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#pricing').removeClass('disabled-section');
                    $('#packages').html('<p>No Package Found</p>')
                });
        }

        /**
         * Set data in modal incase of free package
         */
        function setDataInModalForFreePackage(is_for_update, store_id, package_id, plan_id, membership_type) {
            'use strict';

            $('#is_for_update').val(is_for_update)
            $('#store_id').val(store_id)
            $('#package_id').val(package_id)
            $('#plan_id').val(plan_id)
            $('#membership_type').val(membership_type)
        }
    </script>
@endsection
