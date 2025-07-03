<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="background-tab" data-toggle="tab" href="#background" role="tab"
            aria-controls="background" aria-selected="false">{{ translate('Background') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="button"
            aria-selected="false">{{ translate('Advanced') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">

    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', ['lang' => $lang, 'widget' => 'text_editor'])

        <!-- Editor  -->
        <div class="form-group translate-field">
            <label class="col-12 font-14 bold black">{{ translate('Editor') }}</label>
            <div class="col-12">
                <div class="editor-wrap">
                    <textarea name="text_content_t_" id="text_editor">{{ isset($widget_properties['text_content_t_']) ? fix_image_urls($widget_properties['text_content_t_']) : '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Alignment  -->
        <div class="form-group mt-3">
            <label for="alignment" class="d-block mb-2 font-14 bold black">{{ translate('Text Alignment') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'left' ? 'active' : '' }}">
                    <input type="radio" class="d-none" name="alignment" id="left" value="left"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'left')>
                    {{ translate('Left') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="center" value="center"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center')>
                    {{ translate('Center') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'right' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="right" value="right"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'right')>
                    {{ translate('Right') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'justify' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="justify" value="justify"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'justify')>
                    {{ translate('Justify') }}
                </label>
            </div>
        </div>
    </div>

    <!-- Include background Properties -->
    @include('plugin/pagebuilder::page-builder.properties.background-properties', [
        'properties' => $widget_properties,
    ])

    <!-- Include Advance Properties -->
    @include('plugin/pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
<script>
    (function($) {
        'use strict'

        // SUMMERNOTE INIT
        $('#text_editor').summernote({
            tabsize: 2,
            height: 180,
            codeviewIframeFilter: false,
            codeviewFilter: true,
            codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
            callbacks: {
                onImageUpload: function(images, editor, welEditable) {
                    uploadeImage(images[0], editor, welEditable);
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


        // Upload Image For Summernote
        function uploadeImage(image, editor, welEditable) {
            
            let imageUploadUrl = '{{ route('plugin.builder.pageSection.text-editor.upload') }}';
            let data = new FormData();
            data.append("image", image);

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
                        $('#text_editor').summernote("insertNode", image[0]);
                    } else {
                        toastr.error(data.error, "Error!");
                    }
                },
                error: function(data) {
                    toastr.error('Image Upload Failed', "Error!");
                }
            });
        }

    })(jQuery);
</script>
