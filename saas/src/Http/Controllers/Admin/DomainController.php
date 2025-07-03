<?php

namespace Plugin\Saas\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugin\Saas\Models\CustomDomain;
use Plugin\Saas\Services\SaasNotification as NotificationService;

class DomainController extends Controller
{

    /**
     * Will show custom domain request list
     */
    public function customDomainRequest(): View
    {
        $domain_request = CustomDomain::all();
        return view('plugin/saas::admin.subscriptions.domains.custom_domain_request', compact('domain_request'));
    }

    /**
     * Will request to delete custom domain
     */
    public function deleteCustomDomain(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $custom_domain = CustomDomain::find((int)$request['id']);
            $status = $custom_domain->status;

            if ($status == 1) {
                DB::table('domains')->where('id', '=', $custom_domain->domain->id)->update([
                    'domain' => $custom_domain->domain->main_domain
                ]);
            }

            $custom_domain->delete();
            DB::commit();
            toastNotification('success', translate('Custom domain request deleted successfully!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Unable to delete custom domain!'));
            return back();
        }
    }

    /**
     * Will request to update custom domain
     */
    public function updateCustomDomain(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $custom_domain = CustomDomain::find((int)$request['id']);
            $requested_status = $request['domain_status'];
            $status = $custom_domain->status;

            if ($status == 1 && $requested_status == 2) {
                DB::table('domains')->where('id', '=', $custom_domain->domain->id)->update([
                    'domain' => $custom_domain->domain->main_domain
                ]);
            }

            if ($requested_status == 1) {
                DB::table('domains')->where('id', '=', $custom_domain->domain->id)->update([
                    'domain' => $custom_domain->requested_domain
                ]);

                DB::table('tl_saas_custom_domain')
                    ->where('store_id', '=', $custom_domain->saasAccount->id)
                    ->where('id', '!=', (int)$request['id'])
                    ->update([
                        'status' => 2
                    ]);
            }

            $custom_domain->status = $requested_status;

            if ($requested_status == 2) {
                $custom_domain->cancelled_date = Carbon::now()->toDateTimeString();
            }
            if ($requested_status == 1) {
                $custom_domain->approved_date = Carbon::now()->toDateTimeString();
            }

            $custom_domain->update();

            //handle notification
            $notification_service = new NotificationService();
            $notification_service->customDomainRequestStatusUpdateToSubscriber($custom_domain->saasAccount->id);

            DB::commit();
            toastNotification('success', translate('Custom domain request status updated!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update status!'));
            return back();
        }
    }
}
