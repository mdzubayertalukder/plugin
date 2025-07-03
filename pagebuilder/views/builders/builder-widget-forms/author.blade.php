@php
    $users = \Core\Models\User::where('status', config('settings.general_status.active'))->select('id','name')->get();
@endphp
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
    
    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        <!-- Author Select -->
        <div class="form-group">
            <label for="author_id" class="font-14 bold black">{{ translate('Select a User for Authur Widget') }}</label>
            <select class="form-control mt-1" name="author_id" id="author_id">
                @foreach ( $users as $user)
                    <option value="{{ $user->id }}" @selected(isset($widget_properties['author_id']) && $widget_properties['author_id'] == $user->id)>{{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Include background Properties -->
    @include('plugin/pagebuilder::page-builder.properties.background-properties', ['properties' => $widget_properties])

    <!-- Include Advance Properties -->
    @include('plugin/pagebuilder::page-builder.properties.advance-properties', ['properties' => $widget_properties])
</div>
