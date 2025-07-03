@php
    $short_desc = isset($data['short_desc_t_']) ? $data['short_desc_t_'] : '';
    $email_placeholder = isset($data['email_placeholder_t_']) ? $data['email_placeholder_t_'] : '';
    $btn_text = isset($data['btn_text_t_']) ? $data['btn_text_t_'] : '';

    $active_theme = getActiveTheme();
    $subscribe = getThemeOption('subscribe', $active_theme->id);
    $is_privacy = false;
    if (isset($subscribe['privacy_policy']) && $subscribe['privacy_policy'] == 1 && isset($subscribe['privacy_policy_page'])) {
        $is_privacy = true;
    }
@endphp

<p class="mb-4 font-14">{{ $short_desc }}</p>

<div class="newsletter p-0 m-0 shadow-none bg-transparent">
    <form action="javascript:void(0);" method="post" class="newsletterForm">
        @csrf
        <input type="email" class="form-control" name="email" placeholder="{{ $email_placeholder }}">
        @if ($is_privacy)
            <p class="checkbox-cover d-flex justify-content-center mb-4 font-14">
                <label class="m-0"> {{ front_translate("I've read and accept the") }}
                    @php
                        $tlpage = Core\Models\TlPage::where('id', $subscribe['privacy_policy_page'])
                            ->select(['id', 'permalink', 'title'])
                            ->first();
                        $parentUrl = isset($tlpage) ? getParentUrl($tlpage) : '';
                    @endphp
                    <a
                        href="{{ route('theme.default.viewPage', ['permalink' => $parentUrl . $tlpage->permalink]) }}">{{ $tlpage->translation('title', getFrontLocale()) }}</a>
                    <input type="checkbox">
                    <span class="checkmark"></span>
                </label>
            </p>
        @endif
        <button type="submit" class="btn btn-block btn-default text-center">{{ $btn_text }}</button>
    </form>
</div>
