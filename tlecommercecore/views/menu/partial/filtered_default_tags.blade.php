<!-- Filter Default Tags -->
<ul class="item-check-list tags-check-list">
    @for ($i = 0; $i < sizeof($all_tags); $i++)
        <li>
            <label class="menu-item-title">
                <input type="checkbox" class="menu-item-checkbox"
                    id="searched_tag_{{ $all_tags[$i]->id }}">
                {{ $all_tags[$i]->translation('name', getLocale()) }}
            </label>
        </li>
    @endfor
</ul>
<p
    class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
    <span class="list-controls">
        <input type="checkbox" id="select_searched_tag" class="select-all"
            onclick="selectItemToMenu('#select_searched_tag' , '#tag_searched')">
        <label for="tag-tab8"
            class="cursor-pointer">{{ translate('Select All ') }}</label>
    </span>

    <span class="add-to-menu">
        <input type="button" class="submit-add-to-menu" value="Add to Menu"
            onclick="addItemToMenu('#searched_tag_', {{ json_encode($all_tags) }} , 'tag')">
    </span>
</p>
<!-- Filter Default Tags -->