<?php

namespace Plugin\Saas\Http\Controllers\Admin;


use Exception;
use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Coupon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Plugin\Saas\Models\CouponOfPackages;
use Illuminate\Support\Facades\Validator;
use Plugin\Saas\Repositories\CouponRepository;

class CouponController extends Controller
{
    public function __construct(
        protected CouponRepository $couponRepository
    ) {}

    /**
     * will redirect to coupon creation page
     *
     * @return mixed
     */
    public function createCoupons(): View
    {
        return view('plugin/saas::admin.coupons.create_coupon');
    }

    /**
     * store new coupons
     */
    public function storeCoupons(Request $request): RedirectResponse
    {
        if ($request['is_for_bulk_generation']) {
            $validator = Validator::make($request->all(), [
                'total_unique_coupons' => 'required',
                'prefix' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'coupon_name' => 'required|unique:tl_saas_coupons,coupon_name',
                'coupon_code' => 'required|unique:tl_saas_coupons,coupon_code'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }
        }

        if ($request['coupon_type'] == 'discount') {
            $validator = Validator::make($request->all(), [
                'packages' => 'required',
                'discount' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'package' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }
        }

        $validator = Validator::make($request->all(), [
            'valid_days' => 'required',
            'coupon_type' => 'required',
            'coupon_usable_times' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();
            $valid_till = Carbon::now()->addDays((int)$request['valid_days'])->toDateTimeString();
            $csv_data = [
                ['Coupon Code', 'Valid']
            ];
            if (isset($request['is_for_bulk_generation'])) {
                $data = [];
                $data_for_csv = [];
                for ($i = 0; $i < (int)$request['total_unique_coupons']; $i++) {
                    $unique_code = strtoupper(uniqid() . time());
                    $temp_array = [
                        'coupon_name' => $request['prefix'] . $unique_code,
                        'coupon_code' => $request['prefix'] . $unique_code,
                        'coupon_type' => $request['coupon_type'],
                        'coupon_usable_times' => $request['coupon_usable_times'],
                        'discount' => $request['coupon_type'] == 'discount' ? $request['discount'] : null,
                        'valid_for_days' => $request['valid_days'],
                        'valid_till' => $valid_till
                    ];
                    $data_for_csv = [
                        $request['prefix'] . $unique_code,
                        $valid_till
                    ];
                    array_push($csv_data, $data_for_csv);
                    array_push($data, $temp_array);
                }

                $new_row_ids = [];
                foreach ($data as $row) {
                    $new_row_id = Coupon::insertGetId($row);
                    $new_row_ids[] = $new_row_id;
                }

                $data = [];

                for ($i = 0; $i < sizeof($new_row_ids); $i++) {
                    if ($request['coupon_type'] == 'discount') {
                        $packages = $request['packages'];
                        for ($j = 0; $j < sizeof($packages); $j++) {
                            $temp_array = [
                                'coupon_id' => $new_row_ids[$i],
                                'package_id' => $packages[$j]
                            ];
                            array_push($data, $temp_array);
                        }
                    } else {
                        $temp_array = [
                            'coupon_id' => $new_row_ids[$i],
                            'package_id' => $request['package']
                        ];
                        array_push($data, $temp_array);
                    }
                }
                CouponOfPackages::insert($data);
            } else {
                $coupon = new Coupon();
                $coupon->coupon_name = $request['coupon_name'];
                $coupon->coupon_code = $request['coupon_code'];
                $coupon->coupon_type = $request['coupon_type'];
                $coupon->discount = isset($request['discount']) ? $request['discount'] : null;
                $coupon->valid_for_days = $request['valid_days'];
                $coupon->coupon_usable_times = $request['coupon_usable_times'];
                $coupon->valid_till = $valid_till;
                $coupon->saveOrFail();

                $data = [];
                if ($request['coupon_type'] == 'discount') {
                    $packages = $request['packages'];
                    for ($j = 0; $j < sizeof($packages); $j++) {
                        $temp_array = [
                            'coupon_id' => $coupon->id,
                            'package_id' => $packages[$j]
                        ];
                        array_push($data, $temp_array);
                    }
                } else {
                    $temp_array = [
                        'coupon_id' => $coupon->id,
                        'package_id' => $request['package']
                    ];
                    array_push($data, $temp_array);
                }

                CouponOfPackages::insert($data);

                $data_for_csv = [
                    $request['coupon_code'],
                    $valid_till
                ];
                array_push($csv_data, $data_for_csv);
            }
            DB::commit();
            if ($request['is_for_download'] == 1) {
                $csv = Writer::createFromString('');
                $csv->insertAll($csv_data);
                return response($csv->output())
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="data.csv"');
            } else {
                toastNotification('success', translate('Coupon created successfully!'));
                return back();
            }
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Sorry, Unable to create coupon !'));
            return back();
        }
    }

    /**
     * Will return validation error response
     */
    public function validationErrorResponse($validator)
    {
        toastNotification('error', translate('Please give valid information !'));
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    /**
     * Will return all coupons
     */
    public function coupons(): View
    {
        $coupons = Coupon::with(['packages'])->get();
        return view('plugin/saas::admin.coupons.index', compact('coupons'));
    }

    /**
     * Edit coupon
     */
    public function editCoupon($id): View
    {
        $coupon = Coupon::with(['packages'])->find($id);
        return view('plugin/saas::admin.coupons.edit_coupon', compact('coupon'));
    }

    /**
     * Will update coupon
     */
    public function updateCoupon(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'valid_days' => 'required',
            'coupon_name' => 'required|unique:tl_saas_coupons,coupon_name,' . $request['id'],
            'coupon_type' => 'required',
            'coupon_code' => 'required|unique:tl_saas_coupons,coupon_code,' . $request['id'],
            'coupon_usable_times' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        if ($request['coupon_type'] == 'discount') {
            $validator = Validator::make($request->all(), [
                'packages' => 'required',
                'discount' => 'required'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'package' => 'required'
            ]);
        }

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $coupon = Coupon::find($request['id']);
            $valid_till = Carbon::parse($coupon->created_at)->addDays((int)$request['valid_days'])->toDateTimeString();
            $coupon->coupon_name = $request['coupon_name'];
            $coupon->coupon_code = $request['coupon_code'];
            $coupon->coupon_type = $request['coupon_type'];
            $coupon->discount = isset($request['discount']) ? $request['discount'] : null;
            $coupon->valid_for_days = $request['valid_days'];
            $coupon->valid_till = $valid_till;
            $coupon->coupon_usable_times = $request['coupon_usable_times'];
            $coupon->update();

            DB::table('tl_saas_coupons_of_packages')
                ->where('coupon_id', '=', $coupon->id)
                ->delete();
            $data = [];
            if ($request['coupon_type'] == 'discount') {
                $packages = $request['packages'];
                for ($j = 0; $j < sizeof($packages); $j++) {
                    $temp_array = [
                        'coupon_id' => $coupon->id,
                        'package_id' => $packages[$j]
                    ];
                    array_push($data, $temp_array);
                }
            } else {
                $temp_array = [
                    'coupon_id' => $coupon->id,
                    'package_id' => $request['package']
                ];
                array_push($data, $temp_array);
            }
            CouponOfPackages::insert($data);

            DB::commit();
            toastNotification('success', translate('Coupon updated successfully!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Sorry, Unable to update coupon!'));
            return back();
        }
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $coupon = Coupon::find($request->id);
            $coupon->delete();
            DB::commit();
            toastNotification('success', translate('Coupon Deleted Successfully!'));
            return back();
        } catch (\Exception $ex) {
            DB::rollback();
            toastNotification('error', translate('Coupon Delete Unsuccessful!'));
            return back();
        }
    }

    /**
     * Will return packages according to redeemable coupon
     */
    public function getPackagesAccordingToRedeemableCoupon(Request $request): JsonResponse
    {
        try {
            $currentDate = date('Y-m-d');
            $match_case = [
                ['coupon_type', '=', 'redeemable'],
                ['coupon_code', '=', $request['coupon']],
                ['valid_till', '>=', $currentDate]
            ];

            $data = [
                'tl_saas_packages.id',
                'tl_saas_coupons.total_used',
                'tl_saas_coupons.coupon_usable_times'
            ];

            $package = $this->couponRepository->packagesOfCouponType($match_case, $data)->first();

            if ($package != null && $package->total_used < $package->coupon_usable_times) {
                return response()->json([
                    'success' => true,
                    'package' => $package->id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => translate('Unable to retrieve package information')
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => translate('Unable to retrieve package information')
            ]);
        }
    }
}
