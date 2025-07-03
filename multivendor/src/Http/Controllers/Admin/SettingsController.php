<?php

namespace Plugin\Multivendor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Core\Models\GeneralSettings;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Core\Models\GeneralSettingsHasValue;
use Plugin\Multivendor\Models\CategoryHasCommission;

class SettingsController extends Controller
{

    /**
     * Will redirect seller settings page
     */
    public function sellerSettings()
    {
        $categories_commissions = CategoryHasCommission::with(['category' => function ($q) {
            $q->select('id', 'name');
        }])->select('category_id', 'rate')->get()->groupBy(function ($q) {
            return $q->rate;
        });
        return view('plugin/multivendor::admin.sellers.settings', ['categories_commissions' => $categories_commissions]);
    }
    /**
     * 
     * Will update seller settings
     */
    public function sellerSettingsUpdate(Request $request)
    {

        try {
            DB::beginTransaction();
            $settings = [
                'seller_default_commission' => $request['seller_default_commission'] != null ? $request['seller_default_commission'] : 0,
                'category_wise_seller_commission' => $request->has('category_wise_seller_commission') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'seller_auto_verification' => $request->has('seller_auto_verification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'product_auto_approve' => $request->has('product_auto_approve') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'seller_min_withdrawal_amount' => $request['seller_min_withdrawal_amount'] != null ? $request['seller_min_withdrawal_amount'] : 1,
                'shop_default_bg_image' => $request['shop_default_bg_image'],
            ];
            //Store or update category wise commission
            if ($request->commission != null && $settings['category_wise_seller_commission'] == config('settings.general_status.active')) {
                $categories = [];
                foreach ($request->commission as $key => $commission) {
                    $commission_rate = $commission['rate'];
                    if ($commission_rate != null) {
                        if (array_key_exists('categories', $commission)) {
                            foreach ($commission['categories'] as $category) {
                                $cat_commission = CategoryHasCommission::firstOrCreate(['category_id' => $category]);
                                $cat_commission->rate = $commission_rate;
                                $cat_commission->save();
                                array_push($categories, $category);
                            }
                        }
                    }
                }

                //Remove removable items
                $removable_items = CategoryHasCommission::whereNotIn('category_id', $categories)->get();
                if ($removable_items != null) {
                    $removable_items->each->delete();
                }
            } else {
                $removable_items = CategoryHasCommission::all();
                if ($removable_items != null) {
                    $removable_items->each->delete();
                }
            }

            //Update settings
            $settings_keys = array_keys($settings);
            foreach ($settings_keys as $key) {
                $config = GeneralSettings::firstOrCreate(['name' => $key]);
                $value = GeneralSettingsHasValue::where('settings_id', $config->id)->first();
                if ($value != null) {
                    $value->value = $settings[$key];
                    $value->save();
                } else {
                    $new_value = new GeneralSettingsHasValue;
                    $new_value->settings_id = $config->id;
                    $new_value->value = $settings[$key];
                    $new_value->save();
                }
            }
            DB::commit();
            toastNotification('success', translate('Settings update successfully'));
            return to_route('plugin.multivendor.admin.seller.settings');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Settings update failed'));
            return redirect()->back();
        }
    }
}
