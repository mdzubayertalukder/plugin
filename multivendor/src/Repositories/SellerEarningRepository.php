<?php

namespace Plugin\Multivendor\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Plugin\Multivendor\Models\SellerEarnings;
use Plugin\Multivendor\Models\SellerPayoutInfo;
use Plugin\Multivendor\Models\SellerPayoutRequests;
use Plugin\TlcommerceCore\Repositories\EcommerceNotification;

class SellerEarningRepository
{

    /**
     * Will return seller payout info
     * 
     * @param Int $seller_id
     * @return Collection
     */
    public function sellerPayoutInfo($seller_id)
    {
        return SellerPayoutInfo::where('seller_id', $seller_id)->first();
    }
    /**
     * Will return seller payout info
     * 
     * @param Array $data
     * @param Int $seller_id
     * @return Collection
     */
    public function updateSellerPayoutInfo($data, $seller_id)
    {
        try {
            DB::beginTransaction();
            $payout_info = SellerPayoutInfo::where('seller_id', $seller_id)->first();
            if ($payout_info == null) {
                $payout_info = new SellerPayoutInfo;
            }
            $payout_info->seller_id = $seller_id;
            $payout_info->bank_name = $data['bank_name'];
            $payout_info->bank_code = $data['bank_code'];
            $payout_info->account_name = $data['account_name'];
            $payout_info->account_holder_name = $data['account_holder_name'];
            $payout_info->account_number = $data['account_number'];
            $payout_info->bank_routing_number = $data['bank_routing_number'];

            $payout_info->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return seller payout requests
     * 
     * @param Object $request
     * @param Int $seller_id
     */
    public function sellerPayoutRequests($request, $seller_id)
    {
        $query = SellerPayoutRequests::where('seller_id', $seller_id)
            ->whereNot('status', config('multivendor.payout_request_status.accepted'))
            ->orderBy('id', 'DESC');

        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        $requests = $query->paginate(10)->withQueryString();

        return $requests;
    }
    /**
     * Will return seller payouts
     * 
     * @param Object $request
     * @param Int $seller_id
     */
    public function sellerPayouts($request, $seller_id)
    {
        $query = SellerPayoutRequests::where('seller_id', $seller_id)
            ->where('status', config('multivendor.payout_request_status.accepted'))
            ->orderBy('id', 'DESC');

        $requests = $query->paginate(10)->withQueryString();

        return $requests;
    }
    /**
     * Will return  all seller payout requests
     * 
     * @param Object $request
     * @return Collection
     */
    public function allPayoutRequests($request)
    {
        $query = SellerPayoutRequests::with(['seller'])
            ->whereNot('status', config('multivendor.payout_request_status.accepted'))
            ->orderBy('id', 'DESC');

        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        if ($request->has('seller') && $request['seller'] != null) {
            $query = $query->whereHas('seller', function ($q) use ($request) {
                $q->where('id', $request['seller']);
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        return $requests;
    }
    /**
     * Will return  all seller payouts
     * 
     * @param Object $request
     * @return Collection
     */
    public function allPayouts($request)
    {
        $query = SellerPayoutRequests::with(['seller'])
            ->where('status', config('multivendor.payout_request_status.accepted'))
            ->orderBy('id', 'DESC');

        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        if ($request->has('seller') && $request['seller'] != null) {
            $query = $query->whereHas('seller', function ($q) use ($request) {
                $q->where('id', $request['seller']);
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        return $requests;
    }
    /**
     * Will return payout request details
     * 
     * @param Int $request_id
     * @return Collection
     */
    public function payoutRequestDetails($request_id)
    {
        return SellerPayoutRequests::where('id', $request_id)->first();
    }
    /**
     * Will update payout request details
     * 
     * @param Object $request
     * @return bool
     */
    public function updatePayoutRequestDetails($request)
    {
        try {
            DB::beginTransaction();
            $request_details = SellerPayoutRequests::where('id', $request['id'])->first();
            if ($request_details != null) {
                if ($request['status'] == config('multivendor.payout_request_status.accepted')) {
                    $request_details->payment_method = $request['payment_method'];
                    $request_details->transaction_number = $request['transaction_number'];
                    $request_details->description = $request['payment_description'];
                    $request_details->payment_date = Carbon::now();
                }
                $request_details->status = $request['status'];
                $request_details->save();

                //Send notification to seller
                $message = "";
                if ($request['status'] == config('multivendor.payout_request_status.accepted')) {
                    $message = "Your payout request is accepted";
                }
                if ($request['status'] == config('multivendor.payout_request_status.cancelled')) {
                    $message = "Your payout request is cancelled";
                }
                if ($request['status'] == config('multivendor.payout_request_status.pending')) {
                    $message = "Your payout request id pending";
                }
                EcommerceNotification::sendPayoutRequestStatusUpdateNotificationToSeller($request_details->seller_id, $message);
                DB::commit();
                return true;
            } else {
                DB::commit();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will store seller payment request
     * 
     * @param Array $data
     * @param Int $seller_id
     * @return bool
     */
    public function storeSellerPayoutRequest($data, $seller_id)
    {
        try {
            DB::beginTransaction();
            $payout_request = new SellerPayoutRequests();
            $payout_request->seller_id = $seller_id;
            $payout_request->amount = $data['amount'];
            $payout_request->message = $data['message'];
            $payout_request->save();
            //Send notification to admin
            EcommerceNotification::sendPayoutRequestNotificationToAdmin();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return seller earning history
     * 
     * @param Object $request
     * @return Collections
     */
    public function sellerEarningHistory($request, $seller_id = null)
    {
        $query = SellerEarnings::with(['order' => function ($q) {
            $q->select('id', 'order_code');
        }, 'seller' => function ($sq) {
            $sq->select('id', 'name', 'email', 'image', 'uid');
        }]);

        if ($seller_id != null) {
            $query = $query->where('seller_id', $seller_id);
        }

        if ($request->has('seller') && $request['seller'] != null) {
            $query = $query->where('seller_id', $request['seller']);
        }

        return $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }
}
