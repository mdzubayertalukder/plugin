@extends('core::base.layouts.master')

@section('title')
{{ translate('WooCommerce Configurations') }}
@endsection

@section('custom_css')
<style>
    .woo-config-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        transition: all 0.3s;
    }

    .woo-config-card:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    }

    .status-badge {
        font-size: 0.75rem;
    }
</style>
@endsection

@section('main_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ translate('WooCommerce Configurations') }}</h5>
                <a href="{{ route('core.dropshipping.admin.woocommerce.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ translate('Add New Configuration') }}
                </a>
            </div>
            <div class="card-body">
                @if($configs->count() > 0)
                <div class="row">
                    @foreach($configs as $config)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="woo-config-card p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $config->name }}</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('core.dropshipping.admin.woocommerce.edit', $config->id) }}">
                                            <i class="fa fa-edit"></i> {{ translate('Edit') }}
                                        </a>
                                        <button class="dropdown-item test-connection" data-id="{{ $config->id }}">
                                            <i class="fa fa-plug"></i> {{ translate('Test Connection') }}
                                        </button>
                                        <button class="dropdown-item sync-products" data-id="{{ $config->id }}">
                                            <i class="fa fa-sync"></i> {{ translate('Sync Products') }}
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger delete-config" data-id="{{ $config->id }}">
                                            <i class="fa fa-trash"></i> {{ translate('Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">{{ $config->description }}</small>
                            </div>

                            <div class="mb-2">
                                <span class="badge {{ $config->is_active ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $config->is_active ? translate('Active') : translate('Inactive') }}
                                </span>
                                {!! $config->sync_status_badge !!}
                            </div>

                            <div class="small text-muted">
                                <div><strong>{{ translate('Store URL') }}:</strong> {{ $config->store_url }}</div>
                                <div><strong>{{ translate('Products') }}:</strong> {{ number_format($config->products_count) }}</div>
                                <div><strong>{{ translate('Last Sync') }}:</strong>
                                    {{ $config->last_sync_at ? $config->last_sync_at->diffForHumans() : translate('Never') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $configs->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fa fa-store fa-3x text-muted mb-3"></i>
                    <h5>{{ translate('No WooCommerce Configurations') }}</h5>
                    <p class="text-muted">{{ translate('Add your first WooCommerce store configuration to start dropshipping') }}</p>
                    <a href="{{ route('core.dropshipping.admin.woocommerce.create') }}" class="btn btn-primary">
                        {{ translate('Add Configuration') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
<script>
    $(document).ready(function() {
        // Test connection
        $('.test-connection').on('click', function() {
            var configId = $(this).data('id');
            var btn = $(this);

            btn.html('<i class="fa fa-spinner fa-spin"></i> {{ translate("Testing...") }}');
            btn.prop('disabled', true);

            $.post('{{ route("core.dropshipping.admin.woocommerce.test-connection") }}', {
                _token: '{{ csrf_token() }}',
                config_id: configId
            }).done(function(response) {
                if (response.success) {
                    toastNotification('success', '{{ translate("Connection successful") }}');
                } else {
                    toastNotification('error', response.message);
                }
            }).fail(function() {
                toastNotification('error', '{{ translate("Connection test failed") }}');
            }).always(function() {
                btn.html('<i class="fa fa-plug"></i> {{ translate("Test Connection") }}');
                btn.prop('disabled', false);
            });
        });

        // Sync products
        $('.sync-products').on('click', function() {
            var configId = $(this).data('id');
            var btn = $(this);

            if (confirm('{{ translate("Are you sure you want to sync products? This may take some time.") }}')) {
                btn.html('<i class="fa fa-spinner fa-spin"></i> {{ translate("Syncing...") }}');
                btn.prop('disabled', true);

                $.post('{{ route("core.dropshipping.admin.woocommerce.sync-products", ":id") }}'.replace(':id', configId), {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        toastNotification('success', response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        toastNotification('error', response.message);
                    }
                }).fail(function() {
                    toastNotification('error', '{{ translate("Sync failed") }}');
                }).always(function() {
                    btn.html('<i class="fa fa-sync"></i> {{ translate("Sync Products") }}');
                    btn.prop('disabled', false);
                });
            }
        });

        // Delete configuration
        $('.delete-config').on('click', function() {
            var configId = $(this).data('id');

            if (confirm('{{ translate("Are you sure you want to delete this configuration?") }}')) {
                $.ajax({
                    url: '{{ route("core.dropshipping.admin.woocommerce.delete", ":id") }}'.replace(':id', configId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastNotification('success', '{{ translate("Configuration deleted successfully") }}');
                            location.reload();
                        } else {
                            toastNotification('error', response.message);
                        }
                    },
                    error: function() {
                        toastNotification('error', '{{ translate("Delete failed") }}');
                    }
                });
            }
        });
    });
</script>
@endsection