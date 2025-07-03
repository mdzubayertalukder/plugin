<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Http\Requests\CollectionRequest;
use Plugin\TlcommerceCore\Repositories\ProductCollectionRepository;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class ProductCollectionController extends Controller
{
    protected $collection_repository;

    protected $product_repository;

    public function __construct(ProductCollectionRepository $collection_repository, ProductRepository $product_repository)
    {
        $this->collection_repository = $collection_repository;
        $this->product_repository = $product_repository;
    }

    /**
     * Will return product collections
     * 
     * @return mixed
     */
    public function collections()
    {
        return view('plugin/tlecommercecore::products.collections.index')->with(
            [
                'collections' => $this->collection_repository->collections()
            ]
        );
    }
    /**
     * Will redirect add new collection page
     * 
     * @return mixed
     */
    public function newCollection()
    {
        return view('plugin/tlecommercecore::products.collections.create');
    }
    /**
     * 
     * Will store new collection
     * 
     * @param CollectionRequest $request
     * @return mixed
     */
    public function storeNewCollection(CollectionRequest $request)
    {
        $collection_id = $this->collection_repository->storeNewCollection($request);
        if ($collection_id != null) {
            toastNotification('success', translate('Collection added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.collection.products', $collection_id);
        } else {
            toastNotification('error', translate('Collection store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * 
     * Will redirect to collection page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editCollection($id, Request $request)
    {
        return view('plugin/tlecommercecore::products.collections.edit')->with(
            [
                'collection_details' => $this->collection_repository->collectionDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * 
     * Will update product collection
     * 
     * @param CollectionRequest $request
     * @return mixed
     */
    public function updateCollection(CollectionRequest $request)
    {
        $res = $this->collection_repository->updateCollection($request);
        if ($res == true) {
            toastNotification('success', translate('Collection updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.collection.list');
        } else {
            toastNotification('error', translate('Collection update failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete product collection
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCollection(Request $request)
    {
        $res = $this->collection_repository->deleteProductCollection($request->id);
        if ($res == true) {
            toastNotification('success', translate('Collection deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.collection.list');
        } else {
            toastNotification('error', translate('Unable to delete this collection'), 'warning');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk collection
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkCollection(Request $request)
    {
        $res = $this->collection_repository->deleteBulkProductCollection($request);
        if ($res == true) {
            toastNotification('success', translate('Collections deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Unable to delete collection'), 'warning');
        }
    }
    /**
     * Will update product collection status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateCollectionStatus(Request $request)
    {
        $res = $this->collection_repository->updateCollectionStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will return collection products
     * 
     * @param Int $id
     * @return mixed
     */
    public function collectionProducts($id)
    {
        return view('plugin/tlecommercecore::products.collections.products')->with(
            [
                'collection_details' => $this->collection_repository->collectionDetails($id),
                'products' => $this->product_repository->activeProducts()
            ]
        );
    }
    /**
     * Will store collection products
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function storeCollectionProducts(Request $request)
    {
        if ($request->has('products')) {
            $res = $this->collection_repository->storeCollectionProducts($request);
            if ($res == true) {
                toastNotification('success', translate('Products added successfully'), 'Success');
                return redirect()->route('plugin.tlcommercecore.product.collection.products', $request['collection_id']);
            } else {
                toastNotification('error', translate('Action failed'), 'Failed');
                return redirect()->back();
            }
        } else {
            toastNotification('error', translate('No product selected'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will remove collection product
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function removeCollectionProduct(Request $request)
    {
        $res = $this->collection_repository->removeCollectionProduct($request);
        if ($res == true) {
            toastNotification('success', translate('Product remove successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.collection.products', $request->collection_id);
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will bulk product remove from collection
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function removeBulkCollectionProduct(Request $request)
    {
        $res = $this->collection_repository->removeBulkCollectionProduct($request);
        if ($res == true) {
            toastNotification('success', translate('Products remove successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
}
