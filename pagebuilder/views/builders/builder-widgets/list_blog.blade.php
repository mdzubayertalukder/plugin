@php
    $limit = $data['count'];
    
    switch ($data['type']) {
        case 'recent':
            $blogs = frontendSidebarRecentBlogs($limit);
            break;
        case 'featured':
            $blogs = frontendSidebarFeaturedBlogs($limit);
            break;
        case 'view':
            $blogs = mostViewedBlogs($limit);
            break;
        case 'popular':
            $blogs = mostPopularBlogs($limit);
            break;
        case 'comment':
            $blogs = frontendSidebarMostCommentBlogs($limit);
            break;
        case 'category':
            $blogs = blogsByCategory($data['category'], $limit);
            break;
        default:
            $blogs = 0;
            break;
    }
@endphp
<div class="widget-content">
    {{-- Foreach loop for Recent Post start --}}
    @foreach ($blogs as $blog)
        <!-- Single Post -->
        <div class="wrp-cover">
            <!-- Post Thumbnail -->
            <div class="post-thumb">
                <a href="{{ route('theme.default.blog_details', $blog->permalink) }}">
                    @php
                        $variation = getImageVariation($blog->image, 'small');
                    @endphp
                    <img data-src="{{ $variation }}" alt="{{ $blog->name }}" class="img-small-60 lazy">
                </a>
            </div>
            <!-- Post Title -->
            <div class="post-title">
                <a href="{{ route('theme.default.blog_details', $blog->permalink) }}">{{ $blog->name }}</a>
            </div>
        </div>
        <!--End of Single Post -->
    @endforeach
    {{-- Foreach loop for Recent Post end --}}
</div>
