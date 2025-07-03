# TLCommerce Dropshipping Plugin

A comprehensive dropshipping plugin for TLCommerce that allows super admins to configure WooCommerce stores and tenants to import products with configurable limits and pricing.

## Features

### Super Admin Features
- **WooCommerce Integration**: Connect multiple WooCommerce stores via REST API
- **Product Synchronization**: Automatic and manual product sync from WooCommerce
- **Plan Management**: Configure import limits per subscription plan
- **Real-time Monitoring**: Track import activities and usage across all tenants
- **Connection Testing**: Verify WooCommerce API connections

### Tenant Features
- **Product Browsing**: View all available products from connected WooCommerce stores
- **One-Click Import**: Import individual products with custom markup
- **Bulk Import**: Import multiple products at once (subject to plan limits)
- **Pricing Control**: Set custom markup percentages for imported products
- **Import History**: Track all imported products and their status
- **Limit Monitoring**: View remaining import quotas in real-time

## Installation

1. **Database Setup**: 
   ```sql
   mysql -u username -p database_name < plugins/dropshipping/data.sql
   ```

2. **Plugin Activation**: The plugin is automatically activated through the plugin.json configuration.

3. **Permissions**: Ensure the plugin directory has proper read/write permissions.

## Configuration

### Super Admin Setup

1. **Add WooCommerce Store**:
   - Navigate to Admin → Dropshipping → WooCommerce Configurations
   - Click "Add New Configuration"
   - Enter store details:
     - Store Name
     - Store URL (e.g., https://yourstore.com)
     - Consumer Key
     - Consumer Secret
   - Test connection and save

2. **Configure Plan Limits**:
   - Go to Admin → Dropshipping → Plan Limits
   - Set limits for each subscription plan:
     - Monthly import limit
     - Total import limit
     - Bulk import limit
     - Auto-sync settings

3. **Sync Products**:
   - Select a WooCommerce configuration
   - Click "Sync Products" to import product catalog
   - Monitor sync progress

### Tenant Usage

1. **Browse Products**:
   - Go to User → Dropshipping → Products
   - Use filters to find desired products:
     - Search by name/description
     - Filter by category
     - Price range filtering
     - Stock status

2. **Import Products**:
   - Single Product: Click "Import" on product card, set markup percentage
   - Bulk Import: Select multiple products, set markup, click "Bulk Import"
   - Monitor import status in Import History

3. **Manage Imported Products**:
   - View imported products in your store
   - Update pricing through the plugin interface
   - Sync stock levels from source

## API Endpoints

### Admin Routes
```
GET  /admin/dropshipping                           - Dashboard
GET  /admin/woocommerce-config                     - List configurations
POST /admin/woocommerce-config                     - Create configuration
PUT  /admin/woocommerce-config/{id}                - Update configuration
DELETE /admin/woocommerce-config/{id}              - Delete configuration
POST /admin/woocommerce-config/test-connection     - Test connection
POST /admin/woocommerce-config/{id}/sync-products  - Sync products
```

### Tenant Routes
```
GET  /user/dropshipping/products                   - Browse products
POST /user/dropshipping/import/product/{id}        - Import single product
POST /user/dropshipping/import/products/bulk       - Bulk import
GET  /user/dropshipping/import/history             - Import history
GET  /user/dropshipping/import/limits              - Check limits
```

## Database Schema

### Tables Created
- `dropshipping_woocommerce_configs` - WooCommerce store configurations
- `dropshipping_products` - Synced product catalog
- `dropshipping_product_import_history` - Import tracking
- `dropshipping_plan_limits` - Import limits per plan
- `dropshipping_settings` - Plugin settings

## File Structure

```
plugins/dropshipping/
├── plugin.json                           # Plugin configuration
├── data.sql                             # Database schema
├── banner.png                           # Plugin banner
├── README.md                            # This file
├── routes/
│   ├── admin.php                        # Admin routes
│   └── user.php                         # Tenant routes
├── src/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── WooCommerceConfigController.php
│   │   │   ├── PlanLimitController.php
│   │   │   └── DropshippingAdminController.php
│   │   └── Tenant/
│   │       ├── ProductImportController.php
│   │       └── DropshippingTenantController.php
│   ├── Models/
│   │   ├── WooCommerceConfig.php
│   │   ├── DropshippingProduct.php
│   │   ├── ProductImportHistory.php
│   │   └── DropshippingPlanLimit.php
│   └── Services/
│       ├── WooCommerceApiService.php
│       └── ProductImportService.php
└── views/
    ├── admin/
    │   └── woocommerce/
    │       └── index.blade.php
    └── tenant/
        └── import/
            └── products.blade.php
```

## Configuration Options

### WooCommerce API Setup

1. **Enable REST API** in WooCommerce:
   - Go to WooCommerce → Settings → Advanced → REST API
   - Click "Add Key"
   - Set permissions to "Read"
   - Copy Consumer Key and Secret

2. **API Permissions Required**:
   - `read` - For fetching products, categories, and system status
   - Products endpoint: `/wp-json/wc/v3/products`
   - Categories endpoint: `/wp-json/wc/v3/products/categories`
   - System status: `/wp-json/wc/v3/system_status`

### Plan Limits Configuration

- **Monthly Limit**: Maximum products importable per month (-1 for unlimited)
- **Total Limit**: Maximum total products ever imported (-1 for unlimited)
- **Bulk Limit**: Maximum products in single bulk import
- **Auto Sync**: Enable automatic stock/price updates
- **Markup Constraints**: Minimum and maximum markup percentages

## Error Handling

### Common Issues

1. **Connection Failed**: Check WooCommerce API credentials and SSL
2. **Import Limit Reached**: Upgrade plan or wait for monthly reset
3. **Product Already Imported**: Skip or update existing product
4. **Sync Failed**: Check WooCommerce store availability

### Logging

- Import activities logged to Laravel logs
- Connection tests logged with details
- Sync progress tracked in database

## Security Features

- **API Key Masking**: Consumer secrets masked in admin interface
- **Tenant Isolation**: Products isolated per tenant database
- **Rate Limiting**: API calls throttled to prevent abuse
- **Input Validation**: All form inputs validated and sanitized

## Performance Optimization

- **Batch Processing**: Products synced in batches of 50
- **Background Jobs**: Large imports processed asynchronously
- **Caching**: Product categories and tags cached
- **Database Indexing**: Optimized indexes for fast queries

## Customization

### Adding New Import Sources
1. Create new service class extending `BaseImportService`
2. Implement required methods for API integration
3. Add configuration model and views
4. Register routes and update plugin.json

### Custom Pricing Rules
1. Extend `ProductImportService` class
2. Override `calculatePricing()` method
3. Add custom pricing logic and rules

## Support

For issues and feature requests:
1. Check error logs first
2. Verify WooCommerce API connectivity
3. Confirm plan limits and permissions
4. Contact system administrator

## Version History

- **1.0.0** - Initial release with WooCommerce integration
  - Multi-store support
  - Plan-based import limits
  - Bulk import functionality
  - Real-time sync capabilities

## License

This plugin is part of the TLCommerce ecosystem and follows the same licensing terms. 