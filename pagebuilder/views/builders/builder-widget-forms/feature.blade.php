<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="style-tab" data-toggle="tab" href="#style" role="tab" aria-controls="style"
            aria-selected="true">{{ translate('Style') }}</a>
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
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'feature',
        ])

        <!-- Feature Image -->
        <div class="form-group mb-20">
            <label class="font-14 bold black">{{ translate('Feature Image') }} </label>
            @include('core::base.includes.media.media_input', [
                'input' => 'feature_image',
                'data' => isset($widget_properties['feature_image']) ? $widget_properties['feature_image'] : null,
            ])
        </div>

        <!-- Editor  -->
        <div class="form-group translate-field">
            <label class="col-12 font-14 bold black">{{ translate('Feature Description') }}</label>
            <div class="col-12">
                <div class="editor-wrap">
                    <textarea name="feature_description_t_" id="feature_description">{{ isset($widget_properties['feature_description_t_']) ? fix_image_urls($widget_properties['feature_description_t_']) : '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Link -->
        <div class="form-group mb-3">
            <label for="feature_url" class="font-14 bold black">{{ translate('Link') }}</label>
            <div class="mt-1 mb-3">
                <input type="text" name="feature_url" id="feature_url" class="form-control"
                    placeholder="{{ translate('Feature Url') }}"
                    value="{{ isset($widget_properties['feature_url']) ? $widget_properties['feature_url'] : '' }}">
            </div>
            <label for="new_window">
                <input type="checkbox" name="new_window" id="new_window" @checked(isset($widget_properties['new_window']) && $widget_properties['new_window'] == '1') value="1">
                {{ translate('Open in new window') }}
            </label>
        </div>
    </div>
    <!-- /Content Properties -->

    <!-- Style properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Title Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Width') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="width_c_" class="width-input form-control style--two"
                        value="{{ isset($widget_properties['width_c_']) ? $widget_properties['width_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Height') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="height_c_" class="height-input form-control style--two"
                        value="{{ isset($widget_properties['height_c_']) ? $widget_properties['height_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
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

        // Checked Button
        let heading_alignment = $('input[name="heading_alignment"]:checked');
        heading_alignment.parent().addClass('active');

        // SUMMERNOTE INIT
        $('#feature_description').summernote({
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
