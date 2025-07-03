@php
    use Plugin\TlcommerceCore\Repositories\SettingsRepository;
    $cod_info = \Plugin\TlcommerceCore\Models\PaymentMethods::where('id', config('tlecommercecore.payment_methods.cod'))
        ->select(['status', 'id'])
        ->first();
    $is_active_cod = $cod_info != null ? $cod_info->status : config('settings.general_status.in_active');
    $tax_profiles = \Plugin\TlcommerceCore\Models\TaxProfile::where('status', config('settings.general_status.active'))
        ->select('id', 'title')
        ->get();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Add New Product') }}
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
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Add New Product') }}</h4>
    </div>
    <form method="POST" class="row" enctype="multipart/form-data" id="product-form"
        action="{{ route('plugin.tlcommercecore.product.store.new') }}">
        @csrf
        <!--Left side-->
        <div class="col-lg-8">
            <div class="mb-3">
                <p class="alert alert-info">You are inserting
                    <strong>"{{ getLanguageNameByCode(getDefaultLang()) }}"</strong> version
                </p>
            </div>
            <!--Product information-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product Information') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="theme-input-style product_name"
                                placeholder="{{ translate('Product Name') }}" value="{{ old('name') }}" required>
                            <input type="hidden" name="permalink" id="permalink_input_field" required>
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <!--Permalink-->
                    <div
                        class="form-row mb-20 permalink-input-group d-none @if ($errors->has('permalink')) d-flex @endif">
                        <div class="col-sm-3">
                            <label class="font-14 bold black">{{ translate('Permalink') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <a href="#">{{ url('') }}/products/<span
                                    id="permalink">{{ old('permalink') }}</span><span
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
                    <!--End Permalink-->
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Categories') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="product-category-select form-control" name="categories[]" multiple>

                            </select>
                            @if ($errors->has('category'))
                                <div class="invalid-input">{{ $errors->first('category') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Brand') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="product-brand-select form-control" name="brand">
                                <option></option>
                            </select>
                            @if ($errors->has('brand'))
                                <div class="invalid-input">{{ $errors->first('brand') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Unit') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="product-unit-select form-control" name="unit">
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->translation('name', getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('unit'))
                                <div class="invalid-input">{{ $errors->first('unit') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Condition') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="product-condition-select form-control" name="condition">
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->id }}">
                                        {{ $condition->translation('name', getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('condition'))
                                <div class="invalid-input">{{ $errors->first('condition') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Tags') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="product-tags-select form-control" name="tags[]" multiple>
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
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product Type') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4 pt-1">
                        <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                            <div class="custom-radio mr-3">
                                <input type="radio" name="product_type" id="single_product"
                                    value="{{ config('tlecommercecore.product_variant.single') }}" checked
                                    onchange="switchProductType('single')">
                                <label for="single_product"></label>
                            </div>
                            <label for="single_product">{{ translate('Single Product') }}</label>
                        </div>

                        <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                            <div class="custom-radio mr-3">
                                <input type="radio" name="product_type" id="variant_product"
                                    value="{{ config('tlecommercecore.product_variant.variable') }}"
                                    @if (old('product_type') == config('tlecommercecore.product_variant.variable')) checked @endif
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
            <div class="card mb-30 product-variation d-none">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                <select class="product-colors-select form-control" disabled name="selected_colors[]"
                                    multiple onchange="selectColorVariant()">
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}">
                                            {{ $color->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="switch glow primary medium align-self-center ml-2">
                                    <input type="checkbox" class="color_switcher" onchange="colorSwitch()">
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
                    </div>
                </div>
            </div>
            <!--End Product Variation-->
            <!--Product Price and stock-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product Price And Stock') }}</h4>
                    </div>
                </div>
                <div class="card-body single-product-price">
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Purchase Price') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" name="purchase_price" class="theme-input-style" placeholder="0.00"
                                value="{{ old('purchase_price') }}">
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
                            <input type="number" name="unit_price" class="theme-input-style" placeholder="0.00"
                                value="{{ old('unit_price') }}">
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
                            <input type="number" name="quantity" class="theme-input-style" placeholder="00"
                                value="{{ old('quantity') }}">
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
                                placeholder="{{ translate('Type product sku') }}" value="{{ old('sku') }}">
                            @if ($errors->has('sku'))
                                <div class="invalid-input">{{ $errors->first('sku') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="variant-product-price d-none">
                    <div class="variant-combination">
                        <p class="alert alert-danger m-2">{{ translate('No variant selected yet') }}</p>
                    </div>
                </div>
            </div>
            <!--End Product Price and stock-->
            <!--Product Discount-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product Discount') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (getEcommerceSetting('enable_product_discount') == config('settings.general_status.active'))
                        <div class="form-row">
                            <div class="col-sm-3">
                                <label class="font-14 bold black ">{{ translate('Discount') }} </label>
                            </div>
                            <div class="col-sm-5 mb-30">
                                <input type="number" name="discount_amount" class="theme-input-style"
                                    placeholder="0.00" value="{{ old('discount_amount') }}">
                                @if ($errors->has('discount_amount'))
                                    <div class="invalid-input">{{ $errors->first('discount_amount') }}</div>
                                @endif
                            </div>
                            <div class="col-sm-4">
                                <select class="theme-input-style select2" name="discount_amount_type">
                                    <option value="{{ config('tlecommercecore.amount_type.flat') }}">
                                        {{ translate('Flat') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.amount_type.percent') }}">
                                        {{ translate('Percentage') }}
                                    </option>
                                </select>
                                @if ($errors->has('discount_amount_type'))
                                    <div class="invalid-input">{{ $errors->first('discount_amount_type') }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (getEcommerceSetting('enable_product_discount') != config('settings.general_status.active'))
                        <p class="mt-0 font-13">
                            {{ translate('Product discount is disabled. You can enable discount from') }}
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'products']) }}"
                                class="btn-link">{{ translate('Products Settings') }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            <!--End Product Discount-->
            <!--Color Variation Images-->
            <div class="card product-color-images mb-30 d-none">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Color Variation Images') }}</h4>
                    </div>
                </div>
                <div class="color-variant-image">
                    <p class="alert alert-danger m-2">{{ translate('No color variant selected yet') }}</p>
                </div>
            </div>
            <!--End Color Variation Images-->
            <!--Product Description-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                <textarea id="short_description" name="summary">{{ old('summary') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black ">{{ translate('Description') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <div class="editor-wrap">
                                <textarea id="description" name="description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Product Description-->
            <!--product images-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                'data' => old('thumbnail_image'),
                            ])
                            @if ($errors->has('thumbnail_image'))
                                <div class="invalid-input">{{ $errors->first('thumbnail_image') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 product-gallery-images">
                        <div class="col-sm-3">
                            <label class="font-14 bold black mb-0">{{ translate('Gallery Images') }} </label>
                            <p>624x624</p>
                        </div>
                        <div class="col-sm-9">
                            @include('core::base.includes.media.media_input_multi_select', [
                                'input' => 'gallery_images',
                                'data' => old('gallery_images'),
                                'indicator' => 1,
                                'container_id' => '#multi_input_1',
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
            <!--Product pdf specification-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                'data' => old('pdf_specification'),
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
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                placeholder="{{ translate('Type here') }}" value="{{ old('video') }}">
                            @if ($errors->has('video'))
                                <div class="invalid-input">{{ $errors->first('video') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--End product Video-->
            <!--Product Seo information-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                placeholder="{{ translate('Type here') }}" value="{{ old('meta_title') }}">
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
                            <textarea class="theme-input-style" name="meta_description">{{ old('meta_description') }}</textarea>
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
                                'data' => old('meta_image'),
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
        <div class="col-lg-4">
            <!--Shipping Configuration-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Shipping Configuration') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!--Profile Based Shipping Rate-->
                    @if (SettingsRepository::getEcommerceSetting('shipping_option') != null &&
                            SettingsRepository::getEcommerceSetting('shipping_option') ==
                                config('tlecommercecore.shipping_cost_options.profile_wise_rate'))
                        <div class="profile-wise-shiping-cost">
                            @if ($shipping_profiles->count() > 0)
                                <div class="form-row">
                                    <select name="shipping_profile" class="theme-input-style select2">
                                        @foreach ($shipping_profiles as $profile)
                                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <p class="text text-danger">
                                    {{ translate('Please add a shipping profile, Otherwise customer can not complete checkout') }}
                                </p>
                            @endif
                            <p class="font-13 mb-0 mt-3">
                                {{ translate('Product shipping cost and shipping time depends on shipping profile') }}
                                <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}" class="btn-link">
                                    {{ translate('Manage Shipping Profiles') }}
                                </a>
                            </p>
                        </div>
                    @endif
                    <!--End Profile Based Shipping Rate-->
                    <!--Flat Rate Shipping Cost-->
                    @if (SettingsRepository::getEcommerceSetting('shipping_option') == null ||
                            SettingsRepository::getEcommerceSetting('shipping_option') ==
                                config('tlecommercecore.shipping_cost_options.flat_rate'))
                        <div class="profile-wise-shiping-cost">
                            <p class="font-13 mb-0">
                                {{ translate('Product wise shipping cost or profile based shiping cost is disable') }}
                                <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}" class="btn-link">
                                    {{ translate('Configure Shipping & Delivery') }}
                                </a>
                            </p>
                        </div>
                    @endif
                    <!--End Flat Rate Shipping Cost-->
                    <!--Product Wise shipping Cost-->
                    @if (SettingsRepository::getEcommerceSetting('shipping_option') != null &&
                            SettingsRepository::getEcommerceSetting('shipping_option') ==
                                config('tlecommercecore.shipping_cost_options.product_wise_rate'))
                        <div class="product-wise-shiping-cost">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black">{{ translate('Shipping Cost') }} </label>
                                <div class="input-group addon">
                                    <input type="number" name="product_shipping_cost" class="theme-input-style"
                                        placeholder="0.00">
                                </div>
                                @if ($errors->has('product_shipping_cost'))
                                    <div class="invalid-input">{{ $errors->first('product_shipping_cost') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-6">
                                    <label class="font-14 bold black ">{{ translate('Is Product Quantity Mulitiply') }}
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" name="is_product_quantity_multiple">
                                        <span class="control"></span>
                                    </label>
                                </div>
                            </div>
                            <p class="font-13 mb-0">
                                {{ translate('You can manage shipping cost configuration from') }}
                                <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}"
                                    class="btn-link">{{ translate('Shipping & Delivery') }}
                                </a>
                            </p>
                        </div>
                    @endif
                    <!--End Product Wise shipping Cost-->
                </div>
            </div>
            <!--End Shipping Configuration-->
            <!--Shipping Information-->
            @if (SettingsRepository::getEcommerceSetting('shipping_option') == null ||
                    SettingsRepository::getEcommerceSetting('shipping_option') ==
                        config('tlecommercecore.shipping_cost_options.profile_wise_rate'))
                <div class="card mb-30">
                    <div class="card-header bg-white border-bottom2 py-3">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4>{{ translate('Shipping Information') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="shipping-info">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Weight') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="number" name="weight" class="form-control style--two"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">gm</div>
                                    </div>
                                </div>
                                @if ($errors->has('weight'))
                                    <div class="invalid-input">{{ $errors->first('weight') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Height') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="number" name="height" class="form-control style--two"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">cm</div>
                                    </div>
                                </div>
                                @if ($errors->has('height'))
                                    <div class="invalid-input">{{ $errors->first('height') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Length') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="number" name="length" class="form-control style--two"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">cm</div>
                                    </div>
                                </div>
                                @if ($errors->has('length'))
                                    <div class="invalid-input">{{ $errors->first('length') }}</div>
                                @endif
                            </div>
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-3">{{ translate('Width') }} </label>
                                <div class="input-group addon col-9">
                                    <input type="number" name="width" class="form-control style--two"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">cm</div>
                                    </div>
                                </div>
                                @if ($errors->has('width'))
                                    <div class="invalid-input">{{ $errors->first('width') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!--End Shipping Information-->
            <!--Vat & Tax-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Tax Configuration') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (getEcommerceSetting('enable_tax_in_checkout') != config('settings.general_status.active'))
                        <p class="mt-0 font-13">
                            {{ translate('Product tax is disabled. You can enable tax from') }}
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}"
                                class="btn-link">{{ translate('Tax Settings') }}
                            </a>
                        </p>
                    @endif
                    @if (getEcommerceSetting('enable_tax_in_checkout') == config('settings.general_status.active'))
                        <div class="form-row mb-20">
                            <div class="col-sm-4">
                                <label class="font-14 bold black ">{{ translate('Tax Status') }} </label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control select2" name="taxable">
                                    <option value="{{ config('settings.general_status.active') }}">
                                        {{ translate('Taxable') }}</option>
                                    <option value="{{ config('settings.general_status.in_active') }}">
                                        {{ translate('None') }}</option>
                                </select>
                                <p class="mt-1 font-13">
                                    {{ translate('If tax status is none , tax is not appicable of this product') }}
                                </p>
                            </div>

                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-4">
                                <label class="font-14 bold black ">{{ translate('Tax Profile') }} </label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control select2" name="tax_profile">
                                    @foreach ($tax_profiles as $profile)
                                        <option value="{{ $profile->id }}">{{ $profile->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="mt-0 font-13">
                            {{ translate('You can create new tax profile or manage profiles from') }}
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.list') }}" target="_blank"
                                class="btn-link">{{ translate('Tax Module') }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            <!--End Vat & Tax-->
            <!--Featured-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                <input type="checkbox" name="is_featured">
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
                    <div class="card-header bg-white border-bottom2 py-3">
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
                                    <input type="checkbox" name="is_refundable">
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
                <div class="card-header bg-white border-bottom2 py-3">
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
                                <input type="checkbox" name="is_authenthic" checked>
                                <span class="control"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Authentic-->
            <!--Cash on delivery-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Cash On Delivery') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if ($is_active_cod == config('settings.general_status.active'))
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="cash_on_delivery" class="cash-on-delivery" checked
                                        onchange="cashOnDelivery()">
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="cash-on-delivery-info">
                            <div class="form-group mb-4 pt-1">
                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="cod_anywhere"
                                            value="{{ config('tlecommercecore.cod_location.anywhere') }}"
                                            name="cod_location" checked onchange="codLocation()">
                                        <label for="cod_anywhere"></label>
                                    </div>
                                    <label for="cod_anywhere">{{ translate('Anywhere') }}</label>
                                </div>

                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="cod_in_custom_location"
                                            value="{{ config('tlecommercecore.cod_location.custom') }}"
                                            name="cod_location" onchange="codLocation()">
                                        <label for="cod_in_custom_location"></label>
                                    </div>
                                    <label for="cod_in_custom_location">{{ translate('Custom Locations') }}</label>
                                </div>
                            </div>
                            <div class="form-row mb-20 cod-contries_options d-none">
                                <label class="font-14 bold black col-sm-3">{{ translate('Countries') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-countries-select" id="codCountries"
                                        name="cod_selected_countries[]" multiple onchange="clearCodStateOptions()">

                                    </select>
                                    @if ($errors->has('cod_selected_countries'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_countries') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20 cod-states-options d-none">
                                <label class="font-14 bold black col-sm-3">{{ translate('States') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-state-select" id="codStates"
                                        name='cod_selected_states[]' multiple onchange="clearCodCitiesOptions()">
                                    </select>
                                    @if ($errors->has('cod_selected_states'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_states') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20 cod-cities-options d-none">
                                <label class="font-14 bold black col-sm-3">{{ translate('Cities') }} </label>
                                <div class="col-sm-9">
                                    <select class="form-control cod-city-select" id="codCities"
                                        name='cod_selected_cities[]' multiple>
                                    </select>
                                    @if ($errors->has('cod_selected_cities'))
                                        <div class="invalid-input">{{ $errors->first('cod_selected_cities') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="mt-0 font-13">
                            {{ translate('Cash on Delivery in inactive. You can active Cash on Delivery from') }}
                            <a href="{{ route('plugin.tlcommercecore.payments.methods') }}"
                                class="btn-link">{{ translate('Payments Settings') }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            <!--End Cashon delivery-->

            <!--Warranty-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
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
                                    onchange="warrantyConfig()" @if (old('has_warranty')) checked @endif>
                                <span class="control"></span>
                            </label>
                        </div>
                    </div>
                    <div class="warranty-config {{ old('has_warranty') ? '' : 'd-none' }}">
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Replacement Warranty') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" class="replacement-warranty" name="replacement_warranty"
                                        @if (old('replacement_warranty')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black  col-12">{{ translate('Warranty Days') }} </label>
                            <div class="input-group addon col-12">
                                <input type="number" class="form-control style--two" name="warranty_day"
                                    value="{{ old('warranty_day') }}" placeholder="0">
                                <div class="input-group-append">
                                    <div class="input-group-text px-3  bold">{{ translate('Days') }}</div>
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
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Low stock quantity') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row mb-20">
                        <label class="font-14 bold black  col-sm-4">{{ translate('Quantity') }} </label>
                        <input type="number" class="theme-input-style col-sm-8" name="qty_alert"
                            value="{{ old('qty_alert') }}" placeholder="0">
                        @if ($errors->has('qty_alert'))
                            <div class="invalid-input">{{ $errors->first('qty_alert') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <!--End Low stock quantity-->
            <!--Purchase quantity-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Purchase Quantity') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row mb-20">
                        <label class="font-14 bold black  col-sm-4">{{ translate('Minimum Quantity') }} </label>
                        <input type="number" class="theme-input-style col-sm-8" name="min_purchase_qty"
                            value="{{ old('min_purchase_qty') }}" placeholder="0">
                        @if ($errors->has('min_purchase_qty'))
                            <div class="invalid-input">{{ $errors->first('min_purchase_qty') }}</div>
                        @endif
                    </div>
                    <div class="form-row mb-20">
                        <label class="font-14 bold black  col-sm-4">{{ translate('Miximum Quantity') }} </label>
                        <input type="number" class="theme-input-style col-sm-8" value="{{ old('max_purchase_qry') }}"
                            name="max_purchase_qry" placeholder="0">

                        @if ($errors->has('max_purchase_qry'))
                            <div class="invalid-input">{{ $errors->first('max_purchase_qry') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <!--End Purchase quantity-->
            <!--Attatchment-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Attatchment on Purchase') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (getEcommerceSetting('enable_document_in_checkout') == config('settings.general_status.active'))
                        <div class="form-row mb-20">
                            <div class="col-sm-4">
                                <label class="font-14 bold black ">{{ translate('Status') }} </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="is_active_attatchment" class="attatchment-required"
                                        onchange="attatchmentConfig()" @if (old('is_active_attatchment')) checked @endif>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="attatchment-config  {{ old('is_active_attatchment') ? '' : 'd-none' }} ">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-sm-4">{{ translate('Attatchment Title') }} </label>
                                <div class="col-sm-8">
                                    <input type="text" class="theme-input-style"
                                        value="{{ old('attatchment_name') }}" name="attatchment_name"
                                        placeholder="{{ translate('Attatchment Title') }}">
                                </div>
                                @if ($errors->has('attatchment_name'))
                                    <div class="invalid-input">{{ $errors->first('attatchment_name') }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (getEcommerceSetting('enable_document_in_checkout') != config('settings.general_status.active'))
                        <p class="mt-0 font-13">
                            {{ translate('Document in checkout is disabled. You can enable document in checkout from') }}
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'checkout']) }}"
                                class="btn-link">{{ translate('Checkout Settings') }}
                            </a>
                        </p>
                    @else
                        <p class="mt-0 font-13">
                            {{ translate('You can disble document in checkout from') }}
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'checkout']) }}"
                                class="btn-link">{{ translate('Checkout Settings') }}
                            </a>
                        </p>
                    @endif

                </div>
            </div>
            <!--End Attatchment-->
            <!--Collections-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product collections') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (count($product_collections) > 0)
                        @foreach ($product_collections as $collection)
                            <div class="form-row mb-20">
                                <div class="d-flex align-items-center">
                                    <label class="custom-checkbox position-relative mr-2">
                                        <input type="checkbox" id="{{ $collection->id }}" name="product_colletions[]"
                                            value="{{ $collection->id }}"><span class="checkmark"></span>
                                    </label>
                                    <label
                                        for="{{ $collection->id }}">{{ $collection->translation('name', getLocale()) }}</label>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div>
                            <p class="alert alert-danger m-2 col-12">{{ translate('No Collection Avaible') }}</p>

                        </div>
                    @endif
                    <p class="mt-0 font-13">
                        {{ translate('You can create new cllection or manage existing collections from') }}
                        <a href="{{ route('plugin.tlcommercecore.product.collection.list') }}"
                            class="btn-link">{{ translate('Product Collections Module') }}
                        </a>
                    </p>
                </div>
            </div>
            <!--End Collection-->
        </div>
        <!--Ed right side-->
        <!--Form submit area-->
        <div
            class="bottom-button d-flex align-items-center justify-content-sm-end gap-10 flex-wrap justify-content-center">
            <button type="submit" name="status" value="{{ config('settings.general_status.in_active') }}"
                class="btn btn-dark btn-outline-info" tabindex="4">
                {{ translate('Save & Draft') }}
            </button>
            <button type="submit" name="status" value="{{ config('settings.general_status.active') }}"
                class="btn btn-outline-primary" tabindex="4">
                {{ translate('Save & Publish') }}
            </button>
        </div>
        <!--End Form submit area-->
    </form>
    @include('core::base.media.partial.media_modal')
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

            $('.select2').select2({
                theme: "classic",
            });
            /**
             *  product category
             * 
             */
            $('.product-category-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select product category') }}',
                closeOnSelect: false,
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
                $('.product-gallery-images').removeClass('d-none');
                $('.product-colors-select').attr('disabled', true);
                $(".product-colors-select").val(null).trigger("change");
                $('.color-variant-image').closest(".card").addClass('d-none');
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
                    $('.color-variant-image').html(data);
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
                if (selected_items.indexOf(attribute_id) === -1) {
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
