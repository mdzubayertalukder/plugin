@extends('core::base.layouts.master')
@section('title')
    {{ translate('New Deal') }}
@endsection
@section('main_content')
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="mb-3">
                <p class="alert alert-info">You are inserting <strong>"{{ getLanguageNameByCode(getDefaultLang()) }}"</strong> version</p>
            </div>
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New Deal') }}</h4>
                <form action="{{ route('plugin.flashdeal.store.new') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Title') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="title" class="theme-input-style deal_title"
                                value="{{ old('title') }}" placeholder="{{ translate('Type title') }}">
                            <input type="hidden" name="permalink" id="permalink_input_field">
                            @if ($errors->has('title'))
                                <div class="invalid-input">{{ $errors->first('title') }}</div>
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
                            <a href="#">{{ url('') }}/<span id="permalink">{{ old('permalink') }}</span><span
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
                            <label class="font-14 bold black">{{ translate('Background Color') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="background_color" class="color-input form-control style--two"
                                    placeholder="#fffff" value="#FFFFF">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="#fffff" oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('background_color'))
                                <div class="invalid-input">{{ $errors->first('background_color') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Text Color') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="text_color" class="color-input form-control style--two"
                                    placeholder="#fffff" value="#FFFFF">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="#fffff" oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('text_color'))
                                <div class="invalid-input">{{ $errors->first('text_color') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Start date') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="datetime-local" name="start_date" class="theme-input-style"
                                value="{{ old('start_date') }}">
                            @if ($errors->has('start_date'))
                                <div class="invalid-input">{{ $errors->first('start_date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Expiry date') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="datetime-local" name="expiry_date" class="theme-input-style"
                                value="{{ old('expiry_date') }}">
                            @if ($errors->has('expiry_date'))
                                <div class="invalid-input">{{ $errors->first('expiry_date') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Banner') }}</label>
                        </div>
                        <div class="col-sm-8">
                            @include('core::base.includes.media.media_input', [
                                'input' => 'banner',
                                'data' => old('banner'),
                            ])
                            @if ($errors->has('banner'))
                                <div class="invalid-input">{{ $errors->first('banner') }}</div>
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
            /**
             * 
             * Media Library
             * */
            initDropzone()
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()

            });

            /*Generate permalink*/
            $('.deal_title').change(function(e) {
                e.preventDefault();
                let name = $('.deal_title').val();
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

        //Select color
        function selectColor(e, color) {
            "use strict";
            let target = e.target;
            $(target).closest('.addon').find('.color-input').val(color);
        }
    </script>
@endsection
