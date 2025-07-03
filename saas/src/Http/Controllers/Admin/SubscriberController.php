<?php

namespace Plugin\Saas\Http\Controllers\Admin;


use Exception;
use Core\Models\User;
use App\Models\Tenant;
use Core\Models\Language;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Plugin\Saas\Models\CustomDomain;
use Core\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;
use Plugin\Saas\Http\Requests\SubscriberRequest;
use Plugin\Saas\Repositories\CurrencyRepository;
use Plugin\Saas\Repositories\SubscriptionRepository;

class SubscriberController extends Controller
{


    public function __construct(
        protected UserRepository $user_repository,
        protected SubscriptionRepository $sub_repo,
    ) {}
    /**
     * Will redirect to customer list page
     */
    public function index(): View
    {
        $subscribers = DB::table('tl_users')
            ->leftJoin('tl_saas_accounts', 'tl_saas_accounts.user_id', '=', 'tl_users.id')
            ->where('tl_users.user_type', '=', config('saas.user_type.subscriber'))
            ->orderBy('tl_users.id', 'desc')
            ->groupBy('tl_users.id')
            ->select([
                'tl_users.id',
                DB::raw('GROUP_CONCAT(DISTINCT tl_users.email) as email'),
                DB::raw('GROUP_CONCAT(DISTINCT tl_users.name) as name'),
                DB::raw('GROUP_CONCAT(DISTINCT tl_users.status) as status'),
                DB::raw('GROUP_CONCAT(DISTINCT tl_users.email_verified_at) as email_verified_at'),
                DB::raw('GROUP_CONCAT(DISTINCT tl_users.image) as image'),
                DB::raw('COUNT(tl_saas_accounts.id) as total_store')
            ])
            ->paginate(10)->withQueryString();

        $languages = Language::where('status', 1)->get();
        $currency_repository = new CurrencyRepository();
        $currencies = $currency_repository->getAllSaasCurrencies();

        return view('plugin/saas::admin.subscriptions.subscribers.index', [
            'subscribers' => $subscribers,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }
    /**
     * Will store new subscriber
     */
    public function storeSubscriber(SubscriberRequest $request): JsonResponse
    {
        try {
            $subscriber = new User();
            $subscriber->name = $request['name'];
            $subscriber->email = $request['email'];
            $subscriber->image = $request['image'];
            $subscriber->password = Hash::make($request['password']);
            $subscriber->user_type = config('saas.user_type.subscriber');
            $subscriber->status = config('settings.user_status.active');
            $subscriber->save();

            return response()->json([
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    /**
     * Will redirect to subscriber profile editing page
     */
    public function editSubscriber(Request $request): JsonResponse
    {
        try {
            $user = User::findOrFail($request['id']);
            return response()->json([
                'success' => true,
                'html' => view('plugin/saas::admin.subscriptions.subscribers.edit', ['user' => $user])->render()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    /**
     * Will update subscriber profile
     */
    public function updateSubscriber(SubscriberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::find($request['id']);
            if (!empty($request['password']) || !empty($request['password_confirmation'])) {
                $user->password = Hash::make($request['password']);
            }
            $user->name  = xss_clean($request['name']);
            $user->email = xss_clean($request['email']);
            $user->image = $request['edit_image'];
            $user->status = isset($request['status']) ? config('settings.user_status.active') : config('settings.user_status.in_active');
            $user->update();
            DB::commit();
            return response()->json([
                'success' => true
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    /**
     * will redirect to subscriber details page
     */
    public function subscriberDetails(int $id): View
    {
        $stores = $this->sub_repo->subscriberStoresList($id);
        $payment_history = $this->sub_repo->subscriberPaymentHistory($id);
        $domain_request = CustomDomain::whereHas('saasAccount', function ($query) use ($id) {
            $query->where('user_id', $id);
        })->orderBy('id', 'desc')->get();

        $subscriber_info = User::findOrFail($id);

        return view('plugin/saas::admin.subscriptions.subscribers.details', compact('subscriber_info', 'stores', 'payment_history', 'domain_request'));
    }

    /**
     * Will delete subscriber
     */
    public function subscriberDelete(Request $request): RedirectResponse
    {
        try {
            $user = User::find($request['subscriber_id']);
            $stores = SaasAccount::where('user_id', $user->id)->get();
            foreach ($stores as $store) {
                $tenant_id = $store->tenant_id;
                if ($store->is_db_created == 0) {
                    DB::table('tenants')->where('id', '=', $tenant_id)->delete();
                } else {
                    Tenant::find($tenant_id)->delete();
                }

                $custom_domain = DB::table('tl_saas_custom_domain')->where('store_id', '=', $store->id);
                if (!empty($custom_domain->exists())) {
                    $custom_domain_data = $custom_domain->first();
                    $status = $custom_domain_data->status;
                    if ($status == 1) {
                        DB::table('domains')->where('tenant_id', '=', $tenant_id)->update([
                            'domain' => DB::raw('main_domain')
                        ]);
                    }
                    $custom_domain->delete();
                }

                if (File::exists(public_path("/tenant/tenant$tenant_id"))) {
                    chmod(public_path("tenant/tenant$tenant_id"), 0777);
                    rrmdir(public_path("tenant/tenant$tenant_id"));
                }
            }
            $user->delete();

            toastNotification('success', translate('Subscriber deleted Successfully'));
            return back();
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to Delete Subscriber!'));
            return back();
        }
    }
}
