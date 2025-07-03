<?php

namespace Plugin\Saas\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Currency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Plugin\Saas\Http\Requests\CurrencyRequest;
use Plugin\Saas\Repositories\SettingsRepository;

class CurrencyController extends Controller
{
    /**
     * will redirect to currency adding page
     */
    public function addCurrency(): View
    {
        return view('plugin/saas::admin.currency.add_currency');
    }

    /**
     * store new currency
     *
     * @param  mixed $request
     * @return mixed
     */
    public function storeCurrency(CurrencyRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $currency = new Currency();
            $currency->name = $request['name'];
            $currency->code = $request['code'];
            $currency->symbol = $request['symbol'];
            $currency->position = $request['position'];
            $currency->conversion_rate = $request['exchange_rate'];
            $currency->thousand_separator = $request['thousand_separator'];
            $currency->decimal_separator = $request['decimal_separator'];
            $currency->number_of_decimal = $request['number_of_decimal'];
            $currency->status = $request->has('status') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $currency->saveOrFail();
            DB::commit();
            toastNotification('success', translate('Currency added successfully'));
            return redirect()->route('plugin.saas.all.currencies');
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Unable to create new currency'));
            return redirect()->route('plugin.saas.all.currencies');
        }
    }

    /**
     * will redirect to currency listing page
     *
     * @return void
     */
    public function allCurrencies(): View
    {
        $all_currencies = Currency::all();
        return view('plugin/saas::admin.currency.index', compact('all_currencies'));
    }

    /**
     * update currency status
     *
     * @param  mixed $request
     * @return mixed
     */
    public function updateCurrencyStatus(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $currency = Currency::findOrFail($request['id']);
            $default_currency = SettingsRepository::getSaasSetting('default_currency');
            if ($default_currency == $currency->id && $request['status'] == config('settings.general_status.in_active')) {
                return response()->json([
                    'success' => false,
                    'message' => translate('You can not inactive default currency')
                ]);
            }
            $currency->status = $request['status'];
            $currency->update();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => translate('Currency status updated successfully')
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => translate("Unable to update currency status")
            ], 500);
        }
    }

    /**
     * will redirect to currency editing form
     *
     * @param  mixed $id
     * @return mixed
     */
    public function editCurrency($id): View
    {
        $currency = Currency::findOrFail($id);
        return view('plugin/saas::admin.currency.edit_currency', compact('currency'));
    }

    /**
     * update currency details
     *
     * @param  mixed $request
     * @return mixed
     */
    public function updateCurrency(CurrencyRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $currency = Currency::find($request['id']);
            $currency->name = $request['name'];
            $currency->code = $request['code'];
            $currency->symbol = $request['symbol'];
            $currency->conversion_rate = $request['exchange_rate'];
            $currency->position = $request['position'];
            $currency->thousand_separator = $request['thousand_separator'];
            $currency->decimal_separator = $request['decimal_separator'];
            $currency->number_of_decimal = $request['number_of_decimal'];
            $currency->status = $request->has('status') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $currency->update();
            DB::commit();
            toastNotification('success', translate('Currency updated successfully'));
            return redirect()->route('plugin.saas.edit.currency', $request['id']);
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update currency'));
            return redirect()->route('plugin.saas.all.currencies');
        }
    }

    /**
     * delete currency
     *
     * @param  mixed $request
     * @return mixed
     */
    public function deleteCurrency(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $currency = Currency::findOrFail($request['id']);
            $default_currency = SettingsRepository::getSaasSetting('default_currency');
            if ($default_currency == $currency->id) {
                toastNotification('error', translate('You can not delete default currency'));
            } else {
                $currency->delete();
                toastNotification('success', translate('Currency deleted successfully'));
            }
            DB::commit();
            return redirect()->route('plugin.saas.all.currencies');
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Unable to delete currency'));
            return redirect()->route('plugin.saas.all.currencies');
        }
    }
}
