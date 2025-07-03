@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ $ticket->subject }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
    <!--Editor-->
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <!--End editor-->
    <style>
        .owner {
            background-color: transparent !important;
        }

        .owner .owner-text {
            background-color: white;
            padding: 20px;
            border-radius: 0 20px 0 20px;
        }

        .replier {
            background-color: transparent !important;
        }

        .replier .replier-text {
            background-color: white;
            padding: 20px;
            border-radius: 20px 0 20px 0;
        }

        .chat-box {
            background-color: #f8f8f8;
        }

        .ticket-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ticket-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-item:hover {
            background-color: #f5f5f5;
        }

        .ticket-info {
            flex-grow: 1;
        }

        .ticket-info h4 {
            margin-bottom: 5px;
            font-size: 16px;
        }

        .ticket-info p {
            margin-bottom: 0;
        }

        .ticket-actions {
            text-align: right;
        }

        .ticket-actions a {
            color: #999;
            margin-left: 10px;
        }

        .ticket-actions a:hover {
            color: #333;
        }

        .replier-text img {
            max-width: 50%;
        }

        .owner-text img {
            max-width: 50%;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-30">
                <div class="card-header bg-white p-3">
                    <h4 class="font-20">{{ $ticket->subject }}</h4>
                </div>
                <div class="card-body">
                    <div class="row p-3">
                        <div>
                            {!! $ticket->details !!}
                        </div>
                    </div>
                    @php
                        $attachments = !empty($ticket->attachment) ? explode(',', $ticket->attachment) : [];
                    @endphp
                    @if (sizeof($attachments) > 0)
                        <div class="row pt-3">
                            <div class="col-md-12">
                                <p class="black bold fz-14 mb-0">{{ translate('File Attached') }}</p>
                                <ul class="list-unstyled">
                                    @foreach ($attachments as $key => $item)
                                        <li>
                                            <a href="{{ asset(getFilePath($item)) }}" class="link fz-12" target="_blank">
                                                <i class="icofont-download"></i>
                                                <b>{{ translate('Attachment ') . ' ' . $key + 1 }}</b>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 chat-box">
                @foreach ($ticket_replies as $reply)
                    @if ($reply->replied_by != auth()->user()->id)
                        <div class="card owner">
                            <div class="card-body">
                                <div class="row owner-text">
                                    <div class="col-md-3 owner-img text-center border-right">
                                        <img src="{{ asset(getFilePath($reply->repliedBy->image)) }}"
                                            alt="{{ auth()->user()->name }}" class="img-50">
                                        <div class="pt-1 fz-12">
                                            <span>{{ $reply->repliedBy->name }}</span><br>
                                            <span>{{ $reply->replied_at }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        {!! $reply->details !!}
                                        @php
                                            $attachments = !empty($reply->attachment)
                                                ? explode(',', $reply->attachment)
                                                : [];
                                        @endphp
                                        @if (sizeof($attachments) > 0)
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                    <p class="black bold fz-14 mb-0">{{ translate('File Attached') }}</p>
                                                    <ul class="list-unstyled">
                                                        @foreach ($attachments as $key => $item)
                                                            <li>
                                                                <a href="{{ asset(getFilePath($item)) }}"
                                                                    class="link fz-12" target="_blank">
                                                                    <i class="icofont-download"></i>
                                                                    <b>{{ translate('Attachment ') . ' ' . $key + 1 }}</b>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card replier">
                            <div class="card-body">
                                <div class="row replier-text">
                                    <div class="col-md-9">
                                        {!! $reply->details !!}
                                        @php
                                            $attachments = !empty($reply->attachment)
                                                ? explode(',', $reply->attachment)
                                                : [];
                                        @endphp
                                        @if (sizeof($attachments) > 0)
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                    <p class="black bold fz-14 mb-0">{{ translate('File Attached') }}</p>
                                                    <ul class="list-unstyled">
                                                        @foreach ($attachments as $key => $item)
                                                            <li>
                                                                <a href="{{ asset(getFilePath($item)) }}"
                                                                    class="link fz-12" target="_blank">
                                                                    <i class="icofont-download"></i>
                                                                    <b>{{ translate('Attachment ') . ' ' . $key + 1 }}</b>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-right border-left text-center">
                                        <img src="{{ asset(getFilePath($reply->repliedBy->image)) }}"
                                            alt="{{ auth()->user()->name }}" class="img-50">
                                        <div class="pt-1 fz-12">
                                            <span>{{ $reply->repliedBy->name }}</span><br>
                                            <span>{{ $reply->replied_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            @if ($ticket->status == config('support-ticket.ticket_status.closed'))
                <div class="mt-30 mb-30 bg-danger-light d-flex justify-content-center">
                    <form action="{{ route('reply.support.ticket') }}" class="my-2" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $ticket->id }}">
                        <input type="hidden" name="details" value="Ticket reopened">
                        <input type="hidden" name="status" value="0">
                        {{ translate('The ticket is closed, do you want to reopen? ') }} <button type="submit"
                            class="btn btn-success sm">{{ translate('Reopen') }}</button>
                    </form>
                </div>
            @endif

            @if ($ticket->status != config('support-ticket.ticket_status.closed'))
                <div class="card mt-30">
                    <div class="card-header py-3 bg-white">
                        <h4 class="font-20">{{ translate('reply To ticket') }}</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" action="{{ route('reply.support.ticket') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $ticket->id }}">
                            <div class="row">
                                <div class="form-group col-md-12">
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

                                <div class="form-group col-md-12">
                                    <label class="font-14 bold black mb-2">{{ translate('Set Status') }}</label>
                                    <select class="theme-input-style" name="status" id="status">
                                        <option value="0" {{ $ticket->status == 0 ? 'selected' : '' }}>
                                            {{ translate('In Progress') }}</option>
                                        <option value="1" {{ $ticket->status == 1 ? 'selected' : '' }}>
                                            {{ translate('Closed') }}
                                        </option>
                                        <option value="2" {{ $ticket->status == 2 ? 'selected' : '' }}>
                                            {{ translate('Action Required') }}</option>
                                        <option value="3" {{ $ticket->status == 3 ? 'selected' : '' }}>
                                            {{ translate('Investigeting') }}</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <p class="text-danger">{{ $errors->first('status') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="font-14 bold black mb-2">{{ translate('Attach file') }}</label>
                                    <input type="file" name="ticket_file[]" multiple class="form-control-file">
                                    @if ($errors->has('ticket_file'))
                                        <div class="invalid-input">{{ $errors->first('ticket_file') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-12">
                                    <button class="btn long">{{ translate('Reply') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-30">
                <div class="card-header bg-white py-3">
                    <h4 class="font-20">{{ translate('General Information') }}</h4>
                </div>
                <div class="card-body p-0">
                    <div class="row mx-0">
                        <ul class="ticket-list w-100">
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <h4>{{ translate('Ticket ID') }}: {{ $ticket->uuid }}</h4>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <p><span class="bold">{{ translate('Ticket Category') }}:</span>
                                        {{ $ticket->categoryDetails->name }}</p>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="align-items-center d-flex justify-content-between ticket-info">
                                    <p><span class="bold">{{ translate('Ticket Owner') }}:</span>
                                        {{ $ticket->createdBy->name }}</p>
                                    <button data-toggle="modal" data-target=".owner-ticket-list"
                                        class="btn bn-info sm ml-2">{{ translate('Other Tickets') }}</button>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="align-items-center d-flex justify-content-between ticket-info">
                                    <p><span class="bold">{{ translate('Assigned To') }}:</span>
                                        {{ !empty($ticket->assigned_to) ? $ticket->assignedTo->name : '' }}
                                    </p>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <p><span class="bold">{{ translate('Opened Time') }}:</span>
                                        {{ $ticket->created_at->format('M d Y h:i A') }}</p>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <p><span class="bold">{{ translate('Priority') }}:</span>
                                        <span class="{{ $ticket->priority_class() }}">
                                            {{ ucwords($ticket->priority) }}
                                        </span>
                                    </p>
                                </div>
                            </li>
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <p><span class="bold">{{ translate('Status') }}:</span>
                                        <span class="badge badge-info">
                                            {{ $ticket->status_name() }}
                                        </span>
                                    </p>
                                </div>
                            </li>
                            @if ($ticket->status != config('support-ticket.ticket_status.closed'))
                                <li class="ticket-item border-0">
                                    <div class="ticket-info text-center">
                                        <form action="{{ route('reply.support.ticket') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $ticket->id }}">
                                            <input type="hidden" name="details" value="Ticket closed">
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit"
                                                class="btn btn-danger sm">{{ translate('Close Ticket') }}</button>
                                        </form>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4 class="font-20">{{ translate('Ticket History') }}</h4>
                </div>
                <div class="card-body p-0">
                    <div class="row mx-0">
                        <ul class="ticket-list w-100">
                            @foreach ($ticket_replies as $reply)
                                <li class="ticket-item">
                                    <div class="ticket-info">
                                        <span>
                                            <span class="bold">{{ translate('Replied By') }}</span>
                                            <span class="text-primary">{{ $reply->repliedBy->name }}</span>
                                        </span>
                                        <div class="d-flex justify-content-between mt-1">
                                            <span class="fz-14">{{ translate('on') }}
                                                {{ $reply->replied_at->format('M d Y H:i:s A') }}</span><br>
                                            <span class="fz-14"> {{ translate('status:') }}
                                                <span class="badge badge-info">{{ $reply->ticket_status_name() }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            <li class="ticket-item">
                                <div class="ticket-info">
                                    <span class="bold">{{ translate('Ticket Opened') }}</span><br>
                                    <span class="fz-14">{{ translate('on') }}
                                        {{ $ticket->created_at->format('M d Y H:i:s A') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade owner-ticket-list" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="card mb-30">
                    <div class="d-sm-flex justify-content-between align-items-center card-header bg-white py-3">
                        <h4 class="font-20">{{ translate('All Tickets') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ translate('Subject') }}</th>
                                    <th scope="col">{{ translate('category') }}</th>
                                    <th scope="col">{{ translate('Status') }}</th>
                                    <th scope="col">{{ translate('Last Reply') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($all_other_tickets as $key => $other_ticket)
                                    @php
                                        $status_name = '';
                                        $status_class = '';
                                        $ticket_status = config('support-ticket.ticket_status');
                                        foreach ($ticket_status as $name => $status) {
                                            if ($status == $other_ticket->status) {
                                                $status_name = strToUpper(implode(' ', explode('_', $name)));
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td scope="row">{{ $key + 1 }}.</td>
                                        <td>{{ $other_ticket->subject }}</td>
                                        <td>{{ $other_ticket->categoryDetails->name }}</td>
                                        <td>{{ $status_name }}</td>
                                        @if (!empty($ticket->lastReply()))
                                            <td>{{ $ticket->lastReply()->replied_at }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

            $('#status').select2({
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
