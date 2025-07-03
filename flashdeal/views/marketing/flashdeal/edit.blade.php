@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Deal') }}
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Edit Deal') }}</h4>

    </div>
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="row">
                <div class="col-12 mb-3">
                    <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version
                    </p>
                </div>
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill border-light border-0">
                        @foreach ($languages as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                    href="{{ route('plugin.flashdeal.edit', ['id' => $deal_details->id, 'lang' => $language->code]) }}">
                                    <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}"
                                        width="20px">
                                    <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="form-element py-30 mb-30">
                <form action="{{ route('plugin.flashdeal.update') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Title') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="title"
                                class="theme-input-style @if (!empty($lang) && $lang == getdefaultlang()) deal_title @endif"
                                value="{{ $deal_details->translation('title', $lang) }}"
                                placeholder="{{ translate('Type title') }}">
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <input type="hidden" name="id" value="{{ $deal_details->id }}">
                            <input type="hidden" name="permalink" id="permalink_input_field"
                                value="{{ $deal_details->permalink }}">
                            @if ($errors->has('title'))
                                <div class="invalid-input">{{ $errors->first('title') }}</div>
                            @endif
                        </div>
                    </div>
                    <!---Permalink---->
                    <div
                        class="form-row mb-20 permalink-input-group @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Permalink') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <a href="#">{{ url('') }}/deals/<span
                                    id="permalink">{{ $deal_details->permalink }}</span><span
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
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Background Color') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="background_color" class="color-input form-control style--two"
                                    placeholder="#fffff" value="{{ $deal_details->background_color }}">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="{{ $deal_details->background_color }}"
                                        oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('background_color'))
                                <div class="invalid-input">{{ $errors->first('background_color') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Text Color') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="text_color" class="color-input form-control style--two"
                                    placeholder="#fffff" value="{{ $deal_details->text_color }}">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="{{ $deal_details->text_color }}"
                                        oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('text_color'))
                                <div class="invalid-input">{{ $errors->first('text_color') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Start date') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="datetime-local" name="start_date" class="theme-input-style"
                                value="{{ $deal_details->start_date }}">
                            @if ($errors->has('start_date'))
                                <div class="invalid-input">{{ $errors->first('start_date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Expiry date') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="datetime-local" name="expiry_date" class="theme-input-style"
                                value="{{ $deal_details->end_date }}">
                            @if ($errors->has('expiry_date'))
                                <div class="invalid-input">{{ $errors->first('expiry_date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Banner') }}</label>
                        </div>
                        <div class="col-sm-8">
                            @include('core::base.includes.media.media_input', [
                                'input' => 'banner',
                                'data' => $deal_details->background_image,
                            ])
                            @if ($errors->has('banner'))
                                <div class="invalid-input">{{ $errors->first('banner') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Update') }}</button>
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

        //Color select
        function selectColor(e, color) {
            "use strict";
            let target = e.target;
            $(target).closest('.addon').find('.color-input').val(color);
        }
    </script>
@endsection
