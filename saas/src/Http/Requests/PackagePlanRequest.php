<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class PackagePlanRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        return [
            'plan_name' => 'required|unique:tl_saas_package_plans,name,' . $request->id . '|max:50',
            'plan_duration' => 'required|numeric|min:1'
        ];
    }
}
