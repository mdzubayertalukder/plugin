@php
    $sidebarCategories = frontendSidebarCategories();
    
    function listcategory($categories, $markup, $label)
    {
        $label++;
        foreach ($categories as $cat) {
            $spacer = '';
            for ($i = 0; $i < $label; $i++) {
                $spacer .= '-';
            }
            $markup .= '<li class="list-item py-1"> <a href="' . route('theme.default.blogByCategory', $cat->permalink) . '">' . $spacer . ' ' . $cat->name . '</a></li>';
    
            if (count($cat->active_childs)) {
                listcategory($category->active_childs, $markup, $label);
            }
        }
        return $markup;
    }
@endphp
@if (isset($data['type']))
    <!-- Dropdown Category -->
    @if ($data['type'] == 'dropdown')
        <select name="category" class="form-control p-0" id="category_field">
            <option value="" selected>{{ isset($data['placeholder_t_']) ? $data['placeholder_t_'] : '' }}</option>
            @foreach ($sidebarCategories as $category)
                <option value="{{ $category->permalink }}">
                    {{ $category->name }}
                </option>
                @if (count($category->active_childs))
                    @include('theme/default::frontend.includes.blog_child_category', [
                        'child_category' => $category->active_childs,
                        'label' => 1,
                        'parent' => null,
                        'permalink' => true,
                        'active_childs' => true,
                    ])
                @endif
            @endforeach
        </select>
    @else
        <ul class="list">
            @foreach ($sidebarCategories as $category)
                <li class="list-item py-1"><a
                        href="{{ route('theme.default.blogByCategory', $category->permalink) }}">{{ $category->name }}</a>
                </li>
                @if (count($category->active_childs))
                    {!! listcategory($category->active_childs, '', 1) !!}
                @endif
            @endforeach
        </ul>
    @endif

@endif
