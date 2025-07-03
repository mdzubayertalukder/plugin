@extends('core::base.layouts.master')
@section('title')
    {{ translate('New Brand') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="mb-3">
                <p class="alert alert-info">You are inserting <strong>"{{ getLanguageNameByCode(getDefaultLang()) }}"</strong> version</p>
            </div>
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New Brand') }}</h4>
                <form action="{{ route('plugin.tlcommercecore.product.brand.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="theme-input-style brand_name"
                                value="{{ old('name') }}" placeholder="{{ translate('Type here') }}">
                            <input type="hidden" name="permalink" id="permalink_input_field">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <!---Permalink---->
                    <div
                        class="form-row mb-20 permalink-input-group d-none @if ($errors->has('permalink')) d-flex @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Permalink') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <a href="#">{{ url('') }}/brands/<span
                                    id="permalink">{{ old('permalink') }}</span><span
                                    class="btn custom-btn ml-1 permalink-edit-btn">{{ translate('Edit') }}</span></a>
                            @if ($errors->has('permalink'))
                                <div class="invalid-input">{{ $errors->first('permalink') }}</div>
                            @endif
                            <div class="permalink-editor d-none">
                                <input type="text" class="theme-input-style" id="permalink-updated-input"
                                    placeholder="{{ translate('Type here') }}">
                                <button type="button" class="btn long mt-2 btn-danger permalink-cancel-btn"
                                    data-dismiss="modal">{{ translate('Cancel') }}</button>
                                <button type="button"
                                    class="btn long mt-2 permalink-save-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </div>
                    <!---End Permalink---->
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black mb-0">{{ translate('Logo') }} </label>
                            <p>100x100</p>
                        </div>
                        <div class="col-sm-8">
                            @include('core::base.includes.media.media_input', [
                                'input' => 'logo',
                                'data' => old('logo'),
                            ])
                            @if ($errors->has('logo'))
                                <div class="invalid-input">{{ $errors->first('logo') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Meta Title') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="meta_title" class="theme-input-style"
                                value="{{ old('meta_title') }}" placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('meta_title'))
                                <div class="invalid-input">{{ $errors->first('meta_title') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Meta Image') }} </label>
                        </div>
                        <div class="col-sm-8">
                            @include('core::base.includes.media.media_input', [
                                'input' => 'meta_image',
                                'data' => old('meta_image'),
                            ])
                            @if ($errors->has('meta_image'))
                                <div class="invalid-input">{{ $errors->first('meta_image') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Meta Description') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <textarea name="meta_description" class="theme-input-style"> {{ old('meta_description') }}</textarea>
                            @if ($errors->has('meta_description'))
                                <div class="invalid-input">{{ $errors->first('meta_description') }}</div>
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
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            initDropzone()
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()
            });
            /*Generate permalink*/
            $('.brand_name').change(function(e) {
                e.preventDefault();
                let name = $('.brand_name').val();
                let permalink = string_to_slug(name);
                $('#permalink').html(permalink);
                $('#permalink_input_field').val(permalink);
                $('.permalink-input-group').removeClass("d-none");
                $('.permalink-editor').addClass("d-none");
                $('.permalink-edit-btn').removeClass("d-none");

            });
            /*edit permalink*/
            $('.permalink-edit-btn').on('click', function(e) {
                e.preventDefault();
                let permalink = $('#permalink').html();
                $('#permalink-updated-input').val(permalink);
                $('.permalink-edit-btn').addClass("d-none");
                $('.permalink-editor').removeClass("d-none");


            });
            /*Cancel permalink edit*/
            $('.permalink-cancel-btn').on('click', function(e) {
                e.preventDefault();
                $('#permalink-updated-input').val();
                $('.permalink-editor').addClass("d-none");
                $('.permalink-edit-btn').removeClass("d-none");

            });
            /*Update permalink*/
            $('.permalink-save-btn').on('click', function(e) {
                e.preventDefault();
                let input = $('#permalink-updated-input').val();
                let updated_permalnk = string_to_slug(input);
                $('#permalink_input_field').val(updated_permalnk);
                $('#permalink').html(updated_permalnk);
                $('.permalink-editor').addClass("d-none");
                $('.permalink-edit-btn').removeClass("d-none");

            });

        })(jQuery);
    </script>
@endsection
