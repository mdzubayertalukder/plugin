<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Plugin\Saas\Http\Rules\AlreadySubscribedPlan;

class StorePlanUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        $rules = [
            'subscriber_id' => 'required|exists:tl_users,id',
            'store_id' => 'required|exists:tl_saas_accounts,id',
            'package_id' => 'required|exists:tl_saas_packages,id'
        ];

        $package = Package::find((int)$request['package_id']);

        if ($package != null && $package->type == 'paid') {
            $rules['plan_id'] = [
                'required',
                'exists:tl_saas_package_plans,id',
                new AlreadySubscribedPlan($request)
            ];
        }


        return $rules;
    }
}
