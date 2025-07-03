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
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', ['lang' => $lang, 'widget' => 'blogs'])

        <!-- Blog Type -->
        <div class="form-group mb-3">
            <label for="type" class="font-14 bold black">{{ translate('Blog Type') }}</label>
            <div class="mt-1">
                <select name="type" id="type" class="form-control">
                    <option value="recent" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'recent')>{{ translate('Recent Blog') }}</option>
                    <option value="featured" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'featured')>{{ translate('Featured Blog') }}</option>
                    <option value="comment" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'comment')>{{ translate('Most Commented Blog') }}</option>
                    <option value="view" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'view')>{{ translate('Most Viewed Blog') }}</option>
                    <option value="popular" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'popular')>{{ translate('Popular Blog') }}</option>
                    <option value="category" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'category')>{{ translate('Category Wise') }}</option>
                </select>
            </div>
        </div>

        <!-- Blog Category For Category Wise -->
        <div class="form-group mb-20 category-options {{ isset($widget_properties['type']) && $widget_properties['type'] == 'category' ? '' : 'd-none' }} ">
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

        <!-- Blog Post Style -->
        <div class="form-group mb-3">
            <label for="post_style" class="font-14 bold black">{{ translate('Blog Post Style') }}</label>
            <div class="mt-1">
                <select name="post_style" id="post_style" class="form-control">
                    <option value="s_one" @selected(isset($widget_properties['post_style']) && $widget_properties['post_style'] == 's_one')>{{ translate('Style 1') }}</option>
                    <option value="s_two" @selected(isset($widget_properties['post_style']) && $widget_properties['post_style'] == 's_two')>{{ translate('Style 2') }}</option>
                    <option value="s_three" @selected(isset($widget_properties['post_style']) && $widget_properties['post_style'] == 's_three')>{{ translate('Style 3') }}
                    </option>
                    <option value="s_four" @selected(isset($widget_properties['post_style']) && $widget_properties['post_style'] == 's_four')>{{ translate('Style 4') }}</option>
                    <option value="s_five" @selected(isset($widget_properties['post_style']) && $widget_properties['post_style'] == 's_five')>{{ translate('Style 5') }}</option>
                </select>
            </div>
            <div class="img-box mt-3 d-none text-right">
                <button type="button" class="mb-1 bg-light-blue remove-img">x</button>
                <img src="" alt="preview post style">
            </div>
        </div>

        <!-- Blog Column -->
        <div class="form-group mb-3">
            <label for="type" class="font-14 bold black">{{ translate('Blog Column') }}</label>
            <div class="mt-1" id="blog_colum_image_field">
                <div class="row">
                    <div class="col-4">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_1" value="col-12"
                                @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-12')>
                            <label for="blog_colum_1">
                                <img src="{{ asset('themes/default/public/assets/images/layout/1column.png') }}"
                                    title="Blog Column 1" alt="Blog Column 1" class="layout_img img_layout">
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_2"
                                value="col-sm-6" @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-sm-6')>
                            <label for="blog_colum_2">
                                <img src="{{ asset('themes/default/public/assets/images/layout/2column.png') }}"
                                    title="Blog Column 2" alt="Blog Column 2" class="layout_img img_layout">
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <input type="radio" class="d-none" name="blog_colum" id="blog_colum_3"
                                value="col-sm-6 col-md-4" @checked(isset($widget_properties['blog_colum']) && $widget_properties['blog_colum'] == 'col-sm-6 col-md-4')>
                            <label for="blog_colum_3">
                                <img src="{{ asset('themes/default/public/assets/images/layout/3column.png') }}"
                                    title="Blog Column 3" alt="Blog Column 3" class="layout_img img_layout">
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
                <input type="number" min="1" step="1" required name="count" id="count" class="form-control"
                    placeholder="{{ translate('Blog Count') }}"
                    value="{{ isset($widget_properties['count']) ? $widget_properties['count'] : '' }}">
            </div>
        </div>

        <!-- Read More Text -->
        <div class="form-group mb-3 translate-field {{ isset($widget_properties['post_style']) && $widget_properties['post_style'] != 's_one' ? 'd-none' : '' }} read-more-field">
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

        <!-- Description Color -->
        <div class="form-group mb-3">
            <label for="description_color" class="font-14 bold black">{{ translate('Description Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="description_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['description_color_c_']) ? $widget_properties['description_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['description_color_c_']) ? $widget_properties['description_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Post Background Color -->
        <div class="form-group mb-3">
            <label for="post_background_color"
                class="font-14 bold black">{{ translate('Post Background Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="post_background_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['post_background_color_c_']) ? $widget_properties['post_background_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['post_background_color_c_']) ? $widget_properties['post_background_color_c_'] : '#000000' }}">
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

        //Category Wise Select
        $(document).on('change', '#type', function() {
            let type = $(this).find("option:selected").val();
            if (type === 'category') {
                $('.category-options').removeClass('d-none');
            } else {
                $('.category-options').addClass('d-none');
            }
        })

        // Post Style Preview Image
        $(document).on('change', '#post_style', function() {
            let selected_style = $(this).find("option:selected").val();
            let image_source = "{{ asset('themes/default/public/assets/images/layout/variation-') }}" +
                selected_style + ".png";

            $('.img-box img').attr('src', image_source);
            $('.img-box').removeClass('d-none');

            if (selected_style == 's_one' && $('.read-more-field').hasClass('d-none')) {
                $('.read-more-field').removeClass('d-none');
            } else {
                $('.read-more-field').addClass('d-none');
            }
        });

        // Remove Img
        $(document).on('click', '.remove-img', function() {
            $('.img-box').addClass('d-none');
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
