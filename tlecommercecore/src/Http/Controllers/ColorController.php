<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\ColorRequest;
use Plugin\TlcommerceCore\Repositories\ColorRepository;

class ColorController extends Controller
{
    protected $color_repository;

    public function __construct(ColorRepository $color_repository)
    {
        $this->color_repository = $color_repository;
    }
    /**
     * Will return color list
     * 
     * @return mixed
     */
    public function colors()
    {
        return view('plugin/tlecommercecore::products.colors.index')->with(
            [
                'colors' => $this->color_repository->colorList()
            ]
        );
    }
    /**
     * Store new color
     * 
     * @param ColorRequest $request
     * @return mixed
     */
    public function storeColor(ColorRequest $request)
    {
        $res = $this->color_repository->storeColor($request);
        if ($res == true) {
            toastNotification('success', translate('New color added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.colors.list');
        } else {
            toastNotification('error', translate('Color store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Delete color 
     * 
     * @return \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteColor(Request $request)
    {
        $res = $this->color_repository->deleteColor($request->id);
        if ($res == true) {
            toastNotification('success', translate('Color deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.colors.list');
        } else {
            toastNotification('error', translate('Unable to delete this color'), 'warning');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk colors
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkColor(Request $request)
    {
        $res = $this->color_repository->deleteBulkColor($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will return color details
     * 
     * @param Int $id
     * @param Request $request
     * @return mixed
     */
    public function editColor(Request $request, $id)
    {
        return view('plugin/tlecommercecore::products.colors.edit_color')->with(
            [
                'color_details' => $this->color_repository->colorDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * will update color
     * 
     * @param ColorRequest $request
     * @return mixed
     */
    public function updateColor(ColorRequest $request)
    {
        $res = $this->color_repository->updateColor($request);
        if ($res == true) {
            toastNotification('success', translate('Color updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.colors.list');
        } else {
            toastNotification('error', translate('Color update failed'), 'Failed');
            return redirect()->back();
        }
    }
}
