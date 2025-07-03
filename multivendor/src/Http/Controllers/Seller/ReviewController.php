<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class ReviewController extends Controller
{

    public function __construct(public ProductRepository $product_repository)
    {
    }
    /**
     * will return review list
     * 
     * @param \Illuminate\Http\Request
     * @return mixed
     */
    public function reviews(Request $request)
    {
        $reviews = $this->product_repository->reviewList($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.reviews.reviews', ['reviews' => $reviews]);
    }
}
