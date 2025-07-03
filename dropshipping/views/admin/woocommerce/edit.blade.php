@extends('core::base.layouts.master')

@section('title')
{{ translate('Edit WooCommerce Store') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-edit"></i> {{ translate('Edit WooCommerce Store') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.woocommerce.index') }}" class="btn btn-outline-primary">
                    <i class="icofont-arrow-left"></i> {{ translate('Back to List') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4>{{ translate('WooCommerce Store Configuration') }}</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.dropshipping.woocommerce.update', $config->id) }}" method="POST" id="woocommerceForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label required">{{ translate('Store Name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $config->name) }}" required>
                        <small class="form-text text-muted">{{ translate('A friendly name to identify this store') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">{{ translate('Description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $config->description) }}</textarea>
                        <small class="form-text text-muted">{{ translate('Optional description for this store') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="store_url" class="form-label required">{{ translate('Store URL') }}</label>
                        <input type="url" class="form-control" id="store_url" name="store_url" value="{{ old('store_url', $config->store_url) }}" required placeholder="https://yourstore.com">
                        <small class="form-text text-muted">{{ translate('The complete URL to your WooCommerce store') }}</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consumer_key" class="form-label required">{{ translate('Consumer Key') }}</label>
                                <input type="text" class="form-control" id="consumer_key" name="consumer_key" value="{{ old('consumer_key', $config->consumer_key) }}" required>
                                <small class="form-text text-muted">{{ translate('WooCommerce REST API Consumer Key') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consumer_secret" class="form-label required">{{ translate('Consumer Secret') }}</label>
                                <input type="password" class="form-control" id="consumer_secret" name="consumer_secret" value="{{ old('consumer_secret', $config->consumer_secret) }}" required>
                                <small class="form-text text-muted">{{ translate('WooCommerce REST API Consumer Secret') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $config->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ translate('Active') }}
                            </label>
                            <small class="form-text text-muted">{{ translate('Enable this store for product syncing') }}</small>
                        </div>
                    </div>

                    @if($config->created_at)
                    <div class="alert alert-info">
                        <i class="icofont-info-circle"></i>
                        {{ translate('Created on') }}: {{ date('F d, Y \a\t H:i', strtotime($config->created_at)) }}
                        @if($config->last_sync_at)
                        <br>{{ translate('Last synced') }}: {{ date('F d, Y \a\t H:i', strtotime($config->last_sync_at)) }}
                        @endif
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info btn-block" onclick="testConnection()">
                                <i class="icofont-connection"></i> {{ translate('Test Connection') }}
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="icofont-save"></i> {{ translate('Update Configuration') }}
                            </button>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('admin.dropshipping.woocommerce.destroy', $config->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('{{ translate('Are you sure you want to delete this configuration?') }}')">
                                    <i class="icofont-trash"></i> {{ translate('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Test Connection Result Modal --}}
<div class="modal fade" id="testResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Connection Test Result') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="testLoader">
                    <i class="icofont-spinner-alt-4 fa-spin fa-2x text-primary mb-3"></i>
                    <p>{{ translate('Testing WooCommerce API connection...') }}</p>
                </div>
                <div id="testResult" style="display: none;">
                    <div id="testSuccess" style="display: none;">
                        <i class="icofont-check-circled fa-3x text-success mb-3"></i>
                        <h4 class="text-success">{{ translate('Connection Successful!') }}</h4>
                        <div id="storeDetails" class="mt-3"></div>
                    </div>
                    <div id="testError" style="display: none;">
                        <i class="icofont-close-circled fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">{{ translate('Connection Failed') }}</h4>
                        <div id="errorDetails" class="alert alert-danger mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Store Statistics (if available) --}}
@if(isset($config->products_count) || isset($config->last_sync_at))
<div class="row mt-4">
    <div class="col-md-8 offset-md-2">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h6 class="m-0"><i class="icofont-chart-bar-graph"></i> {{ translate('Store Statistics') }}</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-primary">{{ $config->products_count ?? 0 }}</h4>
                        <small class="text-muted">{{ translate('Products Synced') }}</small>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-success">{{ $config->is_active ? translate('Active') : translate('Inactive') }}</h4>
                        <small class="text-muted">{{ translate('Status') }}</small>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-info">
                            @if($config->last_sync_at)
                            {{ date('M d', strtotime($config->last_sync_at)) }}
                            @else
                            {{ translate('Never') }}
                            @endif
                        </h4>
                        <small class="text-muted">{{ translate('Last Sync') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Help Information --}}
<div class="row mt-4">
    <div class="col-md-8 offset-md-2">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="m-0"><i class="icofont-info-circle"></i> {{ translate('How to get WooCommerce API Keys') }}</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>{{ translate('Log into your WooCommerce admin dashboard') }}</li>
                    <li>{{ translate('Go to WooCommerce → Settings → Advanced → REST API') }}</li>
                    <li>{{ translate('Click "Add Key" to create new API credentials') }}</li>
                    <li>{{ translate('Set permissions to "Read/Write" and save') }}</li>
                    <li>{{ translate('Copy the Consumer Key and Consumer Secret') }}</li>
                </ol>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="icofont-warning"></i>
                    {{ translate('Make sure your WooCommerce store has SSL enabled (HTTPS) for secure API communication.') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script>
    function testConnection() {
        const storeUrl = $('#store_url').val();
        const consumerKey = $('#consumer_key').val();
        const consumerSecret = $('#consumer_secret').val();

        if (!storeUrl || !consumerKey || !consumerSecret) {
            alert('{{ translate("Please fill in all required fields before testing connection") }}');
            return;
        }

        // Show modal
        $('#testResultModal').modal('show');
        $('#testLoader').show();
        $('#testResult').hide();

        // Setup CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Make AJAX request
        $.post('{{ route("admin.dropshipping.woocommerce.test") }}', {
                store_url: storeUrl,
                consumer_key: consumerKey,
                consumer_secret: consumerSecret
            })
            .done(function(response) {
                $('#testLoader').hide();
                $('#testResult').show();

                if (response.success) {
                    $('#testSuccess').show();
                    $('#testError').hide();

                    if (response.store_info) {
                        $('#storeDetails').html(`
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${response.store_info.name || 'Store'}</h6>
                                    <p class="card-text small">
                                        <strong>{{ translate('Version') }}:</strong> ${response.store_info.version || 'N/A'}<br>
                                        <strong>{{ translate('Products') }}:</strong> ${response.store_info.products_count || 0}
                                    </p>
                                </div>
                            </div>
                        `);
                    }
                } else {
                    $('#testSuccess').hide();
                    $('#testError').show();
                    $('#errorDetails').text(response.message || '{{ translate("Unknown error occurred") }}');
                }
            })
            .fail(function(xhr) {
                $('#testLoader').hide();
                $('#testResult').show();
                $('#testSuccess').hide();
                $('#testError').show();

                let errorMsg = '{{ translate("Network error occurred") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#errorDetails').text(errorMsg);
            });
    }
</script>
@endsection