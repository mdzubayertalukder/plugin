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
<div class="widget-content justify-content-start">
    @foreach ($blogs as $blog)
        <!-- Single Post -->
        <div class="{{ isset($data['blog_colum']) ? $data['blog_colum'] : 'col-12' }}">
            <div class="featured-post {{ $loop->last ? '' : 'mb-2 mb-sm-4' }} ">
                <!-- Post Thumbnail -->
                @isset($blog->image)
                    <a href="{{ route('theme.default.blog_details', $blog->permalink) }}">
                        @php
                            $variation = getImageVariation($blog->image, 'medium');
                        @endphp
                        <img data-src="{{ $variation }}" alt="{{ $blog->name }}" class="img-fluid lazy">
                    </a>
                @endisset
                <!-- Post Title -->
                <div class="featured-post-title">
                    <h5> <a href="{{ route('theme.default.blog_details', $blog->permalink) }}">{{ $blog->name }}</a>
                    </h5>
                </div>
            </div>
        </div>
        <!-- End of Single Post -->
    @endforeach
</div>
