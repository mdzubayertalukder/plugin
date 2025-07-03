@extends('core::base.layouts.master')

@section('title')
{{ translate('Dropshipping Settings') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-gear"></i> {{ translate('Dropshipping Settings') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.dashboard') }}" class="btn btn-outline-primary">
                    <i class="icofont-arrow-left"></i> {{ translate('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4>{{ translate('Plugin Configuration') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.dropshipping.settings.update') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="default_markup_percentage" class="form-label">{{ translate('Default Markup Percentage') }}</label>
                        <input type="number" class="form-control" id="default_markup_percentage" name="default_markup_percentage" value="{{ $settings['default_markup_percentage'] ?? '20' }}" min="0" max="1000" step="0.01">
                        <small class="form-text text-muted">{{ translate('Default markup percentage for imported products') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="auto_sync_enabled" class="form-label">{{ translate('Auto Sync') }}</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_sync_enabled" name="auto_sync_enabled" value="1" {{ ($settings['auto_sync_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_sync_enabled">
                                {{ translate('Enable automatic product synchronization') }}
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sync_frequency" class="form-label">{{ translate('Sync Frequency (hours)') }}</label>
                        <select class="form-control" id="sync_frequency" name="sync_frequency">
                            <option value="1" {{ ($settings['sync_frequency'] ?? '24') == '1' ? 'selected' : '' }}>{{ translate('Every Hour') }}</option>
                            <option value="6" {{ ($settings['sync_frequency'] ?? '24') == '6' ? 'selected' : '' }}>{{ translate('Every 6 Hours') }}</option>
                            <option value="12" {{ ($settings['sync_frequency'] ?? '24') == '12' ? 'selected' : '' }}>{{ translate('Every 12 Hours') }}</option>
                            <option value="24" {{ ($settings['sync_frequency'] ?? '24') == '24' ? 'selected' : '' }}>{{ translate('Daily') }}</option>
                            <option value="168" {{ ($settings['sync_frequency'] ?? '24') == '168' ? 'selected' : '' }}>{{ translate('Weekly') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_products_per_sync" class="form-label">{{ translate('Max Products Per Sync') }}</label>
                        <input type="number" class="form-control" id="max_products_per_sync" name="max_products_per_sync" value="{{ $settings['max_products_per_sync'] ?? '100' }}" min="1" max="1000">
                        <small class="form-text text-muted">{{ translate('Maximum number of products to sync in one batch') }}</small>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enable_logging" name="enable_logging" value="1" {{ ($settings['enable_logging'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_logging">
                                {{ translate('Enable detailed logging') }}
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="icofont-save"></i> {{ translate('Save Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection