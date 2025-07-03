@php
    $all_product_categories = getAllProductCategories();
    $all_recent_product_categories = getAllRecentProductCategories(); 
@endphp
<!-- Product Category Menu Item-->
<div data-accordion-tab="toggle">
    <div class="accordion-title d-flex gap-10 align-items-center justify-content-between">
        <h5>{{ translate('Product Categories') }}</h5>
        <i class="icofont-caret-down"></i>
    </div>
    <div class="accordion-content">
        <ul class="nav nav-tabs small-tabs pl-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#product_categories_recent"
                    role="tab">{{ translate('Most Recent') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product_categories_all"
                    role="tab">{{ translate('View All') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product_categories_searched"
                    role="tab">{{ translate('Search') }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="product_categories_recent" role="tabpanel">
                <ul class="item-check-list pages-check-list">
                    @for ($i = 0; $i < sizeof($all_recent_product_categories); $i++)
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox"
                                    id="recent_product_cat_{{ $all_recent_product_categories[$i]->id }}">
                                {{ $all_recent_product_categories[$i]->name }}
                            </label>
                        </li>
                    @endfor
                </ul>
                <p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
                    <span class="list-controls">
                        <input type="checkbox" id="select_recent_product_cat" class="select-all"
                            onclick="selectItemToMenu('#select_recent_product_cat','#product_categories_recent')">
                        <label for="page-tab8" class="cursor-pointer">{{ translate('Select All') }}</label>
                    </span>

                    <span class="add-to-menu">
                        <input type="button" class="submit-add-to-menu" value="Add to Menu"
                            onclick="addItemToMenu('#recent_product_cat_' , {{ json_encode($all_recent_product_categories) }} ,'product_category')">
                    </span>
                </p>
            </div>
            <div class="tab-pane fade" id="product_categories_all" role="tabpanel">
                <ul class="item-check-list pages-check-list">
                    @for ($i = 0; $i < sizeof($all_product_categories); $i++)
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox"
                                    id="all_product_cat_{{ $all_product_categories[$i]->id }}">
                                {{ $all_product_categories[$i]->name }}
                            </label>
                        </li>
                    @endfor
                </ul>
                <p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
                    <span class="list-controls">
                        <input type="checkbox" id="select_all_product_cat" class="select-all"
                            onclick="selectItemToMenu('#select_all_product_cat', '#product_categories_all')">
                        <label for="page-tab8" class="cursor-pointer">{{ translate('Select All ') }}</label>
                    </span>

                    <span class="add-to-menu">
                        <input type="button" class="submit-add-to-menu" value="Add to Menu"
                            onclick="addItemToMenu('#all_product_cat_' , {{ json_encode($all_product_categories) }} ,'product_category')">
                    </span>
                </p>
            </div>
            <div class="tab-pane fade" id="product_categories_searched" role="tabpanel">
                <div class="pt-3">
                    <input type="search" class="theme-input-style" placeholder="Search" id="search_product_category"
                        onkeyup="searchItem('#search_product_category', '#searched_product_category_list' , '{{ route('core.search.product.category.by.keywords') }}')">
                </div>
                <div id="searched_product_category_list">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Product Category Menu Item-->
