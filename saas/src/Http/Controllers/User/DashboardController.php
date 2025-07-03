<?php

namespace Plugin\Saas\Http\Controllers\User;

use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Plugin\Saas\Models\CustomDomain;
use Plugin\Saas\Repositories\SubscriptionRepository;

class DashboardController extends Controller
{
    public $sub_repo;

    public function __construct(SubscriptionRepository $sub_repo)
    {
        $this->sub_repo = $sub_repo;
    }

    /*
     * will return saas dashboard
     */
    public function saasDashboard(): View
    {
        $stores = $this->getStoreListForDashboard();
        $payment_history = $this->getPaymentHistoryForDashboard();
        $domain_request = CustomDomain::whereHas('saasAccount', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->orderBy('id', 'desc')->take(5)->get();
        return view('plugin/saas::user.panel.dashboard', compact('stores', 'payment_history', 'domain_request'));
    }

    /**
     * will return store list for dashboard
     */
    public function getStoreListForDashboard()
    {
        $query = SaasAccount::with(['domain' => function ($q) {
            $q->select(['domain', 'tenant_id']);
        }, 'package' => function ($pq) {
            $pq->with(['package_translations'])->select('id', 'name');
        }, 'plan' => function ($plq) {
            $plq->select(['name', 'id']);
        }]);

        $query = $query->where('user_id', auth()->user()->id);

        $stores = $query->orderBy('id', 'DESC')->get();

        return $stores;
    }

    /**
     * will return payment history for dashboard
     */
    public function getPaymentHistoryForDashboard()
    {
        $payment_history = DB::table('tl_saas_payment_histories')
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->select([
                'title',
                'method',
                'coupon_code',
                'currency',
                'discount_amount',
                'final_amount',
                'updated_at',
                'pid',
                'saas_account_id as store_id'
            ])->take(5)->get();

        return $payment_history;
    }
}
