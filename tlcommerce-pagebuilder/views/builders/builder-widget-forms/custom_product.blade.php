@php
    $categories = \Plugin\TlcommerceCore\Models\ProductCategory::where('parent', null)
        ->where('status', config('settings.general_status.active'))
        ->orderBy('id', 'ASC')
        ->get();
@endphp
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

        <!-- Select Option -->
        <div class="form-group mb-3">
            <label for="collection" class="font-14 bold black">{{ translate('Select Option') }}</label>
            <div class="mt-1">
                <select class="theme-input-style custom-product-options" name="content" required>
                    <option value="new_arrival" @selected(isset($widget_properties['content']) && $widget_properties['content'] == 'new_arrival')>{{ translate('New Arrival') }}
                    </option>
                    <option value="featured" @selected(isset($widget_properties['content']) && $widget_properties['content'] == 'featured')>{{ translate('Featured Products') }}
                    </option>
                    <option value="top_selling" @selected(isset($widget_properties['content']) && $widget_properties['content'] == 'top_selling')>{{ translate('Top Selling') }}
                    </option>
                    <option value="top_reviewed" @selected(isset($widget_properties['content']) && $widget_properties['content'] == 'top_reviewed')>{{ translate('Top Reviewed') }}
                    </option>
                    <option value="category" @selected(isset($widget_properties['content']) && $widget_properties['content'] == 'category')>{{ translate('Category wise') }}</option>
                </select>
            </div>
        </div>

        <!-- Product Category -->
        <div
            class="form-row mb-20 category-options {{ isset($widget_properties['content']) && $widget_properties['content'] == 'category' ? '' : 'd-none' }}">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Select Category') }}</label>
            </div>
            <div class="col-sm-12">
                <select class="theme-input-style" name="category">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(isset($widget_properties['category']) && $widget_properties['category'] == $category->id)>{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('category'))
                    <div class="invalid-input">{{ $errors->first('category') }}</div>
                @endif
            </div>
        </div>

        <!-- Product Count -->
        <div class="form-group mb-3">
            <label for="count" class="font-14 bold black">{{ translate('Product Count') }}</label>
            <div class="mt-1">
                <input type="number" min="1" step="1" required name="count" id="count"
                    class="form-control" placeholder="{{ translate('Category Count') }}"
                    value="{{ isset($widget_properties['count']) ? $widget_properties['count'] : '' }}">
            </div>
        </div>

        <!-- Section Style -->
        <div class="form-group mt-2">
            <label for="style" class="col-5 mb-2 font-14 bold black p-0">{{ translate('Section Style') }}
            </label>
            <select name="style" id="style" class="col-6 form-control d-inline ml-4">
                <option value="slider" @selected(isset($widget_properties['style']) && $widget_properties['style'] == 'slider')>{{ translate('Slider') }}</option>
                <option value="list" @selected(isset($widget_properties['style']) && $widget_properties['style'] == 'list')>{{ translate('List') }}</option>
            </select>
        </div>

        <!-- Slider Item -->
        <div
            class="form-group mt-2 slide-item {{ isset($widget_properties['style']) && $widget_properties['style'] == 'list' ? 'd-none' : '' }}">
            <label for="slide_item" class="col-5 mb-2 font-14 bold black p-0">{{ translate('Slider Item') }}
            </label>
            <div class="row">
                <div class="col-sm-4">
                    <select name="slide_item_lg" id="slide_item_lg" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Desktop') }}</small>
                </div>

                <div class="col-sm-4">
                    <select name="slide_item_md" id="slide_item_md" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Tab') }}</small>
                </div>

                <div class="col-sm-4">
                    <select name="slide_item_sm" id="slide_item_sm" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Mobile') }}</small>
                </div>
            </div>
            <!-- Pagination -->
            <label for="pagination" class="ml-3 mt-3">
                <input type="checkbox" name="pagination" id="pagination" @checked(isset($widget_properties['pagination']) && $widget_properties['pagination'] == '1')
                    value="1">
                {{ translate('Show Slider Pagination') }}
            </label>
        </div>

        <!-- Product Column -->
        <div
            class="form-group mt-2 product-column {{ isset($widget_properties['style']) && $widget_properties['style'] == 'list' ? '' : 'd-none' }}">
            <label for="column" class="col-5 mb-2 font-14 bold black">{{ translate('Product Column') }}
            </label>
            <select name="column" id="column" class="col-6 form-control d-inline ml-4">
                <option value="col-12" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-12')>1 Column</option>
                <option value="col-6" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-6')>2 Column</option>
                <option value="col-sm-6 col-md-4" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-4')>3 Column</option>
                <option value="col-sm-6 col-md-3" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-3')>4 Column</option>
                <option value="col-sm-6 col-md-2" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-2')>6 Column</option>
            </select>
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
        'use strict';

        //Category Wise Select
        $(document).on('change', '.custom-product-options', function() {
            let type = $(this).find("option:selected").val();
            if (type === 'category') {
                $('.category-options').removeClass('d-none');
            } else {
                $('.category-options').addClass('d-none');
            }
        });

        $('#style').on('change', function() {
            let option = $(this).find('option:selected').val();
            if (option == 'slider') {
                $('.slide-item').removeClass('d-none');
                $('.product-column').addClass('d-none');
            }
            if (option == 'list') {
                $('.product-column').removeClass('d-none');
                $('.slide-item').addClass('d-none');
            }
        });

    })(jQuery)
</script>
