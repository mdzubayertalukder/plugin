{{-- Translated Language --}}
<div class="p-1 lang-message">
    <p class="alert alert-info">You are inserting
        <strong>"{{ getLanguageNameByCode($lang) }}"</strong>
        version
    </p>
</div>
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-tabs nav-fill border-light border-0">
            @php
                $languages = getAllLanguages();
            @endphp
            @foreach ($languages as $key => $language)
                <li class="nav-item">
                    <a class="nav-link lang @if ($language->code == $lang) active @else bg-light @endif py-2" 
                        href="javascript:void(0)"
                        data-lang="{{ $language->code }}"
                        data-widget="{{ $widget }}"
                        >
                        <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}" width="20px"
                            title="{{ $language->name }}">
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
<input type="hidden" name="lang" value="{{ $lang }}">
{{-- Translated Language --}}
