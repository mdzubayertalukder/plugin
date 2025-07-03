<?php

namespace Plugin\Refund\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Refund\Models\ReasonTranslation;
use Plugin\Refund\Models\OrderReturnRequest;
use Plugin\Refund\Models\ProductRefundReason;
use Plugin\Refund\Models\RefundRequestTracking;
use Plugin\Refund\Repositories\RefundNotification;
use Plugin\TlcommerceCore\Models\OrderHasProducts;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Plugin\TlcommerceCore\Repositories\EcommerceNotification;

class RefundRepository
{

    /**
     * Will return refund requests list
     *
     * @param Object $request
     * @return Collections
     */
    public function refundsRequestsList($request)
    {
        try {
            $query = OrderReturnRequest::with(['customer', 'product', 'order']);

            if ($request->has('payment_status') && $request['payment_status']) {
                $query = $query->where('refund_status', $request['payment_status']);
            }

            if ($request->has('return_status') && $request['return_status']) {
                $query = $query->where('return_status', $request['return_status']);
            }

            if ($request->has('search') && $request['search'] != null) {
                $query = $query->whereHas('order', function (Builder $query) use ($request) {
                    $query->where('order_code', 'like', '%' . $request['search'] . '%');
                });
            }

            $refunds = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString()->through(function ($item) {
                $item->id = $item->id;
                $item->code = $item->refund_code;
                $item->quantity = $item->quantity;
                $item->total_amount = $item->total_amount;
                $item->total_refund_amount = $item->total_refund_amount;
                $item->payment_status = $item->refund_status;
                $item->return_status = $item->return_status;
                $item->read_at = $item->read_at;
                $item->created_at = $item->created_at;
                $item->order_code = $item->order->order_code;
                $item->order_id = $item->order->id;
                $item->customer_name = $item->customer->name;
                $item->customer_id = $item->customer_id;
                $item->product_name = $item->product->name;
                return $item;
            });

            return $refunds;
        } catch (\Exception $e) {
            return [];
        } catch (\Error $e) {
            return [];
        }
    }
    /**
     * Will return refund reasons list
     *
     * @return Collections
     */
    public function reasonList($status = [1, 2])
    {
        return ProductRefundReason::orderBy('id', 'DESC')->whereIn('status', $status)->get();
    }
    /**
     * Will store new refund reason
     *
     * @param Array $request
     * @return bool
     */
    public function storeRefundReason($request)
    {
        try {
            DB::beginTransaction();
            $reason = new ProductRefundReason;
            $reason->name = $request['name'];
            $reason->save();
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
     * Will delete refund reason
     *
     * @param Int $id
     * @return bool
     */
    public function deleteReason($id)
    {
        try {
            DB::beginTransaction();
            $reason = ProductRefundReason::findOrFail($id);
            $reason->delete();
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
     * Will delete bulk reasons
     *
     * @param Object $request
     * @return bool
     */
    public function deleteBulkReason($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $reason_id) {
                $reason = ProductRefundReason::findOrFail($reason_id);
                if ($reason != null) {
                    $reason->delete();
                }
            }

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
     * will Change reason Status
     *
     * @param Int $id
     * @return bool
     */
    public function changeReasonStatus($id)
    {
        try {
            DB::beginTransaction();
            $reason = ProductRefundReason::findOrFail($id);
            $status = 1;
            if ($reason->status == 1) {
                $status = 2;
            }
            $reason->status = $status;
            $reason->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return reason details
     *
     * @param Int $id
     * @return Collection
     */
    public function reasonDetails($id)
    {
        return ProductRefundReason::findOrFail($id);
    }
    /**
     * will Change reason Status
     *
     * @param Array $request
     * @return bool
     */
    public function updateRefundReason($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $reason_translation = ReasonTranslation::firstOrNew(['reason_id' => $request['id'], 'lang' => $request['lang']]);
                $reason_translation->name = $request['name'];
                $reason_translation->save();
            } else {
                $reason = ProductRefundReason::findOrFail($request['id']);
                $reason->name = $request['name'];
                $reason->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will update refund request status
     *
     * @param Object $request
     * @return bool
     */
    public function updateReturnRequestStatus($request, $seller_id = null)
    {
        info("updateReturnRequestStatus");
        try {
            DB::beginTransaction();
            $refund_request = OrderReturnRequest::find($request['request_id']);

            //update order product return status
            $order_product = OrderHasProducts::find($refund_request->ordered_product_id);
            $order_product_updated_status = $order_product->return_status;
            if ($request['return_status'] == config('tlecommercecore.return_request_status.pending') || $request['return_status'] == config('tlecommercecore.return_request_status.processing') || $request['return_status'] == config('tlecommercecore.return_request_status.product_received')) {
                $order_product_updated_status = config('tlecommercecore.product_return_status.processing');
            }
            if ($request['return_status'] == config('tlecommercecore.return_request_status.cancelled')) {
                $order_product_updated_status = config('tlecommercecore.product_return_status.return_cancel');
            }
            if ($request['return_status'] == config('tlecommercecore.return_request_status.approved')) {
                $order_product_updated_status = config('tlecommercecore.product_return_status.returned');
            }
            $order_product->return_status =  $order_product_updated_status;
            $order_product->save();

            //store data in refund tracking
            $message = $request->has('comment') && $request['comment'] != null ? $request['comment'] : $this->getTrackingMessage('return', $request['return_status']);
            $tracking = new RefundRequestTracking;
            $tracking->request_id = $refund_request->id;
            $tracking->order_id = $refund_request->order_id;
            $tracking->message = $message;
            $tracking->save();

            $mail_title = $this->getMailTitle('return', $request['return_status']);

            //Update payment  status
            if ($seller_id == null && $refund_request->refund_status != $request['payment_status']) {
                $refund_request->refund_status = $request['payment_status'];
                //store data in refund tracking
                $tracking = new RefundRequestTracking;
                $tracking->request_id = $refund_request->id;
                $tracking->order_id = $refund_request->order_id;
                $tracking->message = $request->has('comment') && $request['comment'] != null ? $request['comment'] : $this->getTrackingMessage('payment', $request['return_status']);
                $tracking->save();

                if ($request['payment_status'] == config('tlecommercecore.return_request_payment_status.refunded')) {
                    $refund_request->total_refund_amount = $request['refund_amount'];
                }
                //payment in wallet
                if ($request['payment_status'] == config('tlecommercecore.return_request_payment_status.refunded') && $request->has('paid_by') && $request['paid_by'] == 'wallet' && isActivePluging('wallet')) {
                    $wallet_transaction = new \Plugin\Wallet\Models\WalletTransaction();
                    $wallet_transaction->entry_type = config('tlecommercecore.wallet_entry_type.credit');
                    $wallet_transaction->recharge_type = config('tlecommercecore.wallet_recharge_type.refund');
                    $wallet_transaction->customer_id = $refund_request->customer_id;
                    $wallet_transaction->added_by = auth()->user()->id;
                    $wallet_transaction->document = null;
                    $wallet_transaction->recharge_amount = $request['refund_amount'];
                    $wallet_transaction->status = config('tlecommercecore.wallet_transaction_status.accept');
                    $wallet_transaction->payment_method_id = null;
                    $wallet_transaction->transaction_id = null;
                    $wallet_transaction->save();

                    //Send customer refunded notification
                    $message = "Your order refunded amount credited to your wallet";
                    $mail_title = $this->getMailTitle('payment', $request['return_status']);
                    \Plugin\Wallet\Repositories\WalletNotification::sendWalletStatusUpdateNotification($refund_request->customer_id, $message);
                }
            }

            //update refund request
            $refund_request->return_status = $request['return_status'];
            $refund_request->save();
            //Send notification to customer
            RefundNotification::sendRefundRequestStatusUpdateNotification($refund_request->id, $message, $refund_request->customer_id, $mail_title);
            //Send notification to admin
            if ($seller_id != null) {
                EcommerceNotification::sendCustomerOrderReturnNotification($refund_request->id, $message);
            }
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
     * Will return refund request tracking message
     *
     * @param String $type
     * @param Int $status
     * @return String
     */
    private function getMailTitle($type, $status)
    {
        if ($type == 'return') {
            if ($status == config('tlecommercecore.return_request_status.approved')) {
                return 'Refund request approved';
            }
            if ($status == config('tlecommercecore.return_request_status.pending')) {
                return 'Refund request pending';
            }
            if ($status == config('tlecommercecore.return_request_status.processing')) {
                return 'Refund request processing';
            }
            if ($status == config('tlecommercecore.return_request_status.cancelled')) {
                return 'Refund request cancelled';
            }
            if ($status == config('tlecommercecore.return_request_status.product_received')) {
                return 'Product received';
            }
        } else {
            if ($status == config('tlecommercecore.return_request_payment_status.refunded')) {
                return 'Payment refunded';
            }
            if ($status == config('tlecommercecore.return_request_payment_status.pending')) {
                return 'Payment pending';
            }
        }
    }
    /**
     * Will return refund request tracking message
     *
     * @param String $type
     * @param Int $status
     * @return String
     */
    private function getTrackingMessage($type, $status)
    {
        if ($type == 'return') {
            if ($status == config('tlecommercecore.return_request_status.approved')) {
                return 'Your refund request has been approved';
            }
            if ($status == config('tlecommercecore.return_request_status.pending')) {
                return 'Your refund request has been pending';
            }
            if ($status == config('tlecommercecore.return_request_status.processing')) {
                return 'Your refund request has been processing';
            }
            if ($status == config('tlecommercecore.return_request_status.cancelled')) {
                return 'Your refund request has been cancelled';
            }
            if ($status == config('tlecommercecore.return_request_status.product_received')) {
                return 'Your refunded product has been received';
            }
        } else {
            if ($status == config('tlecommercecore.return_request_payment_status.refunded')) {
                return 'Refunded your order';
            }
            if ($status == config('tlecommercecore.return_request_payment_status.pending')) {
                return 'Your refund request payment has been pending';
            }
        }
    }
}
