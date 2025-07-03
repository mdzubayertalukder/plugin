<input type="hidden" name="key" value="{{ $key }}" id="slide-key">
<div class="form-group mb-2">
    <label for="title" class="black">{{ translate('Title') }}</label>
    <input type="text" name="title" id="title" class="form-control" placeholder="{{ translate('Title') }}" value="{{ isset($details['title']) ? $details['title'] : '' }}">
    <span class="text-danger font-14 title-feedback d-none">{{ translate('Please choose a Title.') }}</span>
</div>
<div class="form-group mb-2">
    <label for="url" class="black">{{ translate('Url') }}</label>
    <input type="text" name="url" id="url" class="form-control" placeholder="{{ translate('Url') }}" value="{{ isset($details['url']) ? $details['url'] : '' }}">
    <span class="text-danger font-14 url-feedback d-none">{{ translate('Please choose an Url.') }}</span>
</div>
<div class="form-row mb-2">
    <div class="col-6">
        <label class="font-14 bold black">{{ translate('Desktop Image') }}</label>
        <div class="d-block">
            @include('core::base.includes.media.media_input', [
                'input' => 'desktop_image',
                'data' => isset($details['desktop_image']['id']) ? $details['desktop_image']['id'] : null,
            ])
        </div>
        <span class="text-danger font-14 desktop_image_id-feedback d-none">{{ translate('Please choose a Desktop Image.') }}</span>
    </div>
    <div class="col-6">
        <label class="font-14 bold black">{{ translate('Mobile Image') }}</label>
        <div class="d-block">
            @include('core::base.includes.media.media_input', [
                'input' => 'mobile_image',
                'data' => isset($details['mobile_image']['id']) ? $details['mobile_image']['id'] : null,
            ])
        </div>
        <span class="text-danger font-14 mobile_image_id-feedback d-none"> {{ translate('Please choose a Mobile Image.') }}</span>
    </div>
</div>