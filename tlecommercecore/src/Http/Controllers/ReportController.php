<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\SearchKeyword;
use Plugin\TlcommerceCore\Models\ProductCategory;

class ReportController extends Controller
{

    /**
     * Will return product reports
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function productReport(Request $request)
    {

        $data =
            [
                'tl_com_products.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_products.name)) as name'),
                DB::raw('SUM(tl_com_ordered_products.quantity) as total_sale'),
                DB::raw('SUM(DISTINCT(tl_com_variant_product_price.quantity)) as variant_in_stock'),
                DB::raw('SUM(DISTINCT(tl_com_single_product_price.quantity)) as single_in_stock'),
            ];

        $query = Product::query()
            ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.product_id', '=', 'tl_com_products.id')
            ->leftjoin('tl_com_variant_product_price', 'tl_com_variant_product_price.product_id', '=', 'tl_com_products.id')
            ->leftjoin('tl_com_single_product_price', 'tl_com_single_product_price.product_id', '=', 'tl_com_products.id')
            ->leftjoin('tl_com_product_has_categories', 'tl_com_product_has_categories.product_id', '=', 'tl_com_products.id')
            ->groupBy('tl_com_products.id')->select($data);


        if ($request->has('category') && $request['category'] != null) {
            $query = $query->where('tl_com_product_has_categories.category_id', $request['category']);
        }

        if ($request->has('search_key') && $request['search_key']) {
            $query = $query->where('tl_com_products.name', 'like', '%' . $request['search_key'] . '%');
        }

        $query = $query->orderBy(DB::raw('SUM(tl_com_ordered_products.quantity)'), 'DESC');

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $report_data = $query->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $report_data = $query->paginate($per_page)
                ->withQueryString();
        }


        $categories = ProductCategory::where('status', config('settings.general_status.active'))->select('name', 'id')->get();

        return view('plugin/tlecommercecore::reports.products_report')->with(
            [
                'data' => $report_data,
                'categories' => $categories
            ]
        );
    }

    /**
     * Will return product wishlist reports
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function productWishlistReport(Request $request)
    {
        $data =
            [
                'tl_com_products.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_products.name)) as name'),
                DB::raw('COUNT(DISTINCT(tl_com_customer_wishlists.id)) as total_wish'),
            ];
        $query = Product::query()
            ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.product_id', '=', 'tl_com_products.id')
            ->leftjoin('tl_com_customer_wishlists', 'tl_com_customer_wishlists.product_id', '=', 'tl_com_products.id')
            ->leftjoin('tl_com_product_has_categories', 'tl_com_product_has_categories.product_id', '=', 'tl_com_products.id')
            ->groupBy('tl_com_products.id')->select($data);


        if ($request->has('category') && $request['category'] != null) {
            $query = $query->where('tl_com_product_has_categories.category_id', $request['category']);
        }

        if ($request->has('search_key') && $request['search_key']) {
            $query = $query->where('tl_com_products.name', 'like', '%' . $request['search_key'] . '%');
        }

        $query = $query->orderBy(DB::raw('COUNT(DISTINCT(tl_com_customer_wishlists.id))'), 'DESC');

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $report_data = $query->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $report_data = $query->paginate($per_page)
                ->withQueryString();
        }

        $categories = ProductCategory::where('status', config('settings.general_status.active'))->select('name', 'id')->get();

        return view('plugin/tlecommercecore::reports.products_wishlist_report')->with(
            [
                'data' => $report_data,
                'categories' => $categories
            ]
        );
    }
    /**
     * Will return users key word search report
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * 
     */
    public function userKeywordSearch(Request $request)
    {
        $data =
            [
                'tl_com_key_word_search.key_word',
                DB::raw('COUNT(DISTINCT(tl_com_key_word_search.id)) as total_search'),
            ];
        $query = SearchKeyword::query()
            ->groupBy('tl_com_key_word_search.key_word')->select($data);


        if ($request->has('search_key') && $request['search_key']) {
            $query = $query->where('tl_com_key_word_search.key_word', 'like', '%' . $request['search_key'] . '%');
        }

        $query = $query->orderBy(DB::raw('COUNT(DISTINCT(tl_com_key_word_search.id))'), 'DESC');

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $report_data = $query->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $report_data = $query->paginate($per_page)
                ->withQueryString();
        }


        $categories = ProductCategory::where('status', config('settings.general_status.active'))->select('name', 'id')->get();

        return view('plugin/tlecommercecore::reports.keyword_search_report')->with(
            [
                'data' => $report_data,
                'categories' => $categories
            ]
        );
    }
    /**
     * Will return sales chart report data
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse 
     */
    public function salesChartReport(Request $request)
    {
        $current_locale = getLocale();
        if ($current_locale == 'sa') {
            $current_locale = 'ar';
        }

        if ($current_locale == 'bd') {
            $current_locale = 'bn';
        }
        Carbon::setLocale($current_locale);

        if ($request['type'] == 'monthly') {
            $times = array();
            $sales = array();
            for ($i = 11; $i >= 0; $i--) {
                $first_day_of_month = Carbon::today()->startOfMonth()->subMonth($i);
                $last_day_of_month = Carbon::today()->endOfMonth()->subMonth($i);

                $total_sales = Orders::whereBetween(
                    'created_at',
                    [$first_day_of_month, $last_day_of_month]
                )
                    ->sum('total_payable_amount');

                array_push($times, $first_day_of_month->shortMonthName);
                array_push($sales, $total_sales);
            }
            return response()->json(
                [
                    'success' => true,
                    'times' => $times,
                    'sales' => $sales,
                ]
            );
        }

        if ($request['type'] == 'daily') {
            $times = array();
            $sales = array();
            for ($i = 29; $i >= 0; $i--) {

                $day = Carbon::today()->endOfDay()->subDay($i);
                $total_sales = Orders::whereDate('created_at', $day)->sum('total_payable_amount');
                array_push($sales, $total_sales);

                array_push($times, $day->translatedFormat('d M'));
            }

            return response()->json(
                [
                    'success' => true,
                    'times' => $times,
                    'sales' => $sales,
                ]
            );
        }
    }
}
