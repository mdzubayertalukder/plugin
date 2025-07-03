<?php

namespace Plugin\Saas\Http\Controllers\Admin;

use PDF;
use Illuminate\View\View;
use Plugin\Saas\Models\Currency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\Saas\Repositories\StoreRepository;
use Plugin\Saas\Repositories\SettingsRepository;

class PaymentController extends Controller
{
    public function __construct(
        public StoreRepository $storeRepository
    ) {}
    /**
     * Will clear system  cache
     */
    public function paymentHistory(): View
    {
        $payment_history = DB::table('tl_saas_payment_histories')
            ->join('tl_users', 'tl_users.id', '=', 'tl_saas_payment_histories.user_id')
            ->orderBy('tl_saas_payment_histories.id', 'desc')
            ->select([
                'tl_users.name as subscriber',
                'tl_saas_payment_histories.title',
                'tl_saas_payment_histories.method',
                'tl_saas_payment_histories.coupon_code',
                'tl_saas_payment_histories.currency',
                'tl_saas_payment_histories.discount_amount',
                'tl_saas_payment_histories.final_amount',
                'tl_saas_payment_histories.updated_at',
                'tl_saas_payment_histories.pid',
                'tl_saas_payment_histories.saas_account_id as store_id'
            ])->get();

        return view('plugin/saas::admin.subscriptions.payments.payment_history', compact('payment_history'));
    }

    /**
     * Will print invoice
     */
    public function printInvoice($store_id)
    {
        $admin_logo = DB::table('tl_general_settings')
            ->join('tl_general_settings_has_values', 'tl_general_settings_has_values.settings_id', '=', 'tl_general_settings.id')
            ->where('tl_general_settings.name', '=', 'admin_logo')
            ->value('value');

        $site_title = DB::table('tl_general_settings')
            ->join('tl_general_settings_has_values', 'tl_general_settings_has_values.settings_id', '=', 'tl_general_settings.id')
            ->where('tl_general_settings.name', '=', 'system_name')
            ->value('value');

        $payment_history = $this->storeRepository->storePaymentHistory($store_id)->first();

        $font_family = "Roboto";
        $local = getLocale();

        if ($local  == 'bd') {
            $font_family = 'Bangla';
        }

        if ($local  == 'sa') {
            $font_family = 'Arabic';
        }

        if ($local  == 'il') {
            $font_family = 'Hebrew';
        }

        $default_currency_id = $default_currency_id = SettingsRepository::getSaasSetting('default_currency');
        $default_currency = Currency::find($default_currency_id);
        $currency_font = 'Arial Unicode MS';
        if ($default_currency->symbol == '₹') {
            $currency_font = 'Roboto';
        }

        $data = [
            'admin_logo' => $admin_logo,
            'site_title' => $site_title,
            'payment_history' => $payment_history,
            'font_family' => $font_family,
            'currency_font' => $currency_font,
        ];

        $default_language = getLocale();
        $is_rtl = DB::table('tl_languages')
            ->where('code', '=', $default_language)
            ->where('is_rtl', '=', 1)
            ->exists();
        if ($is_rtl) {
            $invoice_view = 'plugin/saas::user.panel.subscription.invoice_rtl';
            $pdf = PDF::loadView($invoice_view, $data)->set_option('isFontSubSettingEnabled', true);
        } else {
            $invoice_view = 'plugin/saas::user.panel.subscription.invoice';
            $pdf = PDF::loadView($invoice_view, $data)->set_option('isFontSubSettingEnabled', true);
        }

        return $pdf->download();
    }
}
