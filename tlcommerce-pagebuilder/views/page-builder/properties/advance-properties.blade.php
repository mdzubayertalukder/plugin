<div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
    <ul class="nav nav-tabs mb-20" id="advanceTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="desktop-advance-tab" data-toggle="tab" href="#desktop-advance" role="tab" aria-controls="desktop-advance"
                aria-selected="false">{{ translate('Desktop') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-advance-tab" data-toggle="tab" href="#tab-advance" role="tab"
                aria-controls="tab-advance" aria-selected="false">{{ translate('Tab') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="mobile-advance-tab" data-toggle="tab" href="#mobile-advance" role="tab"
                aria-controls="mobile-advance" aria-selected="true">{{ translate('Mobile') }}</a>
        </li>
    </ul>
    <div class="tab-content" id="advanceTabContent">
    
        <div class="tab-pane fade show active" id="desktop-advance" role="tabpanel" aria-labelledby="desktop-advance-tab">
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Padding') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="padding_top" 
                            placeholder="00" value="{{ isset($properties['padding_top']) ? $properties['padding_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="padding_right" 
                            placeholder="00" value="{{ isset($properties['padding_right']) ? $properties['padding_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="padding_bottom" 
                            placeholder="00"  value="{{ isset($properties['padding_bottom']) ? $properties['padding_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="padding_left" 
                            placeholder="00" value="{{ isset($properties['padding_left']) ? $properties['padding_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Margin') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="margin_top" 
                            placeholder="00" value="{{ isset($properties['margin_top']) ? $properties['margin_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="margin_right" 
                            placeholder="00" value="{{ isset($properties['margin_right']) ? $properties['margin_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="margin_bottom" 
                            placeholder="00" value="{{ isset($properties['margin_bottom']) ? $properties['margin_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="margin_left" 
                            placeholder="00" value="{{ isset($properties['margin_left']) ? $properties['margin_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-advance" role="tabpanel" aria-labelledby="tab-advance-tab">
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Padding') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_padding_top" 
                            placeholder="00" value="{{ isset($properties['tab_padding_top']) ? $properties['tab_padding_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_padding_right" 
                            placeholder="00" value="{{ isset($properties['tab_padding_right']) ? $properties['tab_padding_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_padding_bottom" 
                            placeholder="00" value="{{ isset($properties['tab_padding_bottom']) ? $properties['tab_padding_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_padding_left" 
                            placeholder="00" value="{{ isset($properties['tab_padding_left']) ? $properties['tab_padding_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Margin') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_margin_top" 
                            placeholder="00" value="{{ isset($properties['tab_margin_top']) ? $properties['tab_margin_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_margin_right" 
                            placeholder="00" value="{{ isset($properties['tab_margin_right']) ? $properties['tab_margin_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_margin_bottom" 
                            placeholder="00" value="{{ isset($properties['tab_margin_bottom']) ? $properties['tab_margin_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="tab_margin_left" 
                            placeholder="00" value="{{ isset($properties['tab_margin_left']) ? $properties['tab_margin_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="mobile-advance" role="tabpanel" aria-labelledby="mobile-advance-tab">
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Padding') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_padding_top" 
                            placeholder="00"  value="{{ isset($properties['mobile_padding_top']) ? $properties['mobile_padding_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_padding_right" 
                            placeholder="00"  value="{{ isset($properties['mobile_padding_right']) ? $properties['mobile_padding_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_padding_bottom" 
                            placeholder="00" value="{{ isset($properties['mobile_padding_bottom']) ? $properties['mobile_padding_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_padding_left" 
                            placeholder="00" value="{{ isset($properties['mobile_padding_left']) ? $properties['mobile_padding_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        
            <div class="form-row mb-20">
                <div class="col-sm-12">
                    <label class="font-14 bold black">{{ translate('Margin') }} </label>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_margin_top" 
                            placeholder="00" value="{{ isset($properties['mobile_margin_top']) ? $properties['mobile_margin_top'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Top') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_margin_right" 
                            placeholder="00" value="{{ isset($properties['mobile_margin_right']) ? $properties['mobile_margin_right'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Right') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_margin_bottom" 
                            placeholder="00" value="{{ isset($properties['mobile_margin_bottom']) ? $properties['mobile_margin_bottom'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Bottom') }}</small>
                </div>
                <div class="col-sm-3">
                    <div class="input-group addon">
                        <input type="number" class="form-control radius-0" name="mobile_margin_left" 
                            placeholder="00" value="{{ isset($properties['mobile_margin_left']) ? $properties['mobile_margin_left'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                    <small>{{ translate('Left') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
