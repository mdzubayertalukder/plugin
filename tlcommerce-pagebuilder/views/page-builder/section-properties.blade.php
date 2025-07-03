<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="background-tab" data-toggle="tab" href="#background" role="tab"
            aria-controls="background" aria-selected="false">{{ translate('Background') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="button"
            aria-selected="false">{{ translate('Advanced') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">

    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">

        <!-- Section Width -->
        <div class="form-group">
            <label for="container" class="d-block mb-2 font-14 bold black">{{ translate('Section Width') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary sm {{ isset($section_properties['container']) && $section_properties['container'] == 'container' ? 'active':'' }}">
                    <input type="radio" class="d-none" name="container" id="container" value="container"
                        @checked(isset($section_properties['container']) && $section_properties['container'] == 'container')>
                    {{ translate('Container') }}
                </label>
                <label class="btn btn-primary sm {{ isset($section_properties['container']) && $section_properties['container'] == 'container-fluid' ? 'active':'' }}">
                    <input type="radio"class="d-none" name="container" id="container-fluid" value="container-fluid"
                        @checked(isset($section_properties['container']) && $section_properties['container'] == 'container-fluid')>
                    {{ translate('Container Fluid') }}
                </label>
            </div>
            <i class="fa fa-arrows-v"></i>
        </div>
        <!-- Section Width -->

        <!-- Vertical Alignment -->
        <div class="form-group">
            <label for="vertical" class="d-block mb-2 font-14 bold black">{{ translate('Vertical Alignment') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary sm {{ isset($section_properties['vertical']) && $section_properties['vertical'] == 'start' ? 'active':'' }}">
                    <input type="radio" class="d-none" name="vertical" id="start" value="start"
                        @checked(isset($section_properties['vertical']) && $section_properties['vertical'] == 'start')>
                    {{ translate('Top') }}
                </label>
                <label class="btn btn-primary sm {{ isset($section_properties['vertical']) && $section_properties['vertical'] == 'center' ? 'active':'' }}">
                    <input type="radio"class="d-none" name="vertical" id="center" value="center"
                        @checked(isset($section_properties['vertical']) && $section_properties['vertical'] == 'center')>
                    {{ translate('Center') }}
                </label>
                <label class="btn btn-primary sm {{ isset($section_properties['vertical']) && $section_properties['vertical'] == 'end' ? 'active':'' }}">
                    <input type="radio"class="d-none" name="vertical" id="end" value="end"
                        @checked(isset($section_properties['vertical']) && $section_properties['vertical'] == 'end')>
                    {{ translate('Bottom') }}
                </label>
            </div>
            <i class="fa fa-arrows-v"></i>
        </div>
        <!-- Vertical Alignment -->
        
    </div>

    <!-- Include background Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.background-properties', ['properties' => $section_properties])

    <!-- Include Advance Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.advance-properties',['properties' => $section_properties])
</div>
