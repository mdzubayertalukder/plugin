<!-- Filter Product categories -->
<ul class="item-check-list product-category-check-list">
    @for ($i = 0; $i < sizeof($all_product_categories); $i++)
        <li>
            <label class="menu-item-title">
                <input type="checkbox" class="menu-item-checkbox" id="searched_product_cat_{{ $all_product_categories[$i]->id }}">
                {{ $all_product_categories[$i]->name }}
            </label>
        </li>
    @endfor
</ul>
<p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
    <span class="list-controls">
        <input type="checkbox" id="select_searched_product_cat" class="select-all" onclick="selectItemToMenu('#select_searched_product_cat', '#product_categories_searched')">
        <label for="page-tab8" class="cursor-pointer">{{ translate('Select All ') }}</label>
    </span>

    <span class="add-to-menu">
        <input type="button" class="submit-add-to-menu" value="Add to Menu" onclick="addItemToMenu('#searched_product_cat_' , {{ json_encode($all_product_categories) }} ,'product_category')">
    </span>
</p>
<!-- Filter Product categories -->