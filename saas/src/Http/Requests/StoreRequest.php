<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        $rules = [
            'subscriber_id' => 'required|exists:tl_users,id',
            'store_name' => 'required|unique:tl_saas_accounts,store_name',
            'package_id' => 'required|exists:tl_saas_packages,id',
            'default_language' => 'required|integer',
            'default_currency' => 'required|integer'
        ];

        $package = Package::find((int)$request['package_id']);

        if ($package != null && $package->type == 'paid') {
            $rules['plan_id'] = 'required|exists:tl_saas_package_plans,id';
        }

        return $rules;
    }
}
