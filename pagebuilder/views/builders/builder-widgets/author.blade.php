@php
    $active_theme = getActiveTheme();
    $social_options = getThemeOption('social', $active_theme->id);
    $socials = null;
    if (isset($social_options['social_field']) && $social_options['social_field'] != '') {
        $socials = json_decode($social_options['social_field']);
    }

    $author_id = isset($data['author_id']) ? $data['author_id'] : null;
    
    $author = \Core\Models\User::with('info:*')
        ->where('id', $author_id)
        ->first();
    
    $author_name = isset($author->name) ? $author->name : '';
    $author_image = isset($author->image) ? $author->image : null;
    $author_short_desc = isset($author->info->bio) ? $author->info->bio : '';
    $socials = isset($author->info->custom_social) && $author->info->custom_social != 0 && isset($author->info->social) ? json_decode($author->info->social) : $socials;
@endphp
<div class="widget-content">
    <!-- Author Image -->
    @if (isset($author_image))
        <div class="author-image">
            <img data-src="{{ asset(getFilePath($author_image)) }}" alt="author" class="img-fluid lazy">
        </div>
    @endif

    <!-- Author Name -->
    <div class="author-name text-center">
        <span>{{ $author_name }}</span>
    </div>
    <!-- Author Social Links -->
    <div class="author-social text-center">
        @isset($socials)
            @foreach ($socials as $social)
                @if ($social->social_icon != '')
                    @php
                        $logo_url = $social->social_icon_url;
                        if ($social->social_icon_url === '' || $social->social_icon_url === '/') {
                            $logo_url = url('/') . $social->social_icon_url;
                        }
                    @endphp
                    <a href="{{ $logo_url }}" aria-label="social icon"><i class="fa {{ $social->social_icon }}"></i></a>
                @endif
            @endforeach
        @endisset
    </div>
    <!-- Author Text -->
    <div class="author-text text-center">
        {{ front_translate($author_short_desc) }}
    </div>
</div>
