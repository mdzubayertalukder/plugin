@php
    $count = isset($data['tag_count']) && $data['tag_count'] > 0 ? $data['tag_count'] : null;
    $tags = [];
    
    if ($count && isset($data['type'])) {
        $tags = Plugin\PageBuilder\Helpers\BuilderWidgetHelper::getTagsFromBuilder($data['type'], $count);
    }
@endphp
<div class="widget-content tagcloud">
    @foreach ($tags as $tag)
        <a href="{{ route('theme.default.blogByTag', $tag->permalink) }}">{{ $tag->name }}</a>
    @endforeach
</div>
