@php
    $all_product_brands = getAllProductBrands();
    $all_recent_product_brands = getAllRecentProductBrands(); 
@endphp
<!-- Product brand Menu Item-->
<div data-accordion-tab="toggle">
    <div class="accordion-title d-flex gap-10 align-items-center justify-content-between">
        <h5>{{ translate('Product Brands') }}</h5>
        <i class="icofont-caret-down"></i>
    </div>
    <div class="accordion-content">
        <ul class="nav nav-tabs small-tabs pl-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#product_brands_recent"
                    role="tab">{{ translate('Most Recent') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product_brands_all"
                    role="tab">{{ translate('View All') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product_brands_searched"
                    role="tab">{{ translate('Search') }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="product_brands_recent" role="tabpanel">
                <ul class="item-check-list pages-check-list">
                    @for ($i = 0; $i < sizeof($all_recent_product_brands); $i++)
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox"
                                    id="recent_product_brand_{{ $all_recent_product_brands[$i]->id }}">
                                {{ $all_recent_product_brands[$i]->name }}
                            </label>
                        </li>
                    @endfor
                </ul>
                <p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
                    <span class="list-controls">
                        <input type="checkbox" id="select_recent_product_brand" class="select-all"
                            onclick="selectProductMenu('#select_recent_product_brand' , '#product_brands_recent')">
                        <label for="page-tab8" class="cursor-pointer">{{ translate('Select All') }}</label>
                    </span>

                    <span class="add-to-menu">
                        <input type="button" class="submit-add-to-menu" value="Add to Menu"
                            onclick="addItemToMenu('#recent_product_brand_' , {{ json_encode($all_recent_product_brands) }} , 'product_brand')">
                    </span>
                </p>
            </div>
            <div class="tab-pane fade" id="product_brands_all" role="tabpanel">
                <ul class="item-check-list pages-check-list">
                    @for ($i = 0; $i < sizeof($all_product_brands); $i++)
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox"
                                    id="all_product_brand_{{ $all_product_brands[$i]->id }}">
                                {{ $all_product_brands[$i]->name }}
                            </label>
                        </li>
                    @endfor
                </ul>
                <p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
                    <span class="list-controls">
                        <input type="checkbox" id="select_all_product_brand" class="select-all"
                            onclick="selectProductMenu('#select_all_product_brand' ,'#product_brands_all')">
                        <label for="page-tab8" class="cursor-pointer">{{ translate('Select All ') }}</label>
                    </span>

                    <span class="add-to-menu">
                        <input type="button" class="submit-add-to-menu" value="Add to Menu"
                            onclick="addItemToMenu('#all_product_brand_' , {{ json_encode($all_product_brands) }} , 'product_brand')">
                    </span>
                </p>
            </div>
            <div class="tab-pane fade" id="product_brands_searched" role="tabpanel">
                <div class="pt-3">
                    <input type="search" class="theme-input-style" placeholder="Search" id="search_product_brand"
                        onkeyup="searchItem('#search_product_brand', '#searched_product_brand_list' , '{{ route('core.search.product.brand.by.keywords') }}')">
                </div>
                <div id="searched_product_brand_list">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Product brand Menu Item-->