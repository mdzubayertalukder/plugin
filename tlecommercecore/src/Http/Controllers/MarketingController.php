<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Core\Models\Role;
use Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\Customers;
use Illuminate\Support\Facades\Notification;
use Plugin\TlcommerceCore\Models\CustomNotifications;
use Plugin\TlcommerceCore\Notifications\CustomNotification;
use Plugin\TlcommerceCore\Repositories\MarketingRepository;
use Plugin\TlcommerceCore\Notifications\CustomMailNotification;
use Plugin\TlcommerceCore\Http\Requests\CustomNotificationRequest;

class MarketingController extends Controller
{
    protected $marketing_repository;

    public function __construct(MarketingRepository $marketing_repository)
    {
        $this->marketing_repository = $marketing_repository;
    }

    /**
     * Will return custom notification list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function customNotifications(Request $request)
    {
        $notifications = $this->marketing_repository->customNotifications($request);

        return view('plugin/tlecommercecore::marketing.custom_notifications')->with(
            [
                'notifications' => $notifications
            ]
        );
    }
    /**
     * Will redirect new custom notification 
     */
    public function newCustomNotifications()
    {
        return view('plugin/tlecommercecore::marketing.new_custom_notifications');
    }

    /**
     * Will return customer drop down options
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerOptions(Request $request)
    {

        $query = Customers::query()->select('id', 'name as text');
        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $customers = $query->orderBy('name', 'asc')->paginate(10);
        $morePages = true;

        if (empty($customers->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $customers->items(),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will return users options
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersOptions(Request $request)
    {
        $query = User::query()->select('id', 'name as text')->where('id', '!=', auth()->user()->id);
        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $users = $query->orderBy('name', 'asc')->paginate(10);
        $morePages = true;

        if (empty($users->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $users->items(),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will return user role options
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRolesOptions(Request $request)
    {
        $query = Role::query()->select('id', 'name as text');

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $users = $query->orderBy('name', 'asc')->paginate(10);
        $morePages = true;

        if (empty($users->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $users->items(),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    /**
     * Will send custom notification
     * 
     * @param CustomNotificationRequest $request
     * @return mixed
     */
    public function sendCustomNotification(CustomNotificationRequest $request)
    {
        try {
            DB::beginTransaction();
            $receivers = [];
            if ($request['send_to'] == config('tlecommercecore.custom_notification_receiver_type.all_customers')) {
                $receivers = Customers::where('status', config('settings.general_status.active'))->get();
            }

            if ($request['send_to'] == config('tlecommercecore.custom_notification_receiver_type.specific_customer')) {
                $receivers = Customers::where('status', config('settings.general_status.active'))->whereIn('id', $request['customers'])->get();
            }

            if ($request['send_to'] == config('tlecommercecore.custom_notification_receiver_type.all_users')) {
                $receivers = User::where('status', config('settings.general_status.active'))->where('id', '!=', auth()->user()->id)->get();
            }

            if ($request['send_to'] == config('tlecommercecore.custom_notification_receiver_type.specific_user')) {
                $receivers = User::where('status', config('settings.general_status.active'))->whereIn('id', $request['users'])->get();
            }
            if ($request['send_to'] == config('tlecommercecore.custom_notification_receiver_type.specific_user_role')) {
                $receivers = User::whereHas("roles", function ($q) use ($request) {
                    $q->whereIn("id", $request['user_roles']);
                })->get();
            }

            //Send Database notification
            if ($request['notification_type'] == config('tlecommercecore.custom_notification_type.dashboard')) {
                $data = [
                    'link' => "#",
                    'message' => $request['message']
                ];
                Notification::send($receivers, new CustomNotification($data));
            }
            //Send mail notification
            if ($request['notification_type'] == config('tlecommercecore.custom_notification_type.email')) {
                $data = [
                    '_content_' =>   $request['message'],
                ];
                $keywords = getEmailTemplateVariables(6, true);
                Notification::send($receivers, new CustomMailNotification($data, $keywords, $request['subject'], 6));
            }
            //Send database and mail notification
            if ($request['notification_type'] == config('tlecommercecore.custom_notification_type.email_dashboard')) {
                //Send Database notification
                $data = [
                    'link' => "#",
                    'message' => $request['message']
                ];
                Notification::send($receivers, new CustomNotification($data));

                //Send mail notification
                $mail_data = [
                    '_content_' =>   $request['message'],
                ];
                $keywords = getEmailTemplateVariables(6, true);
                Notification::send($receivers, new CustomMailNotification($mail_data, $keywords, $request['subject'], 6));
            }
            //Store notification info in database
            $notification = new CustomNotifications;
            $notification->to = $request['send_to'];
            $notification->type = $request['notification_type'];
            $notification->details = $request['message'];
            $notification->sender  = auth()->user()->id;
            $notification->save();
            DB::commit();
            toastNotification('success', translate('Notification send successfully'));
            return to_route('plugin.tlcommercecore.marketing.custom.notification.create.new');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Notification sending failed'));
            return redirect()->back();
        }
    }

    /**
     * Will apply bulk action
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customNotificationBulkAction(Request $request)
    {
        try {
            DB::beginTransaction();
            $items = $request['data']['selected_items'];
            CustomNotifications::whereIn('id', $items)->delete();
            DB::commit();
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
