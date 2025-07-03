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

    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
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
        <div
            class="form-row mb-20 category-options {{ isset($widget_properties['type']) && $widget_properties['type'] == 'category' ? '' : 'd-none' }} ">
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
    </div>

    <!-- Style Properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Category Color -->
        <div class="form-group mb-3">
            <label for="category_color" class="font-14 bold black">{{ translate('Category Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="category_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['category_color_c_']) ? $widget_properties['category_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['category_color_c_']) ? $widget_properties['category_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Category Hover Color -->
        <div class="form-group mb-3">
            <label for="category_hover_color" class="font-14 bold black">{{ translate('Category Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="category_hover_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['category_hover_color_c_']) ? $widget_properties['category_hover_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['category_hover_color_c_']) ? $widget_properties['category_hover_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>
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

    })(jQuery);
</script>
