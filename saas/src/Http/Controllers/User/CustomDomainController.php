<?php

namespace Plugin\Saas\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Plugin\Saas\Models\CustomDomain;
use Plugin\Saas\Services\SaasNotification;
use Stancl\Tenancy\Database\Models\Domain;

class CustomDomainController extends Controller
{
    /**
     * Will redirect to custom domain list
     */
    public function customDomain(): View
    {
        $saas_account = SaasAccount::where('user_id', Auth::user()->id)
            ->with('domain')
            ->with('customDomain')
            ->get();

        $domain_request = CustomDomain::groupBy('store_id', 'current_domain')
            ->select(
                'store_id',
                'current_domain',
                DB::raw('GROUP_CONCAT(requested_domain) as requested_domain'),
                DB::raw('GROUP_CONCAT(status) as status'),
                DB::raw('GROUP_CONCAT(approved_date) as approved_date'),
                DB::raw('GROUP_CONCAT(cancelled_date) as cancelled_date'),
            )
            ->get();


        for ($i = 0; $i < sizeof($domain_request); $i++) {
            $domain_request[$i]['requested_domain'] = explode(',', $domain_request[$i]['requested_domain']);
            $domain_request[$i]['status'] = explode(',', $domain_request[$i]['status']);
            $domain_request[$i]['approved_date'] = explode(',', $domain_request[$i]['approved_date']);
            $domain_request[$i]['cancelled_date'] = explode(',', $domain_request[$i]['cancelled_date']);
        }

        return view('plugin/saas::user.panel.customDomain.index', compact('saas_account', 'domain_request'));
    }

    /**
     * Will send request for a custom domain
     */
    public function requestCustomDomain(Request $request): JsonResponse
    {
        $request->validate([
            'current_domain' => 'required|exists:domains,id',
            'custom_domain' => 'required|unique:domains,domain|unique:tl_saas_custom_domain,requested_domain'
        ]);
        try {
            if (preg_match('/[!@#$%^&*(),?":{}|<>]/', $request['custom_domain'])) {
                return response()->json([
                    'success' => false,
                    'message' => translate('Please enter domain name without "http or https" !')
                ], 500);
            }

            DB::beginTransaction();

            $domain = Domain::find((int)$request['current_domain']);

            $total_custom_domain_request = DB::table('tl_saas_custom_domain')
                ->where('store_id', '=', $domain->saasAccount->id)
                ->count();

            $package_privileges = DB::table('tl_saas_package_has_privileges')
                ->where('package_id', '=', $domain->saasAccount->package_id)
                ->value('privileges');

            $package_privileges = json_decode($package_privileges);

            $custom_domain_privileges = $package_privileges->package_privileges_custom_domain;


            if ($custom_domain_privileges != -1 && (int)$total_custom_domain_request >= (int)$custom_domain_privileges) {
                return response()->json([
                    'success' => false,
                    'message' => translate('Your custom domain limit quota is over')
                ], 500);
            }

            $custom_domain = new CustomDomain();
            $custom_domain->store_id = $domain->saasAccount->id;
            $custom_domain->tenant_id = $domain->tenant->id;
            $custom_domain->current_domain = $domain->main_domain;
            $custom_domain->requested_domain = $request['custom_domain'];
            $custom_domain->status = 0;
            $custom_domain->status = 0;
            $custom_domain->saveOrFail();

            //handle notification
            $notification_service = new SaasNotification();
            $notification_service->newCustomDomainRequestNotificationToAdmin($domain->saasAccount->id);

            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => translate('Unable to send request!')
            ], 500);
        }
    }
}
