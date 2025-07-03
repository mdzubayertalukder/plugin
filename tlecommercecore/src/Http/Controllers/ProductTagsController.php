<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Http\Requests\ProductTagsRequest;
use Plugin\TlcommerceCore\Repositories\ProductTagsRepository;

class ProductTagsController extends Controller
{

    protected $tag_repository;

    public function __construct(ProductTagsRepository $tag_repository)
    {
        $this->tag_repository = $tag_repository;
    }
    /**
     * Will return product tags 
     * 
     * @return mixed
     */
    public function productTags()
    {
        return view('plugin/tlecommercecore::products.tags.index')->with(
            [
                'tags' => $this->tag_repository->tagList()
            ]
        );
    }
    /**
     * Store Product tag
     * 
     * @param ProductTagsRequest $request
     * @return mixed
     */
    public function storeTag(ProductTagsRequest $request)
    {
        $res = $this->tag_repository->storeTag($request);
        if ($res == true) {
            toastNotification('success', translate('New tag added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.tags.list');
        } else {
            toastNotification('error', translate('Tag store failed', 'Failed'));
            return redirect()->back();
        }
    }
    /**
     * Delete product tag
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteTag(Request $request)
    {
        $res = $this->tag_repository->deleteTag($request->id);
        if ($res == true) {
            toastNotification('success', translate('Tag deleted successfully'), 'success');
            return redirect()->route('plugin.tlcommercecore.product.tags.list');
        } else {
            toastNotification('error', translate('Tag delete failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk product tags
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkTag(Request $request)
    {
        $res = $this->tag_repository->deleteBulkTag($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Change Status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function changeStatus(Request $request)
    {
        $res = $this->tag_repository->changeStatus($request->id);
        if ($res == true) {
            toastNotification('success',translate('Status changed successfully'), 'Success');
        } else {
            toastNotification('error', translate('Status update failed'), 'Failed');
        }
    }
    /**
     * Edit tag
     * 
     * @param Int $id
     * @return mixed
     */
    public function editTag($id)
    {
        return view('plugin/tlecommercecore::products.tags.edit_tag')->with(
            [
                'tag_details' => $this->tag_repository->tagDetails($id)
            ]
        );
    }
    /**
     * update product tag
     * 
     * @param ProductTagRequest $request
     * @return mixed
     */
    public function updateTag(ProductTagsRequest $request)
    {
        $res = $this->tag_repository->updateTag($request);
        if ($res == true) {
            toastNotification('success', translate('Tag updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.tags.list');
        } else {
            toastNotification('error', translate('Tag update failed'), 'Failed');
            return redirect()->back();
        }
    }
}
