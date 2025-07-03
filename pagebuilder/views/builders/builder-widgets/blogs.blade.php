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

<div class="post-blog-list">
    <!-- Post -->
    <div class="row">
        @foreach ($blogs as $blog)
            <div class="{{ isset($data['blog_colum']) ? $data['blog_colum'] : 'col-12' }}">
                @includeIf('theme/default::frontend.includes.blog-styles.' . $data['post_style'], [
                    'blog_excerpt' => 100,
                    'read_more' => isset($data['read_more_t_'])
                        ? front_translate($data['read_more_t_'])
                        : front_translate('Read More'),
                    'blog' => $blog,
                ])
            </div>
        @endforeach
    </div>
    <!-- End of Post -->
</div>
