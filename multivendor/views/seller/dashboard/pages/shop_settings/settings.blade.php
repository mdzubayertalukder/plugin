@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Shop Settings') }}
@endsection
@section('custom_css')
@endsection
@section('seller_main_content')
    @if (auth()->user()->shop->status == config('settings.general_status.active'))
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-3 pb-3">
            <h4><i class="icofont-ui-settings"></i> {{ translate('Shop Settings') }}</h4>
        </div>
        <div class="row">
            <!--Shop Basic settings-->
            <div class="col-lg-12">
                <form method="POST" action="{{ route('plugin.multivendor.seller.dashboard.shop.settings.update') }}">
                    @csrf
                    <div class="card mb-30">
                        <div class="card-header bg-white border-bottom2">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h4 class="py-2">{{ translate('Basic Information') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Name') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="hidden" name="id" value="{{ $basic_settings->id }}">
                                    <input type="text" name="shop_name" class="theme-input-style"
                                        placeholder="{{ translate('Enter Shop Name') }}"
                                        value="{{ $basic_settings->shop_name }}" required>
                                    @if ($errors->has('shop_name'))
                                        <div class="invalid-input">{{ $errors->first('shop_name') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Link') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <a href="{{ url('/shop') }}/{{ $basic_settings->shop_slug }}"
                                        target="_blank">{{ url('') }}/shop/<span
                                            id="permalink">{{ $basic_settings->shop_slug }}</span>
                                        <span class="btn custom-btn ml-1 permalink-edit-btn">
                                            {{ translate('Edit') }}
                                        </span>
                                    </a>
                                    <input type="hidden" name="shop_slug" id="permalink_input_field"
                                        value="{{ $basic_settings->shop_slug }}" required>
                                    <div class="permalink-editor d-none">
                                        <input type="text" class="theme-input-style" id="permalink-updated-input"
                                            placeholder="{{ translate('Type here') }}">
                                        <button type="button" class="btn long mt-2 btn-danger permalink-cancel-btn"
                                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                                        <button type="button"
                                            class="btn long mt-2 permalink-save-btn">{{ translate('Save') }}</button>
                                    </div>

                                    @if ($errors->has('shop_slug'))
                                        <div class="invalid-input">{{ $errors->first('shop_slug') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Logo') }} </label>
                                </div>
                                <div class="col-md-8">
                                    @include('core::base.includes.media.media_input', [
                                        'input' => 'shop_logo',
                                        'data' => $basic_settings->logo,
                                        'user_filter' => true,
                                    ])
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Banner') }} </label>
                                </div>
                                <div class="col-md-8">
                                    @include('core::base.includes.media.media_input', [
                                        'input' => 'shop_banner',
                                        'data' => $basic_settings->shop_banner,
                                        'user_filter' => true,
                                    ])
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Phone') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="shop_phone" class="theme-input-style"
                                        placeholder="{{ translate('Enter Shop Phone') }}"
                                        value="{{ $basic_settings->shop_phone }}" required>
                                    @if ($errors->has('shop_phone'))
                                        <div class="invalid-input">{{ $errors->first('shop_phone') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Shop Address') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="shop_address" class="theme-input-style"
                                        placeholder="{{ translate('Enter Shop Address') }}"
                                        value="{{ $basic_settings->shop_address }}">
                                    @if ($errors->has('shop_address'))
                                        <div class="invalid-input">{{ $errors->first('shop_address') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Save Change') }}</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <!--End Shop Besic settings-->
            <!--Shop Seo settings-->
            <div class="col-lg-12">
                <form method="POST" action="{{ route('plugin.multivendor.seller.dashboard.shop.seo.settings.update') }}">
                    @csrf
                    <div class="card mb-30">
                        <div class="card-header bg-white border-bottom2">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h4 class="py-2">{{ translate('Seo Information') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Meta Title') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="hidden" name="id" value="{{ $basic_settings->id }}">
                                    <input type="text" name="meta_title" class="theme-input-style"
                                        placeholder="{{ translate('Enter Meta Title') }}"
                                        value="{{ $basic_settings->meta_title }}" required>
                                    @if ($errors->has('meta_title'))
                                        <div class="invalid-input">{{ $errors->first('meta_title') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Meta Image') }} </label>
                                </div>
                                <div class="col-md-8">
                                    @include('core::base.includes.media.media_input', [
                                        'input' => 'meta_image',
                                        'data' => $basic_settings->meta_image,
                                        'user_filter' => true,
                                    ])
                                </div>
                            </div>


                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Meta Description') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea name="meta_description" class="theme-input-style">{{ $basic_settings->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <div class="invalid-input">{{ $errors->first('meta_description') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Save Change') }}</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <!--End Shop Seo settings-->
        </div>
    @else
        <p class="alert alert-info">Your Shop is Inactive. Please contact with Administration </p>
    @endif
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            initDropzone();
            /*edit permalink*/
            $(".permalink-edit-btn").on("click", function(e) {
                e.preventDefault();
                let permalink = $("#permalink").html();
                $("#permalink-updated-input").val(permalink);
                $(".permalink-edit-btn").addClass("d-none");
                $(".permalink-editor").removeClass("d-none");
            });
            /*Cancel permalink edit*/
            $(".permalink-cancel-btn").on("click", function(e) {
                e.preventDefault();
                $("#permalink-updated-input").val();
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*Update permalink*/
            $(".permalink-save-btn").on("click", function(e) {
                e.preventDefault();
                let input = $("#permalink-updated-input").val();
                let updated_permalink = string_to_slug(input);
                $("#permalink_input_field").val(updated_permalink);
                $("#permalink").html(updated_permalink);
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });

        })(jQuery);

    </script>
@endsection
