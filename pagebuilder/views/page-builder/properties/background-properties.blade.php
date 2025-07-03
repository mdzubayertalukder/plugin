<div class="tab-pane fade" id="background" role="tabpanel" aria-labelledby="background-tab">
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black">{{ translate('Background Color') }} </label>
        </div>
        <div class="col-sm-12">
            <div class="input-group addon">
                <input type="text" name="background_color" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($properties['background_color']) ? $properties['background_color'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        id="colorPicker" value="{{ isset($properties['background_color']) ? $properties['background_color'] : '#000000' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black">{{ translate('Background Image') }} </label>
        </div>
        <div class="col-md-12">
            @include('core::base.includes.media.media_input', [
                'input' => 'background_image',
                'data' => isset($properties['background_image']) ? $properties['background_image'] : null,
            ])
        </div>
    </div>
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black"> {{ translate('Background Size') }} </label>
        </div>
        <div class="col-sm-12">
            <select class="theme-input-style" name="background_size">
                <option value="">Select</option>
                <option value="cover" @selected(isset($properties['background_size']) && $properties['background_size'] == 'cover')>cover</option>
                <option value="auto" @selected(isset($properties['background_size']) && $properties['background_size'] == 'auto')>auto</option>
                <option value="contain" @selected(isset($properties['background_size']) && $properties['background_size'] == 'contain')>contain</option>
                <option value="initial" @selected(isset($properties['background_size']) && $properties['background_size'] == 'initial')>initial</option>
                <option value="inherit" @selected(isset($properties['background_size']) && $properties['background_size'] == 'inherit')>inherit</option>
                <option value="unset" @selected(isset($properties['background_size']) && $properties['background_size'] == 'unset')>unset</option>
            </select>
        </div>
    </div>
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black"> {{ translate('Background Position') }} </label>
        </div>
        <div class="col-sm-12">
            <select class="theme-input-style" name="background_position">
                <option value="">Select</option>
                <option value="bottom" @selected(isset($properties['background_position']) && $properties['background_position'] == 'bottom')>bottom</option>
                <option value="center" @selected(isset($properties['background_position']) && $properties['background_position'] == 'center')>center</option>
                <option value="inherit" @selected(isset($properties['background_position']) && $properties['background_position'] == 'inherit')>inherit</option>
                <option value="initial" @selected(isset($properties['background_position']) && $properties['background_position'] == 'initial')>initial</option>
                <option value="left" @selected(isset($properties['background_position']) && $properties['background_position'] == 'left')>left</option>
                <option value="right" @selected(isset($properties['background_position']) && $properties['background_position'] == 'right')>right</option>
                <option value="top" @selected(isset($properties['background_position']) && $properties['background_position'] == 'top')>top</option>
                <option value="unset" @selected(isset($properties['background_position']) && $properties['background_position'] == 'unset')>unset</option>
            </select>
        </div>
    </div>
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black"> {{ translate('Background Repeat') }} </label>
        </div>
        <div class="col-sm-12">
            <select class="theme-input-style" name="background_repeat">
                <option value="">Select</option>
                <option value="no-repeat" @selected(isset($properties['background_repeat']) && $properties['background_repeat'] == 'no-repeat')>no-repeat</option>
                <option value="repeat" @selected(isset($properties['background_repeat']) && $properties['background_repeat'] == 'repeat')>repeat</option>
            </select>
        </div>
    </div>
</div>