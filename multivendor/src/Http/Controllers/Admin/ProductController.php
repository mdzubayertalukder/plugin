<?php

namespace Plugin\Multivendor\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class ProductController extends Controller
{

    public function __construct(public ProductRepository $productRepository)
    {
    }
    /**
     * Will return seller product
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function sellerProducts(Request $request)
    {
        return view('plugin/multivendor::admin.products.index')->with([
            'products' => $this->productRepository->productManagement($request, null, 'seller')
        ]);
    }
}
