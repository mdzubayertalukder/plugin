<!-- Filter Default Pages -->
<ul class="item-check-list pages-check-list">
    @for ($i = 0; $i < sizeof($all_pages); $i++)
        <li>
            <label class="menu-item-title">
                <input type="checkbox" class="menu-item-checkbox"
                    id="searched_page_{{ $all_pages[$i]->id }}">
                {{ $all_pages[$i]->translation('name', getLocale()) }}
            </label>
        </li>
    @endfor
</ul>
<p
    class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
    <span class="list-controls">
        <input type="checkbox" id="select_searched_page" class="select-all"
            onclick="selectItemToMenu('#select_searched_page' , '#page_searched')">
        <label for="page-tab8"
            class="cursor-pointer">{{ translate('Select All ') }}</label>
    </span>

    <span class="add-to-menu">
        <input type="button" class="submit-add-to-menu" value="Add to Menu"
            onclick="addItemToMenu('#searched_page_', {{ json_encode($all_pages) }} , 'page')">
    </span>
</p>
<!-- Filter Default Pages -->