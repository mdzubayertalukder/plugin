@php
    $categories = getAllTicketCategories();
    $staffs = getAllStaffs();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Create Ticket') }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
    <!--Editor-->
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <!--End editor-->
@endsection
@section('main_content')
    <!-- Main Content -->
    <form class="form-horizontal" id="create_package" action="{{ route('store.support.ticket') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                {{-- Create Ticket --}}
                <div class="card mb-30">
                    <div class="card-header bg-white py-3">
                        <h4 class="font-20">{{ translate('Open New Ticket') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-20 col-12">
                                <label for="name" class="font-14 bold black mb-2">{{ translate('Subject') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" class="theme-input-style"
                                    value="{{ old('Subject') }}" placeholder="{{ translate('Subject') }}">
                                @if ($errors->has('subject'))
                                    <p class="text-danger">{{ $errors->first('subject') }}</p>
                                @endif
                            </div>

                            <div class="form-group mb-20 col-12">
                                <label class="font-14 bold black mb-2">{{ translate('Priority') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="theme-input-style" id="priority" name="priority">
                                    <option value=""></option>
                                    <option value="high">{{ translate('High') }}</option>
                                    <option value="urgent">{{ translate('Urgent') }}</option>
                                    <option value="medium">{{ translate('Medium') }}</option>
                                    <option value="low">{{ translate('Low') }}</option>
                                </select>
                                @if ($errors->has('priority'))
                                    <p class="text-danger">{{ $errors->first('priority') }}</p>
                                @endif
                            </div>

                            <div class="form-group mb-20 col-12">
                                <label class="font-14 bold black mb-2">{{ translate('Category') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="theme-input-style" name="category" id="category">
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('category'))
                                    <p class="text-danger">{{ $errors->first('category') }}</p>
                                @endif
                            </div>
                            <div class="form-group mb-20 col-12">
                                <label class="font-14 bold black mb-2">{{ translate('Assign User') }}</label>
                                <select class="theme-input-style" name="user" id="user">
                                    <option value=""></option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('user'))
                                    <p class="text-danger">{{ $errors->first('user') }}</p>
                                @endif
                            </div>
                            <div class="form-group mb-20 col-12">
                                <label class="font-14 bold black mb-2">{{ translate('Details') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="editor-wrap">
                                    <textarea name="details" id="details">{{ old('details') }}</textarea>
                                </div>
                                @if ($errors->has('details'))
                                    <p class="text-danger"> {{ $errors->first('details') }} </p>
                                @endif
                            </div>
                            <div class="form-group mb-20 col-12">
                                <label class="font-14 bold black mb-2">{{ translate('Attach  file') }}</label>
                                <input type="file" name="ticket_file[]" multiple class="form-control-file">
                                @if ($errors->has('ticket_file'))
                                    <div class="invalid-input">{{ $errors->first('ticket_file') }}</div>
                                @endif
                            </div>
                            <div class="form-group mb-20 col-12">
                                <button type="submit" class="btn long">
                                    {{ translate('Create Ticket') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Create Ticket --}}
            </div>
        </div>
    </form>
    <!-- End Main Content -->
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->
    <!--Editor-->
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <!--End Editor-->
    <script>
        initDropzone()

        $(document).ready(function() {
            'use strict'

            $('#priority').select2({
                theme: "classic",
                placeholder: `{{ translate('No Option Selected') }}`,
            });

            $('#category').select2({
                theme: "classic",
                placeholder: `{{ translate('No Option Selected') }}`,
            });

            $('#user').select2({
                theme: "classic",
                placeholder: `{{ translate('No Option Selected') }}`,
            });

            // SUMMERNOTE INIT
            $('#details').summernote({
                tabsize: 2,
                height: 200,
                codeviewIframeFilter: false,
                codeviewFilter: true,
                codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["table", ["table"]],
                    ["insert", ["link", "picture", "video"]],
                    ["view", ["fullscreen", "codeview", "help"]],
                ],
                placeholder: 'Ticket Details',
                callbacks: {
                    onImageUpload: function(images, editor, welEditable) {
                        sendFile(images[0], editor, welEditable);
                    },
                    onChangeCodeview: function(contents, $editable) {
                        let code = $(this).summernote('code')
                        code = code.replace(
                            /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                            '')
                        $(this).val(code)
                    }
                }
            });
        })


        // send file function summernote
        function sendFile(image, editor, welEditable) {
            "use strict";
            let imageUploadUrl = `{{ route('support.ticket.content.image') }}`;
            let data = new FormData();
            data.append("image", image);
            data.append("_token", '{{ csrf_token() }}');

            $.ajax({
                data: data,
                type: "POST",
                url: imageUploadUrl,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {

                    if (data.url) {
                        var image = $('<img>').attr('src', data.url);
                        $('#details').summernote("insertNode", image[0]);
                    } else {
                        toastr.error(data.error, "Error!");
                    }

                },
                error: function(data) {
                    toastr.error('Image Upload Failed', "Error!");
                }
            });
        }
    </script>
@endsection
