<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class PackageConfirmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        $package_info = Package::where('id', $request['package'])->first();
        $rules = [
            'package' => 'required|exists:tl_saas_packages,id',
        ];
        if ($package_info->type == 'paid') {
            $rules['plan_id'] = 'required|numeric';

            if (!$request->has('is_trial')) {
                $rules['name'] = 'required|max:250';
                $rules['email'] = 'required|email|max:250';
                $rules['phone'] = 'required|max:250';
                $rules['country'] = 'nullable|exists:tl_countries,id';
                $rules['state'] = 'nullable|exists:tl_states,id';
                $rules['city'] = 'nullable|exists:tl_cities,id';
                $rules['address'] = 'required|max:250';
                $rules['payment_method'] = 'required|exists:tl_saas_payment_methods,id';
            }
        }

        //Store validation when update plan
        if ($request->has('store')) {
            $rules['store'] = 'required|exists:tl_saas_accounts,id|max:250';
        }
        //Store name validation when subscribe new package
        if (!$request->has('store') && $request->has('store_name')) {
            $rules['store_name'] = 'required|unique:tl_saas_accounts,store_name|max:250';
            $rules['default_language'] = 'required|max:250';
            $rules['default_currency'] = 'required|max:250';
        }

        return $rules;
    }
}
