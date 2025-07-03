<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Repositories\CustomerRepository;
use Plugin\TlcommerceCore\Http\Requests\CustomerAddressRequest;
use Plugin\TlcommerceCore\Http\Resources\CustomerAddressCollection;
use Plugin\TlcommerceCore\Http\Resources\CustomerAddressDetailCollection;

class CustomerAddressController extends Controller
{
    protected $customer_repository;

    public function __construct(CustomerRepository $customer_repository)
    {
        $this->customer_repository = $customer_repository;
    }

    /**
     * Will store customer new address
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCustomerAddress(CustomerAddressRequest $request)
    {
        $res = $this->customer_repository->storeCustomerAddress($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will update customer address
     * 
     * @param 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerAddress(CustomerAddressRequest $request)
    {
        $res = $this->customer_repository->updateCustomerAddress($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return customer all address
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerAllAddress()
    {
        $addresses = $this->customer_repository->customerAllAddress(auth('jwt-customer')->user()->id);
        if ($addresses != NULL) {
            return  new CustomerAddressCollection($addresses);
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will get customer address details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerAddressDetails(Request $request)
    {
        $address = $this->customer_repository->customerAddressDetails($request['id']);
        if ($address != null) {
            return new CustomerAddressDetailCollection($address);
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
}
