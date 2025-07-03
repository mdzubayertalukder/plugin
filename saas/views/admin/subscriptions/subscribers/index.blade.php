@php
    $package_repository = new \Plugin\Saas\Repositories\PackageRepository();
    $packages = $package_repository->getAllActivePackages();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Subscriber List') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <!-- Subscriber List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0">
                    <h4 class="font-20">{{ translate('All Subscribers') }}</h4>
                    <button data-toggle="modal" data-target="#createSubscriber"
                        class="btn long">{{ translate('Add New Subscriber') }}</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="hoverable text-nowrap border-top2 " id="store_list">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Name') }}</th>
                                    <th>{{ translate('Email') }}</th>
                                    <th>{{ translate('Total Store') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $key = 1;
                                @endphp
                                @forelse ($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $key }}.</td>
                                        <td>
                                            <img src="{{ getFilePath($subscriber->image) }}" alt="{{ $subscriber->name }}"
                                                class="img-70 rounded-circle">
                                            <span class="ml-2">{{ $subscriber->name }}</span>
                                        </td>
                                        <td>
                                            @if ($subscriber->email_verified_at != null)
                                                <p class="badge badge-success mb-0" title="Email is verified"><i
                                                        class="icofont-verification-check"></i>
                                                </p>
                                            @else
                                                <p class="badge badge-danger mb-0" title="Email is not verified"><i
                                                        class="icofont-close"></i></p>
                                            @endif
                                            <span class="ml-1">{{ $subscriber->email }}</span>
                                        </td>
                                        <td> {{ $subscriber->total_store }} </td>
                                        <td>
                                            @if ($subscriber->status == 1)
                                                <p class="badge badge-success mb-0">{{ translate('Active') }}</p>
                                            @else
                                                <p class="badge badge-danger mb-0">{{ translate('Inactive') }}</p>
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
                                                    <a href="#" class="create-new-store"
                                                        data-id="{{ $subscriber->id }}"
                                                        data-name="{{ $subscriber->name }}">{{ translate('Create a New Store') }}
                                                    </a>
                                                    <a
                                                        href="{{ route('plugin.saas.subscriber.details', $subscriber->id) }}">{{ translate('Details') }}
                                                    </a>
                                                    <a href="#" data-id="{{ $subscriber->id }}"
                                                        class="edit-subscriber">{{ translate('Edit') }}</a>

                                                    <a href="#" class="delete-subscriber"
                                                        data-id="{{ $subscriber->id }}">{{ translate('Delete') }}
                                                    </a>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="7">
                                            <p class="alert mb-0">{{ translate('No Subscriber Found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="pgination px-3">
                            {!! $subscribers->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Subscriber List-->
    </div>
    <!-- Create Subscriber Modal-->
    <div class="modal fade" id="createSubscriber" tabindex="-1" role="dialog" aria-labelledby="createSubscriberLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add New Subscriber') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="subscriberAddForm">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Image') }}</label>
                            </div>
                            <div class="col-md-8">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'image',
                                    'data' => '',
                                ])
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Name') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="name" class="theme-input-style"
                                    placeholder="{{ translate('Enter Name') }}">
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-sm-4">
                                <label class="font-14 bold black">{{ translate('Email') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="email" name="email" class="theme-input-style"
                                    placeholder="{{ translate('Enter Email Address') }}">
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Password') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="password" class="theme-input-style"
                                    placeholder="{{ translate('Enter password') }}">
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Confirm Password') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="password_confirmation" class="theme-input-style"
                                    placeholder="{{ translate('Confirm password') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn long">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Create Subscriber Modal-->
    <!-- Edit Subscriber Modal-->
    <div class="modal fade" id="editSubscriber" tabindex="-1" role="dialog" aria-labelledby="editSubscriberLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Subscriber Information') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body edit-form-container">

                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Subscriber Modal-->
    <!--Subscriber Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">
                        {{ translate('Are you sure to delete this subscriber ? Once you delete, all data related to this will be deleted') }}.
                    </p>
                    <form method="POST" action="{{ route('plugin.saas.subscriber.delete') }}"
                        id="subscriber-delete-form">
                        @csrf
                        <input type="hidden" id="subscriber_id_to_delete" name="subscriber_id">
                        <button type="button" class="btn long mt-2  btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2" id="subscriber-delete-btn">
                            <span class="subscriber-delete-btn-label">{{ translate('Delete') }}</span>
                            <div class="spinner">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Subscriber Delete Modal-->

    <!-- Create Store-->
    <div class="modal fade" id="createStore" tabindex="-1" role="dialog" aria-labelledby="createStoreLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStoreLabel">{{ translate('Create New Store') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" method="post" id="new-store-form">
                        @csrf
                        <input type="hidden" name="subscriber_id" id="subscriber_id">
                        <div class="form-group mb-20">
                            <label class="mb-2 font-14 bold black">{{ translate('Subscriber') }}</label>
                            <input type="text" name="subscriber_name" class="theme-input-style subscriber_name"
                                readonly>
                        </div>
                        <div class="form-group mb-20">
                            <label for="store_name" class="mb-2 font-14 bold black">{{ translate('Store Name') }} <span
                                    class="text-danger"> *
                                </span></label>
                            <input type="text" id="store_name" name="store_name" class="theme-input-style"
                                placeholder="{{ translate('Enter Store Name') }}">
                        </div>
                        <div class="form-group mb-20">
                            <label for="package" class="mb-2 font-14 bold black">{{ translate('Package') }}<span
                                    class="text-danger"> *
                                </span></label>
                            <select class="theme-input-style package-selector" name="package_id">
                                <option>{{ translate('Select Package') }}</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-20">
                            <label for="plan" class="mb-2 font-14 bold black">{{ translate('Plan') }}<span
                                    class="text-danger"> *
                                </span></label>
                            <select class="theme-input-style" name="plan_id" id="plans">
                                <option value=""> {{ translate('Select Plan') }} </option>
                            </select>
                        </div>
                        <div class="form-group mb-20">
                            <label class="mb-2 font-14 bold black">{{ translate('Default Language') }}<span
                                    class="text-danger"> *
                                </span></label>
                            <select class="theme-input-style" name="default_language">
                                @foreach ($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-20">
                            <label class="mb-2 font-14 bold black">{{ translate('Default Currency') }}<span
                                    class="text-danger"> *
                                </span></label>
                            <select class="theme-input-style" name="default_currency">
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn long">
                                <span class="store-create-btn-label">{{ translate('Create') }}</span>
                                <div class="spinner">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Create Store-->
    </div>
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    <script>
        $(document).ready(function() {
            "use strict";
            //Store New Subscriber
            $("#subscriberAddForm").on('submit', function(e) {
                e.preventDefault();
                $('p.text-danger').remove();
                $.ajax({
                    url: '{{ route('plugin.saas.subscriber.store') }}',
                    type: 'POST',
                    data: $("#subscriberAddForm").serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ translate('Subscriber Added successful!') }}');
                            location.reload()
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key +
                                    '"], select[name="' + key + '"]');
                                inputField.after('<p class="text-danger">' + value[0] +
                                    '</p>');
                            });
                        } else {
                            toastr.error("{{ translate('Subscriber create failed!') }}");
                        }
                    }
                });
            });

            //Load edit subscriber modal
            $(".edit-subscriber").on('click', function(e) {
                e.preventDefault();
                $("#editSubscriber").modal("show");
                $('.edit-form-container').html('<p>Loading....</p>');
                let id = $(this).data("id");
                $.ajax({
                    type: 'POST',
                    url: '{{ route('plugin.saas.subscriber.edit') }}',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.edit-form-container').html(response.html);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error(
                            "{{ translate('Something went wrong. Please try again') }}");
                        $('.edit-form-container').html(
                            '<p>Something went wrong. Please try again</p>');
                    }

                });
            });

            //Update Subscriber
            $(document).on('submit', "#subscriberEditForm", function(e) {
                e.preventDefault();
                $('p.text-danger').remove();
                $.ajax({
                    url: '{{ route('plugin.saas.subscriber.update') }}',
                    type: 'POST',
                    data: $("#subscriberEditForm").serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ translate('Subscriber Updated successfully') }}');
                            location.reload()
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key +
                                    '"], select[name="' + key + '"]');
                                inputField.after('<p class="text-danger">' + value[0] +
                                    '</p>');
                            });
                        } else {
                            toastr.error("{{ translate('Subscriber update failed!') }}");
                        }
                    }
                });
            });

            //Load delete modal
            $(".delete-subscriber").on("click", function(e) {
                e.preventDefault();
                let id = $(this).data("id");
                $("#subscriber_id_to_delete").val(id);
                $('#delete-modal').modal('show');
            });

            //Will delete subscriber
            $("#subscriber-delete-btn").on('click', function(e) {
                $(".subscriber-delete-btn-label").html('');
                $('.spinner').addClass("lds-ellipsis");
            });

            //Will load store create modal
            $(".create-new-store").on('click', function(e) {
                e.preventDefault();
                let id = $(this).data("id");
                let name = $(this).data("name");
                $("#subscriber_id").val(id);
                $(".subscriber_name").val(name);
                $("#createStore").modal("show");
            });

            //Will load package plans options
            $(".package-selector").on("change", function(e) {
                e.preventDefault();
                let selected_package = $(this).val();
                $.ajax({
                    url: '{{ route('plugin.saas.get.plans.according.to.package') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        package_id: selected_package
                    },
                    success: function(response) {
                        if (response.success) {
                            let is_redeem_coupon = $('#is_redeem_coupon').val()
                            let lifetime_plan = '{{ config('saas.plans.lifetime') }}'

                            let plans = response.plans

                            let html = ``

                            for (let i = 0; i < plans.length; i++) {
                                html = html + `<option value='` + plans[i]['id'] + `'>` + plans[
                                        i]['name'] +
                                    `</option>`
                            }
                            $('#plans').html(html)

                            if (is_redeem_coupon == '1') {
                                $('#plans').html(`<option value="` + lifetime_plan +
                                    `" selected> {{ translate('Lifetime Plan') }} </option>`
                                )
                                $('#plans').prop('disabled', true);
                            } else {
                                if (html == '') {
                                    $('#plans').html(
                                        `<option value="-1"> {{ translate('Select Plan') }} </option>`
                                    )
                                }
                            }
                        } else {
                            toastr.error(
                                "{{ translate('No plan found with this package !') }}");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("{{ translate('No plan found with this package !') }}");
                    }
                });
            });

            //Will create a new store
            $("#new-store-form").on('submit', function(e) {
                e.preventDefault();
                $(".store-create-btn-label").html('');
                $('.spinner').addClass("lds-ellipsis");
                $('p.text-danger').remove();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('plugin.saas.store.create') }}',
                    data: $("#new-store-form").serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ translate('New Store Created Successfully') }}');
                            location.reload()
                        } else {
                            toastr.error(response.message);
                            $(".store-create-btn-label").html('Create');
                            $('.spinner').removeClass("lds-ellipsis");
                        }
                    },
                    error: function(xhr, status, error) {
                        $(".store-create-btn-label").html('Create');
                        $('.spinner').removeClass("lds-ellipsis");
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputField = $('input[name="' + key +
                                    '"], select[name="' + key + '"]');
                                var message = '<p class="text-danger">' + value[0] +
                                    '</p>';
                                inputField.after(message);
                            });
                        } else {
                            toastr.error("{{ translate('New Store Create failed!') }}");
                        }
                    }

                });

            });

        });
    </script>
@endsection
