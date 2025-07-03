<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\TaxRate;
use Plugin\TlcommerceCore\Http\Requests\TaxRateRequest;
use Plugin\TlcommerceCore\Repositories\VatTaxRepository;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Http\Requests\TaxProfileRequest;
use Plugin\TlcommerceCore\Repositories\ShippingRepository;
use Plugin\TlcommerceCore\Http\Requests\ShippingZoneTaxRequest;

class TaxController extends Controller
{
    protected $tax_repository;
    protected $shipping_repository;
    protected $product_repository;

    public function __construct(VatTaxRepository $tax_repository, ShippingRepository $shipping_repository, ProductRepository $product_repository)
    {
        $this->tax_repository = $tax_repository;
        $this->shipping_repository = $shipping_repository;
        $this->product_repository = $product_repository;
    }
    /**
     * Will redirect vat and taxes page
     * 
     * @return mixed
     */
    public function taxes()
    {
        return view('plugin/tlecommercecore::taxes.index')->with(
            [
                'tax_profiles' => $this->tax_repository->taxProfiles()
            ]
        );
    }
    /**
     * Will store new tax profile
     * 
     * @param TaxProfileRequest $request
     */
    public function storeTaxProfile(TaxProfileRequest $request)
    {
        return response()->json(
            [
                'success' => $this->tax_repository->storeTaxProfile($request)
            ]
        );
    }
    /**
     * Will update new tax profile
     * 
     * @param TaxProfileRequest $request
     */
    public function updateTaxProfile(TaxProfileRequest $request)
    {
        return response()->json(
            [
                'success' => $this->tax_repository->updateTaxProfile($request)
            ]
        );
    }
    /**
     * Will delete tax profile
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deleteTaxProfile(Request $request)
    {
        $res = $this->tax_repository->deleteTaxProfile($request['id']);
        if ($res) {
            toastNotification('success', 'Tax profile deleted successfully');
            return to_route('plugin.tlcommercecore.ecommerce.settings.taxes.list');
        }
        if (!$res) {
            toastNotification('error', 'Tax profile delete failed');
            return redirect()->back();
        }
    }
    /**
     * Will return zone taxes list
     * 
     * @param Int $id
     * @return mixed
     */
    public function manageTaxRates($id, Request $request)
    {
        return view('plugin/tlecommercecore::taxes.manage_tax_rates')->with(
            [
                'profile' =>  $this->tax_repository->taxProfileDetails($id),
                'tax_rates' => $this->tax_repository->taxProfileTaxRates($id, $request)
            ]
        );
    }
    /**
     * Will store new tax rates
     * 
     * @
     */
    public function storeTaxRates(TaxRateRequest $request)
    {
        return response()->json(
            [
                'success' => $this->tax_repository->storeTaxRates($request)
            ]
        );
    }
    /**
     * Will update tax rate value
     * 
     * @param \illuminate\Http\Request $request
     */
    public function updateTaxRateValue(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request['selected_items'], true) as $item) {
                $tax_rate = TaxRate::find($item);
                if ($tax_rate != null) {
                    $tax_rate->tax_rate = $request['rate'];
                    $tax_rate->save();
                }
            }
            DB::commit();

            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will update tax post code
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateTaxRatePostCode(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request['selected_items'], true) as $item) {
                $tax_rate = TaxRate::find($item);
                if ($tax_rate != null) {
                    $tax_rate->postal_code = $request['post_code'];
                    $tax_rate->save();
                }
            }
            DB::commit();

            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will update tax name
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateTaxRateName(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request['selected_items'], true) as $item) {
                $tax_rate = TaxRate::find($item);
                if ($tax_rate != null) {
                    $tax_rate->tax_name = $request['name'];
                    $tax_rate->save();
                }
            }
            DB::commit();

            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Bulk action of tax rate
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function taxRateBulkAction(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request['items'], true) as $item) {
                $tax_rate = TaxRate::find($item);
                if ($tax_rate != null) {
                    $tax_rate->delete();
                }
            }
            DB::commit();

            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
}
