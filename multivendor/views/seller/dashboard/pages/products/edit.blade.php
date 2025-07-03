@php
    $units = \Plugin\TlcommerceCore\Models\Units::with('unit_translations')
        ->orderBy('id', 'DESC')
        ->select('name', 'id')
        ->get();
    $conditions = \Plugin\TlcommerceCore\Models\ProductCondition::with('condition_translations')
        ->where('status', config('settings.general_status.active'))
        ->select('id', 'name')
        ->orderBy('id', 'DESC')
        ->get();
    
    $colors = \Plugin\TlcommerceCore\Models\Colors::where('status', config('settings.general_status.active'))
        ->select('id', 'name')
        ->orderBy('id', 'DESC')
        ->get();
    $languages = \Core\Models\Language::where('status', config('settings.general_status.active'))
        ->select('id', 'name', 'code', 'native_name')
        ->get();
    $attributes = \Plugin\TlcommerceCore\Models\ProductAttribute::with('attribute_translations')
        ->where('status', config('settings.general_status.active'))
        ->select('id', 'name')
        ->get();
@endphp
@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Edit Product') }}
@endsection
@section('custom_css')
    <!--Select2-->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--End select2-->
    <!--Editor-->
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <!--End editor-->
    <style>
        #codCountries {
            width: 100% !important;
        }

        #codCountries~span {
            width: 100% !important;
        }

        #codStates {
            width: 100% !important;
        }

        #codStates~span {
            width: 100% !important;
        }

        #codCities {
            width: 100% !important;
        }

        #codCities~span {
            width: 100% !important;
        }
    </style>
@endsection
@section('seller_main_content')
    @if (auth()->user()->shop->status == config('settings.general_status.active'))
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-3 pb-3">
            <h4><i class="icofont-plugin"></i> {{ translate('Product Information') }}</h4>
        </div>
        <form method="POST" class="row" id="product-form"
            action="{{ route('plugin.multivendor.seller.dashboard.products.update') }}">
            @csrf
            <div class="col-12 mb-3">
                <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version
                </p>
            </div>
            <!--Left side-->
            <div class="col-lg-8">
                <ul class="nav nav-tabs nav-fill border-light border-0">
                    @foreach ($languages as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                href="{{ route('plugin.multivendor.seller.dashboard.products.edit', ['id' => $product_details->id, 'lang' => $language->code]) }}">
                                <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}" width="20px">
                                <span>{{ $language->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <!--Product information-->
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Name') }}<span class="text-danger">*</span>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="name"
                                    class="theme-input-style @if (request()->has('lang') && request()->get('lang') == getdefaultlang()) product_name @endif"
                                    placeholder="{{ translate('Product Name') }}"
                                    value="{{ $product_details->translation('name', $lang) }}" required>
                                <input type="hidden" name="seller_id" value="{{ $product_details->supplier }}">
                                <input type="hidden" name="id" value="{{ $product_details->id }}" required>
                                <input type="hidden" name="lang" value="{{ $lang }}">
                                <input type="hidden" name="permalink" id="permalink_input_field"
                                    value="{{ $product_details->permalink }}" required>
                                @if ($errors->has('name'))
                                    <div class="invalid-input">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>
                        <!--Permalink-->
                        <div class="form-row mb-20 permalink-input-group  @if ($errors->has('permalink')) d-flex @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Permalink') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <a href="{{ url('/products') }}/{{ $product_details->permalink }}"
                                    target="_blank">{{ url('') }}/products/
                                    <span id="permalink">{{ $product_details->permalink }}
                                    </span>
                                    <span class="btn custom-btn ml-1 permalink-edit-btn">
                                        {{ translate('Edit') }}
                                    </span>
                                </a>
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
                        <!--End Permalink-->
                        <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Categories') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-category-select form-control" name="categories[]" multiple>
                                    @foreach ($product_details->product_cats as $category)
                                        <option value="{{ $category->id }}" selected>
                                            {{ $category->translation('name', getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('category'))
                                    <div class="invalid-input">{{ $errors->first('category') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Brand') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-brand-select form-control" name="brand">
                                    @if ($product_details->brand_info != null)
                                        <option value="{{ $product_details->brand_info->id }}" selected>
                                            {{ $product_details->brand_info->translation('name', getLocale()) }}
                                        </option>
                                    @endif
                                </select>
                                @if ($errors->has('brand'))
                                    <div class="invalid-input">{{ $errors->first('brand') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Unit') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-unit-select form-control" name="unit">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            @if ($product_details->unit != null && $product_details->unit == $unit->id) selected @endif>
                                            {{ $unit->translation('name', getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('unit'))
                                    <div class="invalid-input">{{ $errors->first('unit') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Condition') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-condition-select form-control" name="condition">
                                    @foreach ($conditions as $condition)
                                        <option value="{{ $condition->id }}"
                                            @if ($product_details->conditions == $condition->id) selected @endif>
                                            {{ $condition->translation('name', getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('condition'))
                                    <div class="invalid-input">{{ $errors->first('condition') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Tags') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-tags-select form-control" name="tags[]" multiple>
                                    @foreach ($product_details->tagItems as $tag)
                                        <option value="{{ $tag->id }}" selected>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('tags'))
                                    <div class="invalid-input">{{ $errors->first('tags') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End product information-->
                <!--Product Type-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Type') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4 pt-1">
                            <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" name="product_type" id="single_product"
                                        value="{{ config('tlecommercecore.product_variant.single') }}"
                                        @if ($product_details->has_variant == config('tlecommercecore.product_variant.single')) checked @endif
                                        onchange="switchProductType('single')">
                                    <label for="single_product"></label>
                                </div>
                                <label for="single_product">{{ translate('Single Product') }}</label>
                            </div>

                            <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" name="product_type" id="variant_product"
                                        value="{{ config('tlecommercecore.product_variant.variable') }}"
                                        @if ($product_details->has_variant == config('tlecommercecore.product_variant.variable')) checked @endif
                                        onchange="switchProductType('variant')">
                                    <label for="variant_product"></label>
                                </div>
                                <label for="variant_product">{{ translate('Variant Product') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Product Type-->
                <!--Product Variation-->
                <div
                    class="card mb-30 product-variation {{ $product_details->has_variant == config('tlecommercecore.product_variant.variable') ? '' : 'd-none' }} @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Variation') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Colors') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <div class="d-flex">
                                    <select class="product-colors-select form-control"
                                        {{ count($product_details->color_choices) > 0 ? '' : 'disabled' }}
                                        name="selected_colors[]" multiple onchange="selectColorVariant()">
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ $product_details->color_choices->contains('color_id', $color->id) ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="switch glow primary medium align-self-center ml-2">
                                        <input type="checkbox" class="color_switcher"
                                            {{ count($product_details->color_choices) > 0 ? 'checked' : '' }}
                                            onchange="colorSwitch()">
                                        <span class="control"></span>
                                    </label>
                                </div>

                                @if ($errors->has('colors'))
                                    <div class="invalid-input">{{ $errors->first('colors') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Choice Options') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <select class="product-choice-option-select form-control attributes"
                                    onchange="selectProductChoiceOption(this)">
                                    @foreach ($attributes as $attribute)
                                        <option></option>
                                        <option value="{{ $attribute->id }}">
                                            {{ $attribute->translation('name', getLocale()) }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('attributes'))
                                    <div class="invalid-input">{{ $errors->first('attributes') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="choice_options">
                            @if ($product_details->choices != null)
                                @foreach ($product_details->choices as $choice)
                                    <div class="form-row mb-10">
                                        <div class="col-sm-3 mb-2">
                                            <input type="hidden"
                                                value="{{ $choice->choice_id }}"name="product_attributes[]"
                                                class="selected_attributes">
                                            @php
                                                
                                                $choice_name = $attributes
                                                    ->where('id', $choice->choice_id)
                                                    ->first()
                                                    ->translation('name', getLocale());
                                            @endphp
                                            <input type="text" value="{{ $choice_name }}"
                                                class="theme-input-style selectec_options" disabled>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="form-group d-flex">
                                                <select class="form-control choice-options-select"
                                                    name='attribute_{{ $choice->choice_id }}_selected[]'
                                                    onchange="variantConbination()" multiple>
                                                    @php
                                                        $attribute_values = \Plugin\TlcommerceCore\Models\AttributeValues::where('attribute_id', $choice->choice_id)
                                                            ->where('status', config('settings.general_status.active'))
                                                            ->get();
                                                    @endphp
                                                    @foreach ($attribute_values as $values)
                                                        <option value="{{ $values->id }}"
                                                            {{ $product_details->choice_options->contains('option_id', $values->id) ? 'selected' : '' }}>
                                                            {{ $values->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="align-self-center ml-1 bg-transparent black"
                                                    onclick="removeProductChoiceOption(this)">
                                                    <i class="icofont-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!--End Product Variation-->
                <!--Product Price and stock-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Price And Stock') }}</h4>
                        </div>
                    </div>
                    <div
                        class="card-body single-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.single') ? '' : 'd-none' }}">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Purchase Price') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="purchase_price" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->single_price != null ? $product_details->single_price->purchase_price : 0 }}">
                                @if ($errors->has('purchase_price'))
                                    <div class="invalid-input">{{ $errors->first('purchase_price') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Unit Price') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="unit_price" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->single_price != null ? $product_details->single_price->unit_price : 0 }}">
                                @if ($errors->has('unit_price'))
                                    <div class="invalid-input">{{ $errors->first('unit_price') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Quantity') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="quantity" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->single_price != null ? $product_details->single_price->quantity : 0 }}">
                                @if ($errors->has('quantity'))
                                    <div class="invalid-input">{{ $errors->first('quantity') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Sku') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="sku" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->single_price != null ? $product_details->single_price->sku : 0 }}">
                                @if ($errors->has('sku'))
                                    <div class="invalid-input">{{ $errors->first('sku') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div
                        class="variant-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.variable') ? '' : 'd-none' }}">
                        <div class="variant-combination">
                            @if ($product_details->variations != null)
                                <div class="table-responsive">
                                    <table class="table-bordered dh-table">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('Variant') }}</th>
                                                <th>{{ translate('Purchase Price') }}</th>
                                                <th>{{ translate('Unit Price') }}</th>
                                                <th>{{ translate('SKU') }}</th>
                                                <th>{{ translate('Quantity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product_details->variations as $key => $combination)
                                                @php
                                                    $variant_array = explode('/', trim($combination->variant, '/'));
                                                    $name = '';
                                                    foreach ($variant_array as $com_key => $variant) {
                                                        $variant_com_array = explode(':', $variant);
                                                        if ($variant_com_array[0] === 'color') {
                                                            $option_name = translate('Color');
                                                            $choice_name = \Plugin\TlcommerceCore\Models\Colors::find($variant_com_array[1])->translation('name');
                                                        } else {
                                                            $option_name = $attributes
                                                                ->where('id', $variant_com_array[0])
                                                                ->first()
                                                                ->translation('name', getLocale());
                                                            $choice_name = \Plugin\TlcommerceCore\Models\AttributeValues::find($variant_com_array[1])->name;
                                                        }
                                                    
                                                        $name .= $option_name . ':' . $choice_name . ' | ';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <label class="control-label">{{ trim($name, ' | ') }}</label>
                                                        <input type="hidden" value="{{ $combination->variant }}"
                                                            name="variations[{{ $key }}][code]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="theme-input-style"
                                                            name="variations[{{ $key }}][purchase_price]"
                                                            value="{{ $combination->purchase_price }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="theme-input-style"
                                                            name="variations[{{ $key }}][unit_price]"
                                                            value="{{ $combination->unit_price }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="theme-input-style"
                                                            name="variations[{{ $key }}][sku]"
                                                            value="{{ $combination->sku }}"
                                                            placeholder="{{ translate('Sku') }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="theme-input-style"
                                                            name="variations[{{ $key }}][quantity]"
                                                            value="{{ $combination->quantity }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </table>
                                    <!-- End Variant combination -->
                                </div>
                            @else
                                <p class="alert alert-danger m-2">{{ translate('No variant selected yet') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <!--End Product Price and stock-->
                <!--Product Discount-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Discount') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Discount') }} </label>
                            </div>
                            <div class="col-sm-5 mb-30">
                                <input type="text" name="discount_amount" class="theme-input-style"
                                    placeholder="0.00" value="{{ $product_details->discount_amount }}">
                                @if ($errors->has('discount_amount'))
                                    <div class="invalid-input">{{ $errors->first('discount_amount') }}</div>
                                @endif
                            </div>
                            <div class="col-sm-4">
                                <select class="theme-input-style" name="discount_amount_type">
                                    <option value="{{ config('tlecommercecore.amount_type.flat') }}"
                                        @if ($product_details->discount_type == config('tlecommercecore.amount_type.flat')) selected @endif> {{ translate('Flat') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.amount_type.percent') }}"
                                        @if ($product_details->discount_type == config('tlecommercecore.amount_type.percent')) selected @endif> {{ translate('Percentage') }}
                                    </option>
                                </select>
                                @if ($errors->has('discount_amount_type'))
                                    <div class="invalid-input">{{ $errors->first('discount_amount_type') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Product Discount-->
                <!--Color Variation Images-->
                <div
                    class="card mb-30 product-color-images {{ count($product_details->color_choices) > 0 ? '' : 'd-none' }}  @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Color Variation Images') }}</h4>
                        </div>
                    </div>
                    <div class="color-variant-image">
                        @if (count($product_details->color_choices) > 0)
                            <div class="card-body">
                                @foreach ($product_details->color_choices as $key => $color)
                                    @php
                                        $color_name = \Plugin\TlcommerceCore\Models\Colors::find($color->color_id)->translation('name');
                                        $color_images = \Plugin\TlcommerceCore\Models\ProductColorVariantImages::where('product_id', $product_details->id)
                                            ->where('color_id', $color->color_id)
                                            ->pluck('image')
                                            ->toArray();
                                    @endphp
                                    <div class="form-row mb-20">
                                        <div class="col-sm-3">
                                            <label class="font-14 bold black">{{ $color_name }} </label>
                                        </div>
                                        <div class="col-sm-9">
                                            @include('core::base.includes.media.media_input_multi_select', [
                                                'input' => 'color_' . $color->color_id . '_image',
                                                'data' => implode(',', $color_images),
                                                'indicator' => $color->color_id,
                                                'container_id' => '#multi_input_' . $color->color_id,
                                            ])
                                            @if ($errors->has('color_' . $color->color_id . '_image'))
                                                <div class="invalid-input">
                                                    {{ $errors->first('color_' . $color->color_id . '_image') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="alert alert-danger m-2">{{ translate('No color variant selected yet') }}</p>
                        @endif
                    </div>
                </div>
                <!--End Color Variation Images-->
                <!--product images-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Images') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black mb-0">{{ translate('Thumbnail Image') }} </label>
                                <p>385x380</p>
                            </div>
                            <div class="col-sm-">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'thumbnail_image',
                                    'data' => $product_details->thumbnail_image,
                                    'user_filter' => true,
                                ])
                                @if ($errors->has('thumbnail_image'))
                                    <div class="invalid-input">{{ $errors->first('thumbnail_image') }}</div>
                                @endif
                            </div>
                        </div>
                        <div
                            class="form-row mb-20 product-gallery-images {{ count($product_details->color_choices) > 0 ? 'd-none' : '' }}">
                            <div class="col-sm-3">
                                <label class="font-14 bold black mb-0">{{ translate('Gallery Images') }} </label>
                                <p>624x624</p>
                            </div>
                            <div class="col-sm-9">
                                @php
                                    $gallary_images = \Plugin\TlcommerceCore\Models\ProductGalleryImages::where('product_id', $product_details->id)
                                        ->pluck('image_id')
                                        ->toArray();
                                @endphp
                                @include('core::base.includes.media.media_input_multi_select', [
                                    'input' => 'gallery_images',
                                    'data' => implode(',', $gallary_images),
                                    'indicator' => 1,
                                    'container_id' => '#multi_input_1',
                                    'user_filter' => true,
                                ])
                                @if ($errors->has('gallery_images'))
                                    <div class="invalid-input">{{ $errors->first('gallery_images') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End product Images-->
                <!--Product Description-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Description') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Summary') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <div class="editor-wrap">
                                    <textarea id="short_description" name="summary">{{ $product_details->translation('summary', $lang) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Description') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <div class="editor-wrap">
                                    <textarea id="description" name="description">{{ $product_details->translation('description', $lang) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Product Description-->

                <!--Product pdf specification-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('PDF Specification') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('PDF Specification') }} </label>
                            </div>
                            <div class="col-sm-9">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'pdf_specification',
                                    'data' => $product_details->pdf_specifications,
                                    'user_filter' => true,
                                ])
                                @if ($errors->has('pdf_specification'))
                                    <div class="invalid-input">{{ $errors->first('pdf_specification') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End product pdf specification-->
                <!--Product Video-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Product Video') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Youtube Link') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="video" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->video_link }}">
                                @if ($errors->has('video'))
                                    <div class="invalid-input">{{ $errors->first('video') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End product Video-->
                <!--Product Seo information-->
                <div class="card mb-30 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Seo Meta Tags') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Meta Title') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="meta_title" class="theme-input-style"
                                    placeholder="{{ translate('Type here') }}"
                                    value="{{ $product_details->product_seo != null ? $product_details->product_seo->meta_title : '' }}">
                                @if ($errors->has('meta_title'))
                                    <div class="invalid-input">{{ $errors->first('meta_title') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Meta Description') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <textarea class="theme-input-style" name="meta_description">{{ $product_details->product_seo != null ? $product_details->product_seo->meta_description : '' }}</textarea>
                                @if ($errors->has('meta_description'))
                                    <div class="invalid-input">{{ $errors->first('meta_description') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Meta Image') }} </label>
                            </div>
                            <div class="col-sm-">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'meta_image',
                                    'data' =>
                                        $product_details->product_seo != null
                                            ? $product_details->product_seo->meta_image
                                            : null,
                                    'user_filter' => true,
                                ])
                                @if ($errors->has('meta_image'))
                                    <div class="invalid-input">{{ $errors->first('meta_image') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End product seo information-->
            </div>
            <!--End side-->
            <!--Right side-->
            <div class="col-lg-4 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                <!--Featured-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Featured') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="is_featured"
                                        @if ($product_details->is_featured == config('settings.general_status.active')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Featured-->
                <!--Refundable-->
                @if (isActivePluging('refund'))
                    <div class="card mb-30">
                        <div class="card-header bg-white border-bottom2">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h4>{{ translate('Refundable') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row mb-20">
                                <div class="col-sm-6">
                                    <label class="font-14 bold black ">{{ translate('Status') }} </label>
                                </div>
                                <div class="col-sm-6">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" name="is_refundable"
                                            @if ($product_details->is_refundable == config('settings.general_status.active')) checked @endif>
                                        <span class="control"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!--End Refundable-->
                <!--Authentic-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Authentic') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="is_authenthic"
                                        @if ($product_details->is_authentic == config('settings.general_status.active')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Authentic-->
                <!--Shipping Information-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Shipping Information') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="shipping-info">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Weight') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="text" name="weight"
                                        value="{{ $product_details->shipping_info != null ? $product_details->shipping_info->weight : 0 }}"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">{{ translate('gm') }}</div>
                                    </div>
                                </div>
                                @if ($errors->has('weight'))
                                    <div class="invalid-input">{{ $errors->first('weight') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Height') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="text" name="height"
                                        value="{{ $product_details->shipping_info != null ? $product_details->shipping_info->height : 0 }}"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">{{ translate('cm') }}</div>
                                    </div>
                                </div>
                                @if ($errors->has('height'))
                                    <div class="invalid-input">{{ $errors->first('height') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Length') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="text" name="length"
                                        value="{{ $product_details->shipping_info != null ? $product_details->shipping_info->length : 0 }}"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">{{ translate('cm') }}</div>
                                    </div>
                                </div>
                                @if ($errors->has('length'))
                                    <div class="invalid-input">{{ $errors->first('length') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Width') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="text" name="width"
                                        value="{{ $product_details->shipping_info != null ? $product_details->shipping_info->width : 0 }}"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">{{ translate('cm') }}</div>
                                    </div>
                                </div>
                                @if ($errors->has('width'))
                                    <div class="invalid-input">{{ $errors->first('width') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Shipping Information-->
                <!--Shipping area-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Shipping Profile') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text text-danger">
                            {{ translate('Product shipping cost and delivery time depends on shipping profile') }}
                        </p>
                        @if ($shipping_profiles->count() > 0)
                            <div class="form-row mb-20">
                                <select name="shipping_profile" class="theme-input-style">
                                    @foreach ($shipping_profiles as $profile)
                                        <option value="{{ $profile->id }}" @selected($product_details->shippingProfile != null && $product_details->shippingProfile->profile_id == $profile->id)>
                                            {{ $profile->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="shipping-zone-list">
                            @if ($product_details->shippingProfile != null)
                                @include(
                                    'plugin/multivendor::seller.dashboard.pages.products.shipping_zone',
                                    ['id' => $product_details->shippingProfile->profile_id]
                                )
                            @endif
                        </div>
                    </div>
                </div>
                <!--End Shipping area-->
                <!--Cash on delivery-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Cash On Delivery') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="cash_on_delivery" class="cash-on-delivery"
                                        @if ($product_details->is_active_cod == config('settings.general_status.active')) checked @endif onchange="cashOnDelivery()">
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div
                            class="cash-on-delivery-info {{ $product_details->is_active_cod == config('settings.general_status.active') ? '' : 'd-none' }}">
                            <div class="form-group mb-4 pt-1">
                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="cod_anywhere"
                                            value="{{ config('tlecommercecore.cod_location.anywhere') }}"
                                            name="cod_location" checked onchange="codLocation()"
                                            @if ($product_details->cod_location_type == config('tlecommercecore.cod_location.anywhere')) checked @endif>
                                        <label for="cod_anywhere"></label>
                                    </div>
                                    <label for="cod_anywhere">{{ translate('Anywhere') }}</label>
                                </div>

                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="cod_in_custom_location"
                                            value="{{ config('tlecommercecore.cod_location.custom') }}"
                                            name="cod_location" onchange="codLocation()"
                                            @if ($product_details->cod_location_type == config('tlecommercecore.cod_location.custom')) checked @endif>
                                        <label for="cod_in_custom_location"></label>
                                    </div>
                                    <label for="cod_in_custom_location">{{ translate('Custom Locations') }}</label>
                                </div>
                            </div>

                            <div
                                class="form-row mb-20 cod-contries_options {{ $product_details->cod_location_type == config('tlecommercecore.shipping_location.custom') ? '' : 'd-none' }}">
                                <label class="font-14 bold black col-sm-3">{{ translate('Countries') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-countries-select" id="codCountries"
                                        name="cod_selected_countries[]" multiple onchange="clearCodStateOptions()">
                                        @foreach ($product_details->codCountryList as $country)
                                            <option value="{{ $country->id }}" selected>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('cod_selected_countries'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_countries') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="form-row mb-20 cod-states-options {{ $product_details->cod_location_type == config('tlecommercecore.shipping_location.custom') ? '' : 'd-none' }}">
                                <label class="font-14 bold black col-sm-3">{{ translate('States') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-state-select" id="codStates"
                                        name='cod_selected_states[]' multiple onchange="clearCodCitiesOptions()">
                                        @foreach ($product_details->codStateList as $state)
                                            <option value="{{ $state->id }}" selected>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('cod_selected_states'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_states') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-row mb-20 cod-cities-options {{ $product_details->cod_location_type == config('tlecommercecore.shipping_location.custom') ? '' : 'd-none' }}">
                                <label class="font-14 bold black col-sm-3">{{ translate('Cities') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-city-select" id="codCities"
                                        name='cod_selected_cities[]' multiple>
                                        @foreach ($product_details->codCityList as $city)
                                            <option value="{{ $city->id }}" selected>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('cod_selected_cities'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_cities') }}</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--End Cashon delivery-->
                <!--Warranty-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Warranty') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="has_warranty" class="has-warranty"
                                        onchange="warrantyConfig()" @if ($product_details->has_warranty == config('settings.general_status.active')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div
                            class="warranty-config {{ $product_details->has_warranty == config('settings.general_status.active') ? '' : 'd-none' }}">
                            <div class="form-row mb-20">
                                <div class="col-sm-6">
                                    <label class="font-14 bold black ">{{ translate('Replacement Warranty') }} </label>
                                </div>
                                <div class="col-sm-6">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" class="replacement-warranty" name="replacement_warranty"
                                            @if ($product_details->has_replacement_warranty == config('settings.general_status.active')) checked @endif>
                                        <span class="control"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-12">{{ translate('Warranty Days') }} </label>
                                <div class="input-group addon col-12">
                                    <input type="text" class="form-control style--two" name="warranty_day"
                                        value="{{ $product_details->warrenty_days }}" placeholder="0">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">Days</div>
                                    </div>
                                </div>
                                @if ($errors->has('warranty_day'))
                                    <div class="invalid-input">{{ $errors->first('warranty_day') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Warranty-->
                <!--Low stock quantity-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Low stock quantity') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black  col-3">{{ translate('Quantity') }} </label>
                            <input type="text" class="theme-input-style col-9" name="qty_alert"
                                value="{{ $product_details->low_stock_quantity_alert }}" placeholder="0">
                            @if ($errors->has('qty_alert'))
                                <div class="invalid-input">{{ $errors->first('qty_alert') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <!--End Low stock quantity-->
                <!--Purchase quantity-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Purchase Quantity') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black  col-sm-4">{{ translate('Minimum Quantity') }} </label>
                            <input type="text" class="theme-input-style col-sm-8" name="min_purchase_qty"
                                value="{{ $product_details->min_item_on_purchase }}" placeholder="0">
                            @if ($errors->has('min_purchase_qty'))
                                <div class="invalid-input">{{ $errors->first('min_purchase_qty') }}</div>
                            @endif
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black  col-sm-4">{{ translate('Miximum Quantity') }} </label>
                            <input type="text" class="theme-input-style col-sm-8"
                                value="{{ $product_details->max_item_on_purchase }}" name="max_purchase_qry"
                                placeholder="0">

                            @if ($errors->has('max_purchase_qry'))
                                <div class="invalid-input">{{ $errors->first('max_purchase_qry') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <!--End Purchase quantity-->
                <!--Attatchment-->
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Attatchment on Purchase') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="is_active_attatchment" class="attatchment-required"
                                        onchange="attatchmentConfig()" @if ($product_details->is_active_attatchment == config('settings.general_status.active')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div
                            class="attatchment-config  {{ $product_details->is_active_attatchment == config('settings.general_status.active') ? '' : 'd-none' }} ">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-sm-4">{{ translate('Attatchment Name') }} </label>
                                <div class="col-sm-8">
                                    <input type="text" class="theme-input-style"
                                        value="{{ $product_details->attatchment_name }}" name="attatchment_name"
                                        placeholder="{{ translate('Attatchment Name') }}">
                                </div>
                                @if ($errors->has('attatchment_name'))
                                    <div class="invalid-input">{{ $errors->first('attatchment_name') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Attatchment-->
            </div>
            <!--Ed right side-->
            <!--Form submit area-->
            <div
                class="bottom-button d-flex align-items-center justify-content-sm-end gap-10 flex-wrap justify-content-center">
                <button type="submit" name="status" value="{{ config('settings.general_status.in_active') }}"
                    class="btn btn-dark btn-outline-info" tabindex="4">
                    {{ translate('Update & Draft') }}
                </button>
                <button type="submit" name="status" value="{{ config('settings.general_status.active') }}"
                    class="btn btn-outline-primary" tabindex="4">
                    {{ translate('Update & Publish') }}
                </button>
            </div>
            <!--End Form submit area-->
        </form>
        @include('core::base.media.partial.media_modal')
    @else
        <p class="alert alert-info">Your Shop is Inactive. Please contact with Administration </p>
    @endif
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->
    <!--Editor-->
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <!--End Editor-->
    <script src="{{ asset('/public/backend/assets/js/products.js?v=1') }}"></script>
    <script>
        let select_attribute_placeholder = '{{ translate('Select Choice Option') }}';
        (function($) {
            "use strict";
            initDropzone();
            /**
             *  product category
             * 
             */
            $('.product-category-select').select2({
                theme: "classic",
                closeOnSelect: false,
                placeholder: '{{ translate('Select product category') }}',
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.category.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
            /**
             *  product brand
             * 
             */
            $('.product-brand-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select product brand') }}',
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.brand.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
            /**
             * product tags
             * 
             */
            $('.product-tags-select').select2({
                theme: "classic",
                tags: true,
                closeOnSelect: false,
                placeholder: '{{ translate('Select or insert product tags') }}',
                createTag: function(item) {
                    return {
                        id: item.term,
                        text: item.term,
                    };
                },
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.tag.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

            /**
             *  product Unit
             * 
             */
            $('.product-unit-select').select2({
                theme: "classic",
                placeholder: '{{ translate('select product unit') }}',
            });
            /**
             *  product Condition
             * 
             */
            $('.product-condition-select').select2({
                theme: "classic",
                placeholder: '{{ translate('select product condition') }}',
            });

            /**
             *select product colors
             *
             */
            $('.product-colors-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Nothing Selected') }}',
                closeOnSelect: false,
            });
            /**
             * select product Attributes
             * 
             */
            $('.product-choice-option-select').select2({
                theme: "classic",
                placeholder: select_attribute_placeholder,
            }).on('change', function(e) {

            });
            /*select product choice otions*/
            $('.choice-options-select').select2({
                theme: "classic",
                placeholder: 'Nothing Selected',
                closeOnSelect: false
            });

            /**
             * select product cod countries
             * 
             */
            $('.cod-countries-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select Countries') }}',
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.cod.countries.dropdown.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
            /**
             * select product shipping state
             *
             */
            $('.cod-state-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select States') }}',
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.cod.state.dropdown.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            countries: $("#codCountries").val(),
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
            /**
             * select product cod cities 
             *
             */
            $('.cod-city-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select Cities') }}',
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.cod.city.dropdown.option') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            states: $("#codStates").val(),
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
            //Select shipping profile
            $('.select_shipping_profile').on('change', function(e) {
                let profile_id = $(this).val();
                if (profile_id == '') {
                    $('.shipping-zone-list').html('')
                    return 0;
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: profile_id
                    },
                    url: '{{ route('plugin.multivendor.seller.dashboard.product.shipping.zones') }}',
                    success: function(data) {
                        $('.shipping-zone-list').html(data)
                    }
                });
            });
        })(jQuery);

        // send file function summernote
        function sendFile(image, editor, welEditable, section_id) {
            "use strict";
            let imageUploadUrl = '{{ route('core.blog.content.image') }}';
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
                        $('#' + section_id).summernote("insertNode", image[0]);
                    } else {
                        toastr.error(data.error, "Error!");
                    }

                },
                error: function(data) {
                    toastr.error('Image Upload Failed', "Error!");
                }
            });
        }

        /**
         *Enable and disable color variation selector
         *  
         */
        function colorSwitch() {
            "use strict";
            if ($('.color_switcher').is(":checked")) {
                $('.product-colors-select').attr('disabled', false);
                $('.color-variant-image').closest(".card").removeClass('d-none');
                $('.product-gallery-images').addClass('d-none');
            } else {
                $('.product-colors-select').attr('disabled', true)
                $(".product-colors-select").val(null).trigger("change")
                $('.color-variant-image').closest(".card").addClass('d-none');
                $('.product-gallery-images').removeClass('d-none');
                variantConbination()
            }
        }
        /**
         * 
         * Select color variation
         */
        function selectColorVariant() {
            "use strict";
            variantConbination()
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                data: $('#product-form').serialize(),
                url: '{{ route('plugin.tlcommercecore.product.form.color.variant.image.input') }}',
                success: function(data) {
                    $('.product-color-images').removeClass('d-none');
                    $('.color-variant-image').html(data)
                }
            });
        }
        /**
         * 
         * Select a choice option
         */
        function selectProductChoiceOption(e) {
            "use strict";
            let attribute_id = $(e).val();
            if (attribute_id) {
                let selected_items = $("input[name='product_attributes[]']")
                    .map(function() {
                        return $(this).val();
                    }).get();
                if (selected_items.indexOf(attribute_id) == -1) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        type: "POST",
                        data: {
                            attribute_id: attribute_id,
                            _token: "{{ csrf_token() }}",
                        },
                        url: '{{ route('plugin.tlcommercecore.product.form.add.choice.option') }}',
                        success: function(data) {
                            $('.choice_options').append(data)
                            $(".product-choice-option-select").val(null).trigger("change")
                        }
                    });
                } else {
                    $(".product-choice-option-select").val(null).trigger("change")
                }
            }
        }
        /**
         * Remove product choice option from selected list
         *
         */
        function removeProductChoiceOption(e) {
            "use strict";
            $(e).closest('.form-row').remove();
            variantConbination()
        }
        /**
         *Generate product variant combination
         *  
         */
        function variantConbination() {
            "use strict";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                data: $('#product-form').serialize(),
                url: '{{ route('plugin.tlcommercecore.product.form.variant.combination') }}',
                success: function(data) {
                    $('.variant-combination').html(data)
                }
            });
        }

        /**
         * Enable and disable product warranty
         * 
         */
        function warrantyConfig() {
            "use strict";
            if ($('.has-warranty').is(":checked")) {
                $('.warranty-config').removeClass('d-none')
            } else {
                $('.warranty-config').addClass('d-none')
            }
        }
        /**
         * Enable and disable attachment on purchase
         * 
         */
        function attatchmentConfig() {
            "use strict";
            if ($('.attatchment-required').is(":checked")) {
                $('.attatchment-config').removeClass('d-none')
            } else {
                $('.attatchment-config').addClass('d-none')
            }
        }
        /**
         * 
         * Enable and disable cash on delivery
         */
        function cashOnDelivery() {
            "use strict";
            if ($('.cash-on-delivery').is(":checked")) {
                $('.cash-on-delivery-info').removeClass('d-none')
            } else {
                $('.cash-on-delivery-info').addClass('d-none')
            }
        }
        /**
         *Select cod location
         *  
         */
        function codLocation() {
            "use strict";
            let location_type = $('input[name="cod_location"]:checked').val();
            if (location_type == '{{ config('tlecommercecore.cod_location.anywhere') }}') {
                $('.cod-contries_options').addClass('d-none')
                $('.cod-states-options').addClass('d-none')
                $('.cod-cities-options').addClass('d-none')
            } else {
                $('.cod-contries_options').removeClass('d-none')
                $('.cod-states-options').removeClass('d-none')
                $('.cod-cities-options').removeClass('d-none')
            }
        }

        /**
         *Clear Cod states options
         * 
         */
        function clearCodStateOptions() {
            "use strict";
            $("#codStates").val([]).trigger('change');
            clearCodCitiesOptions()

        }
        /**
         *Clear Cod cities options
         * 
         */
        function clearCodCitiesOptions() {
            "use strict";
            $("#codCities").val([]).trigger('change');

        }
    </script>
@endsection
