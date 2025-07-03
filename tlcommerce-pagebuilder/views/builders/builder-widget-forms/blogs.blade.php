@php
    $blogCategories = \Core\Models\TlBlogCategory::where('is_publish', config('settings.general_status.active'))->get();
@endphp
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

    <!-- Content Proprties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        @include('plugin/tlcommerce-pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'blogs',
        ])

        <!-- Blog Type -->
        <div class="form-group mb-3">
            <label for="type" class="font-14 bold black">{{ translate('Blog Type') }}</label>
            <div class="mt-1">
                <select name="type" id="type" class="form-control">
                    <option value="latest" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'latest')>{{ translate('Latest Blog') }}</option>
                    <option value="featured" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'featured')>{{ translate('Featured Blog') }}</option>
                    <option value="category" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'category')>{{ translate('Category Wise') }}</option>
                </select>
            </div>
        </div>

        <!-- Blog Category For Category Wise -->
        <div
            class="form-group mb-20 category-options {{ isset($widget_properties['type']) && $widget_properties['type'] == 'category' ? '' : 'd-none' }} ">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Select Category') }}</label>
            </div>
            <div class="col-sm-12">
                <select class="theme-input-style" name="category">
                    @foreach ($blogCategories as $category)
                        <option value="{{ $category->id }}" @selected(isset($widget_properties['category']) && $widget_properties['category'] == $category->id)>{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('category'))
                    <div class="invalid-input">{{ $errors->first('category') }}</div>
                @endif
            </div>
        </div>

        <!-- Blog Column -->
        <div class="form-group mb-3">
            <label for="type" class="font-14 bold black">{{ translate('Blog Column') }}</label>
            <div class="mt-1" id="blog_colum_image_field">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_1" value="col-12"
                                @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-12')>
                            <label for="blog_colum_1">
                                <img src="/themes/tlcommerce/public/blog/images/layout/1column.png"
                                    title="Blog Column 1" alt="Blog Column 1" class="layout_img img_layout" style="min-width: 80px">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_2" value="col-6"
                                @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-sm-6')>
                            <label for="blog_colum_2">
                                <img src="/themes/tlcommerce/public/blog/images/layout/2column.png"
                                    title="Blog Column 2" alt="Blog Column 2" class="layout_img img_layout" style="min-width: 80px">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_3"
                                value="col-6 col-md-4" @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-sm-6 col-md-4')>
                            <label for="blog_colum_3">
                                <img src="/themes/tlcommerce/public/blog/images/layout/3column.png"
                                    title="Blog Column 3" alt="Blog Column 3" class="layout_img img_layout" style="min-width: 80px">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_4"
                                value="col-6 col-lg-3" @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-sm-6 col-md-3')>
                            <label for="blog_colum_4">
                                <img src="/themes/tlcommerce/public/blog/images/layout/3column.png"
                                    title="Blog Column 4" alt="Blog Column 4" class="layout_img img_layout" style="min-width: 80px">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blog Count -->
        <div class="form-group mb-3">
            <label for="count" class="font-14 bold black">{{ translate('Blog Count') }}</label>
            <div class="mt-1">
                <input type="number" min="1" step="1" required name="count" id="count"
                    class="form-control" placeholder="{{ translate('Blog Count') }}"
                    value="{{ isset($widget_properties['count']) ? $widget_properties['count'] : '' }}">
            </div>
        </div>

        <!-- Read More Text -->
        <div class="form-group mb-3 translate-field read-more-field">
            <label for="read_more" class="font-14 bold black">{{ translate('Read More Text') }}</label>
            <div class="mt-1">
                <input type="text" name="read_more_t_" id="read_more" class="form-control"
                    placeholder="{{ translate('Read More Text') }}"
                    value="{{ isset($widget_properties['read_more_t_']) ? $widget_properties['read_more_t_'] : '' }}">
            </div>
        </div>
    </div>

    <!-- Style Proprties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Title Color -->
        <div class="form-group mb-3">
            <label for="title_color" class="font-14 bold black">{{ translate('Title Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="title_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['title_color_c_']) ? $widget_properties['title_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['title_color_c_']) ? $widget_properties['title_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Title Hover Color -->
        <div class="form-group mb-3">
            <label for="title_hover_color" class="font-14 bold black">{{ translate('Title Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="title_hover_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['title_hover_color_c_']) ? $widget_properties['title_hover_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['title_hover_color_c_']) ? $widget_properties['title_hover_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Color -->
        <div class="form-group mb-3">
            <label for="button_color" class="font-14 bold black">{{ translate('Button Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="button_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['button_color_c_']) ? $widget_properties['button_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['button_color_c_']) ? $widget_properties['button_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Hover Color -->
        <div class="form-group mb-3">
            <label for="button_hover_color" class="font-14 bold black">{{ translate('Button Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="button_hover_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['button_hover_color_c_']) ? $widget_properties['button_hover_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['button_hover_color_c_']) ? $widget_properties['button_hover_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Include background Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.background-properties', [
        'properties' => $widget_properties,
    ])

    <!-- Include Advance Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
<script>
    (function($) {
        'use strict'

        //Category Wise Select
        $(document).on('change', '#type', function() {
            let type = $(this).find("option:selected").val();
            if (type === 'category') {
                $('.category-options').removeClass('d-none');
            } else {
                $('.category-options').addClass('d-none');
            }
        })

        let checked_image = $('input[name="blog_colum"]:checked').attr('id');
        $('label[for="' + checked_image + '"]').find('img').css({
            "border-color": "#0073aa"
        });

        // image click and set border color
        $(document).on('click', '#blog_colum_image_field .layout_img', function() {
            $('#blog_colum_image_field .layout_img').each(function(index, element) {
                $(this).css({
                    "border-color": "#d9d9d9",
                })
            });
            $(this).css({
                "border-color": "#0073aa"
            });
        });

    })(jQuery);
</script>
