@php
$label = $label + 1;
@endphp
@foreach ($child_category as $child)
    @if (isset($selected_categories))
        <option value="{{ isset($permalink)?$child->permalink:$child->id }}"
            {{ $selected_categories->contains('category_id', $child->id) ? 'selected' : '' }}>
            @for ($i = 0; $i < $label; $i++)
                -
            @endfor{{ $child->translation('name', getLocale()) }}
        </option>
        @if (count($child->childs))
            @include('plugin/tlecommercecore::products.categories.child_category', [
                'child_category' => $child->childs,
                'label' => $label,
                'selected_categories' => $selected_categories,
            ])
        @endif
    @else
        <option value="{{ isset($permalink)?$child->permalink:$child->id }}" {{ $parent == $child->id ? 'selected' : '' }}>
            @for ($i = 0; $i < $label; $i++)
                -
            @endfor{{ $child->translation('name', getLocale()) }}
        </option>
        @if (count($child->childs))
            @include('plugin/tlecommercecore::products.categories.child_category', [
                'child_category' => $child->childs,
                'label' => $label,
            ])
        @endif
    @endif
@endforeach
