<?php

namespace Plugin\Flashdeal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\Flashdeal\Http\Requests\FlashDealRequest;
use Plugin\Flashdeal\Repositories\FlashDealRepository;

class FlashDealController extends Controller
{
    protected $flashdeal_repository;

    public function __construct(FlashDealRepository $flashdeal_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->flashdeal_repository = $flashdeal_repository;
    }

    /**
     * Will return flashdeal list
     * 
     * @return mixed
     */
    public function deals()
    {
        return view('plugin/flashdeal::marketing.flashdeal.index')->with(
            [
                'deals' => $this->flashdeal_repository->dealsList()
            ]
        );
    }
    /**
     * Will redirect to new flash deal page
     * 
     * @return mixed
     */
    public function newDeal()
    {
        return view('plugin/flashdeal::marketing.flashdeal.new');
    }
    /**
     * Will store new flash deal
     * 
     * @param FlashDealRequest $request
     * @return mixed
     */
    public function storeNewDeal(FlashDealRequest $request)
    {
        $deal_id = $this->flashdeal_repository->storeFlashDeal($request);
        if ($deal_id != null) {
            toastNotification('success', translate('New deal added successfully'));
            return redirect()->route('plugin.flashdeal.products', $deal_id);
        } else {
            toastNotification('error', translate('Deal add failed'));
            return redirect()->back();
        }
    }
    /**
     * Will redirect to deal edit page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editDeal($id, Request $request)
    {
        return view('plugin/flashdeal::marketing.flashdeal.edit')->with(
            [
                'deal_details' => $this->flashdeal_repository->dealDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * Will update flash deal
     * 
     * @param FlashDealRequest $request
     * @return mixed
     */
    public function updateDeal(FlashDealRequest $request)
    {
        $res = $this->flashdeal_repository->updateDeal($request);
        if ($res == true) {
            toastNotification('success', 'Deal updated successfully');
            return redirect()->route('plugin.flashdeal.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        } else {
            toastNotification('error', 'Action failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete deal
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteDeal(Request $request)
    {
        $res = $this->flashdeal_repository->deleteDeal($request);
        if ($res == true) {
            toastNotification('success', 'Deal deleted successfully');
            return redirect()->route('plugin.flashdeal.list');
        } else {
            toastNotification('error', 'Action failed');
            return redirect()->back();
        }
    }
    /**
     * Will update deal status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateDealStatus(Request $request)
    {
        $res = $this->flashdeal_repository->updateDealStatus($request['id']);
        if ($res == true) {
            toastNotification('success', 'Status update successfully');
        } else {
            toastNotification('error', 'Action failed');
        }
    }
    /**
     * Will delete bulk  deal
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteBulkDeal(Request $request)
    {
        $res = $this->flashdeal_repository->deleteDealBulk($request);
        if ($res == true) {
            toastNotification('success', 'Selected items successfully');
        } else {
            toastNotification('error', 'Action failed');
        }
    }
    /**
     * Will redirect to flash deal products page
     * 
     * @param Int $id
     * @return mixed
     */
    public function dealProducts($id)
    {
        return view('plugin/flashdeal::marketing.flashdeal.products')->with(
            [
                'deal_details' => $this->flashdeal_repository->dealDetails($id)
            ]
        );
    }
    /**
     * Will store deals products
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function storeDealProducts(Request $request)
    {
        $res = $this->flashdeal_repository->storeFlashDealProducts($request);
        if ($res == true) {
            toastNotification('success', 'Products added successfully');
            return redirect()->route('plugin.flashdeal.products', $request['deal_id']);
        } else {
            toastNotification('error', 'Action failed. Please try again');
            return redirect()->back();
        }
    }
    /**
     * Will remove a deal product
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function removeDealProduct(Request $request)
    {
        $res = $this->flashdeal_repository->removeDealProduct($request);
        if ($res == true) {
            toastNotification('success', 'Products removed successfully');
            return redirect()->route('plugin.flashdeal.products', $request['deal_id']);
        } else {
            toastNotification('error', 'Action failed');
            return redirect()->back();
        }
    }
    /**
     * Will remove bulk deal products
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function removeDealProductBulk(Request $request)
    {
        $res = $this->flashdeal_repository->removeDealProductBulk($request);
        if ($res == true) {
            toastNotification('success', 'Selected items removed successfully');
        } else {
            toastNotification('error', 'Action failed');
        }
    }
    /**
     * Will update deal product
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function updateDealProduct(Request $request)
    {
        $res = $this->flashdeal_repository->updateDealProduct($request);
        if ($res == true) {
            toastNotification('success', 'Deal product updated successfully');
            return redirect()->route('plugin.flashdeal.products', $request['deal_id']);
        } else {
            toastNotification('error', 'Action failed');
            return redirect()->back();
        }
    }
}
