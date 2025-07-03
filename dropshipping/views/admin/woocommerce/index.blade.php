@extends('core::base.layouts.master')

@section('title')
{{ translate('WooCommerce Configurations') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-shop"></i> {{ translate('WooCommerce Configurations') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.woocommerce.create') }}" class="btn long">
                    <i class="icofont-plus"></i> {{ translate('Add WooCommerce Store') }}
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4>{{ translate('All WooCommerce Stores') }}</h4>
            </div>
            <div class="card-body">
                @if($configs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }}</th>
                                <th>{{ translate('Store URL') }}</th>
                                <th>{{ translate('Description') }}</th>
                                <th>{{ translate('Products') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Created') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($configs as $index => $config)
                            <tr>
                                <td>{{ $configs->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $config->name }}</strong>
                                </td>
                                <td>
                                    <a href="{{ $config->store_url }}" target="_blank" class="text-decoration-none">
                                        {{ Str::limit($config->store_url, 40) }}
                                        <i class="icofont-external-link ml-1"></i>
                                    </a>
                                </td>
                                <td>
                                    {{ Str::limit($config->description ?? 'No description', 50) }}
                                </td>
                                <td>
                                    <span class="badge badge-info badge-pill">
                                        {{ $config->total_products ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($config->is_active)
                                    <span class="badge badge-success">
                                        <i class="icofont-check"></i> {{ translate('Active') }}
                                    </span>
                                    @else
                                    <span class="badge badge-danger">
                                        <i class="icofont-close"></i> {{ translate('Inactive') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($config->created_at)
                                        {{ date('M d, Y', strtotime($config->created_at)) }}
                                        @else
                                        N/A
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                            {{ translate('Actions') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.dropshipping.woocommerce.edit', $config->id) }}">
                                                <i class="icofont-edit text-primary"></i> {{ translate('Edit') }}
                                            </a>

                                            <button type="button" class="dropdown-item" onclick="testConnection({{ $config->id }})">
                                                <i class="icofont-connection text-info"></i> {{ translate('Test Connection') }}
                                            </button>

                                            <form action="{{ route('admin.dropshipping.woocommerce.sync', $config->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="dropdown-item" onclick="return confirm('{{ translate('Are you sure you want to sync products? This may take a while.') }}')">
                                                    <i class="icofont-refresh text-success"></i> {{ translate('Sync Products') }}
                                                </button>
                                            </form>

                                            <div class="dropdown-divider"></div>

                                            <form action="{{ route('admin.dropshipping.woocommerce.destroy', $config->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ translate('Are you sure you want to delete this configuration? This will also delete all associated products.') }}')">
                                                    <i class="icofont-trash"></i> {{ translate('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $configs->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="icofont-shop fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">{{ translate('No WooCommerce stores configured') }}</h4>
                    <p class="text-muted mb-4">{{ translate('Configure your first WooCommerce store to start importing products for dropshipping') }}</p>
                    <a href="{{ route('admin.dropshipping.woocommerce.create') }}" class="btn btn-primary btn-lg">
                        <i class="icofont-plus"></i> {{ translate('Add WooCommerce Store') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Test Connection Modal --}}
<div class="modal fade" id="testConnectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Testing Connection') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="connectionLoader">
                    <i class="icofont-spinner-alt-4 fa-spin fa-2x text-primary mb-3"></i>
                    <p>{{ translate('Testing WooCommerce API connection...') }}</p>
                </div>
                <div id="connectionResult" style="display: none;">
                    <div id="connectionSuccess" style="display: none;">
                        <i class="icofont-check-circled fa-2x text-success mb-3"></i>
                        <h5 class="text-success">{{ translate('Connection Successful!') }}</h5>
                        <div id="storeInfo"></div>
                    </div>
                    <div id="connectionError" style="display: none;">
                        <i class="icofont-close-circled fa-2x text-danger mb-3"></i>
                        <h5 class="text-danger">{{ translate('Connection Failed') }}</h5>
                        <div id="errorMessage" class="alert alert-danger"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script>
    function testConnection(configId) {
        // Find the config data from the page
        const config = @json($configs);
        const configData = config.data.find(c => c.id === configId);

        if (!configData) {
            alert('Configuration not found');
            return;
        }

        // Show modal
        $('#testConnectionModal').modal('show');
        $('#connectionLoader').show();
        $('#connectionResult').hide();

        // Make AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.post('{{ route("admin.dropshipping.woocommerce.test") }}', {
                store_url: configData.store_url,
                consumer_key: configData.consumer_key,
                consumer_secret: configData.consumer_secret
            })
            .done(function(response) {
                $('#connectionLoader').hide();
                $('#connectionResult').show();

                if (response.success) {
                    $('#connectionSuccess').show();
                    $('#connectionError').hide();

                    if (response.store_info) {
                        $('#storeInfo').html(`
                    <div class="card mt-3">
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
                    $('#connectionSuccess').hide();
                    $('#connectionError').show();
                    $('#errorMessage').text(response.message || '{{ translate("Unknown error occurred") }}');
                }
            })
            .fail(function(xhr) {
                $('#connectionLoader').hide();
                $('#connectionResult').show();
                $('#connectionSuccess').hide();
                $('#connectionError').show();

                let errorMsg = '{{ translate("Network error occurred") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#errorMessage').text(errorMsg);
            });
    }
</script>
@endsection