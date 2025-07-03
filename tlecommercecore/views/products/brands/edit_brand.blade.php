@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Brand') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Edit Brand') }}</h4>

    </div>
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="row">
                <div class="col-12 mb-3">
                    <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version</p>
                </div>
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill border-light border-0">
                        @foreach ($languages as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                    href="{{ route('plugin.tlcommercecore.product.brand.edit', ['id' => $brand_details->id, 'lang' => $language->code]) }}">
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
                <form action="{{ route('plugin.tlcommercecore.product.brand.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" name="id" value="{{ $brand_details->id }}">
                            <input type="hidden" name="permalink" id="permalink_input_field"
                                value="{{ $brand_details->permalink }}">
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <input type="text" name="name"
                                class="theme-input-style @if (!empty($lang) && $lang == getdefaultlang()) brand_name @endif"
                                value="{{ $brand_details->translation('name', $lang) }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="@if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <!---Permalink---->
                        <div class="form-row mb-20 permalink-input-group">
                            <div class="col-sm-4">
                                <label class="font-14 bold black">{{ translate('Permalink') }} </label>
                            </div>
                            <div class="col-sm-8">
                                <a href="#">{{ url('') }}/brand/<span
                                        id="permalink">{{ $brand_details->permalink }}</span><span
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
                                    'data' => $brand_details->logo,
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
                                    value="{{ $brand_details->meta_title }}" placeholder="{{ translate('Type here') }}">
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
                                    'data' => $brand_details->meta_image,
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
                                <textarea name="meta_description" class="theme-input-style"> {{ $brand_details->meta_description }}</textarea>
                                @if ($errors->has('meta_description'))
                                    <div class="invalid-input">{{ $errors->first('meta_description') }}</div>
                                @endif
                            </div>
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
