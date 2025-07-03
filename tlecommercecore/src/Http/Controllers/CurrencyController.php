<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Plugin\TlcommerceCore\Models\Currency;
use Plugin\TlcommerceCore\Http\Requests\CurrencyRequest;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;

class CurrencyController extends Controller
{

    /**
     * will redirect to currency adding page
     */
    public function addCurrency()
    {
        return view('plugin/tlecommercecore::currency.add_currency');
    }

    /**
     * store new currency
     *
     * @param  mixed $request
     * @return mixed
     */
    public function storeCurrency(CurrencyRequest $request)
    {
        try {
            $currency = new Currency();
            $currency->name = $request['name'];
            $currency->code = $request['code'];
            $currency->symbol = $request['symbol'];
            $currency->position = $request['position'];
            $currency->conversion_rate = $request['exchange_rate'];
            $currency->thousand_separator = $request['thousand_separator'];
            $currency->decimal_separator = $request['decimal_separator'];
            $currency->number_of_decimal = $request['number_of_decimal'] != null ?  $request['number_of_decimal'] : 0;
            $currency->status = $request->has('status') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $currency->saveOrFail();
            $this->resetActiveCurrenciesCache();
            toastNotification('success', translate('Currency added successfully'));
            return redirect()->route('plugin.tlcommercecore.ecommerce.all.currencies');
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to create new currency'));
            return redirect()->route('plugin.tlcommercecore.ecommerce.all.currencies');
        }
    }

    /**
     * will redirect to currency listing page
     *
     * @return void
     */
    public function allCurrencies()
    {
        $all_currencies = Currency::all();
        return view('plugin/tlecommercecore::currency.index', compact('all_currencies'));
    }

    /**
     * update currency status
     *
     * @param  mixed $request
     * @return mixed
     */
    public function updateCurrencyStatus(Request $request)
    {
        try {
            $currency = Currency::findOrFail($request['id']);
            $default_currency = SettingsRepository::getEcommerceSetting('default_currency');
            if ($default_currency == $currency->id && $request['status'] == config('settings.general_status.in_active')) {
                return response()->json([
                    'success' => false,
                    'message' => translate('You can not inactive default currency')
                ]);
            }
            $currency->status = $request['status'];
            $currency->update();
            $this->resetActiveCurrenciesCache();
            return response()->json([
                'success' => true,
                'message' => translate('Currency status updated successfully')
            ]);
        } catch (Exception $e) {
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
    public function editCurrency($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            return view('plugin/tlecommercecore::currency.edit_currency', compact('currency'));
        } catch (\Throwable $th) {
            return errorMessage('Invalid edit request', 'Unable to find requested currency', '#');
        }
    }

    /**
     * update currency details
     *
     * @param  mixed $request
     * @return mixed
     */
    public function updateCurrency(CurrencyRequest $request)
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
            $this->resetActiveCurrenciesCache();
            toastNotification('success', translate('Currency updated successfully'));
            return redirect()->route('plugin.tlcommercecore.ecommerce.edit.currency', $request['id']);
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update currency'));
            return redirect()->route('plugin.tlcommercecore.ecommerce.all.currencies');
        }
    }

    /**
     * delete currency
     *
     * @param  mixed $request
     * @return mixed
     */
    public function deleteCurrency(Request $request)
    {
        try {
            $currency = Currency::findOrFail($request['id']);
            $default_currency = SettingsRepository::getEcommerceSetting('default_currency');
            if ($default_currency == $currency->id) {
                toastNotification('error', translate('You can not delete default currency'));
            } else {
                $currency->delete();
                $this->resetActiveCurrenciesCache();
                toastNotification('success', translate('Currency deleted successfully'));
            }

            return redirect()->route('plugin.tlcommercecore.ecommerce.all.currencies');
        } catch (Exception $e) {
            toastNotification('error', translate('Unable to delete currency'));
            return redirect()->route('plugin.tlcommercecore.ecommerce.all.currencies');
        }
    }

    /**
     * Will reset active currencies cache
     */
    public function resetActiveCurrenciesCache()
    {
        cache()->forget('active-currencies');
        $currencies = Cache::rememberForever('active-currencies', function () {
            return Currency::where('status', config('settings.general_status.active'))
                ->select('id', 'name', 'code', 'symbol', 'conversion_rate', 'position', 'thousand_separator', 'decimal_separator', 'number_of_decimal')
                ->get();
        });
    }
}
