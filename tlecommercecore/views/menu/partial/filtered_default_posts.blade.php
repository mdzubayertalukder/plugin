<!-- Filter Default Posts -->
<ul class="item-check-list posts-check-list">
    @for ($i = 0; $i < sizeof($all_posts); $i++)
        <li>
            <label class="menu-item-title">
                <input type="checkbox" class="menu-item-checkbox"
                    id="searched_post_{{ $all_posts[$i]->id }}">
                {{ $all_posts[$i]->translation('name', getLocale()) }}
            </label>
        </li>
    @endfor
</ul>
<p class="button-controls d-flex justify-content-between gap-10 align-items-center pt-3 border-top2">
    <span class="list-controls">
        <input type="checkbox" id="select_searched_post" class="select-all"
            onclick="selectItemToMenu('#select_searched_post' , '#post_searched')">
        <label for="page-tab8"
            class="cursor-pointer">{{ translate('Select All ') }}</label>
    </span>

    <span class="add-to-menu">
        <input type="button" class="submit-add-to-menu" value="Add to Menu"
            onclick="addItemToMenu('#searched_post_' , {{ json_encode($all_posts) }} , 'post')">
    </span>
</p>
<!-- Filter Default Posts -->