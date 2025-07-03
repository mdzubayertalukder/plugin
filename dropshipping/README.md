# Dropshipping Plugin for TLCommerce

A comprehensive dropshipping plugin that integrates WooCommerce stores with your multi-tenant TLCommerce platform.

## Features

### Super Admin Features
- **WooCommerce Store Management**: Add and configure multiple WooCommerce stores
- **Product Synchronization**: Sync products from connected WooCommerce stores
- **Plan Limits Management**: Set import limits per subscription package
- **Comprehensive Reports**: View import activities and usage statistics
- **Global Settings**: Configure plugin-wide settings

### Tenant Features
- **Product Browsing**: Browse available products from connected WooCommerce stores
- **Single & Bulk Import**: Import individual products or in bulk with custom pricing
- **Import History**: Track all import activities with detailed logs
- **Pricing Management**: Set markup percentages and fixed markups
- **Import Limits**: View current usage against subscription limits

## Installation

### Method 1: Direct File Copy
1. Copy the entire `dropshipping` folder to your `plugins/` directory
2. Run the following command to install database tables:
```bash
php artisan migrate --path=plugins/dropshipping/data.sql
```

### Method 2: Plugin Upload (Recommended)
1. Create a zip file of the dropshipping plugin:
```bash
cd plugins
zip -r dropshipping.zip dropshipping/
```

2. Upload via Super Admin Panel:
   - Go to **Admin Dashboard** → **Plugins**
   - Click **Install Plugin**
   - Upload the `dropshipping.zip` file
   - The plugin will be automatically installed and registered

3. Activate the plugin:
   - Go to **Admin Dashboard** → **Plugins**
   - Find "Dropshipping" in the plugin list
   - Click **Activate**

## Configuration

### 1. WooCommerce Store Setup
1. Navigate to **Dropshipping** → **WooCommerce Stores**
2. Click **Add New Store**
3. Fill in the required information:
   - **Store Name**: A friendly name for identification
   - **Store URL**: Your WooCommerce store URL (e.g., https://yourstore.com)
   - **Consumer Key**: WooCommerce REST API Consumer Key
   - **Consumer Secret**: WooCommerce REST API Consumer Secret
4. Test the connection before saving
5. Set the store as **Active** to make it available for tenants

### 2. WooCommerce API Setup
To get the Consumer Key and Secret:
1. Go to your WooCommerce admin → **WooCommerce** → **Settings** → **Advanced** → **REST API**
2. Click **Create an API key**
3. Set permissions to **Read**
4. Copy the Consumer Key and Consumer Secret

### 3. Plan Limits Configuration
1. Navigate to **Dropshipping** → **Plan Limits**
2. Configure limits for each subscription package:
   - **Monthly Import Limit**: Maximum imports per month (-1 for unlimited)
   - **Bulk Import Limit**: Maximum products per bulk import
   - **Auto Sync**: Enable/disable automatic product syncing

## Usage

### For Tenants

#### Browsing Products
1. Go to **Dropshipping** → **Browse Products**
2. Select a WooCommerce store from the dropdown
3. Use search and filters to find products
4. Click on products to view details

#### Importing Products
1. **Single Import**:
   - Click the import button on any product
   - Set your markup percentage or fixed markup
   - Choose import options (reviews, gallery images)
   - Click **Import Product**

2. **Bulk Import**:
   - Select multiple products using checkboxes
   - Click **Bulk Import**
   - Configure pricing settings
   - Confirm the import

#### Managing Imported Products
1. Go to **Dropshipping** → **Imported Products**
2. View all successfully imported products
3. Update pricing or sync individual products
4. Remove products if needed

#### Monitoring Usage
1. Go to **Dropshipping** → **Import Limits**
2. View your current usage against subscription limits
3. Track monthly and total import counts

### For Super Admins

#### Managing Stores
1. **Add Store**: Configure new WooCommerce connections
2. **Edit Store**: Update store credentials or settings
3. **Test Connection**: Verify API connectivity
4. **Sync Products**: Manually trigger product synchronization

#### Monitoring Activity
1. **Import Reports**: View detailed import activities across all tenants
2. **Usage Reports**: Monitor tenant usage patterns
3. **Dashboard**: Overview of system-wide statistics

#### Configuration
1. **Settings**: Configure global plugin settings
2. **Plan Limits**: Set import restrictions per subscription package

## Technical Details

### Database Tables
- `dropshipping_woocommerce_configs`: WooCommerce store configurations
- `dropshipping_products`: Cached product data from WooCommerce stores
- `dropshipping_product_import_history`: Import activity logs
- `dropshipping_plan_limits`: Subscription package limits
- `dropshipping_settings`: Plugin configuration settings

### Routes
- **Admin Routes**: `/admin/dropshipping/*`
- **Tenant Routes**: `/user/dropshipping/*`

### Permissions
The plugin integrates with the existing permission system. Ensure users have appropriate roles to access dropshipping features.

## Troubleshooting

### Plugin Not Appearing
1. Verify the plugin is properly activated in the plugins list
2. Check that the `tl_plugins` table contains the dropshipping entry
3. Clear application cache: `php artisan cache:clear`

### WooCommerce Connection Issues
1. Verify WooCommerce REST API is enabled
2. Check Consumer Key and Secret are correct
3. Ensure store URL is accessible and correct
4. Verify SSL certificates if using HTTPS

### Import Failures
1. Check import limits haven't been exceeded
2. Verify product exists in the source WooCommerce store
3. Check error logs in the import history

### Navigation Not Showing
1. Ensure plugin is activated
2. Verify user has appropriate permissions
3. Check if navigation cache needs clearing

## Support

For technical support or feature requests, please contact the development team.

## Version History

### v1.0.0
- Initial release with core dropshipping functionality
- WooCommerce integration
- Product import system
- Plan limits management
- Comprehensive reporting 