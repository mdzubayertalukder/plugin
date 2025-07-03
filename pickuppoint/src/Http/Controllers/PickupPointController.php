<?php

namespace Plugin\PickupPoint\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\Facades\DataTables;
use Plugin\PickupPoint\Models\PickupPoint;
use Plugin\PickupPoint\Http\Requests\PickupPointReuest;
use Plugin\PickupPoint\Http\Repositories\PickupPointRepository;
use Plugin\PickupPoint\Models\PickupPointTranslation;

class PickupPointController extends Controller
{
    protected $repository;
    public function __construct(PickupPointRepository $repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->repository = $repository;
    }

    /**
     * will redirect to pickup point creation page
     *
     * @return mixed
     */
    public function createPickupPoints()
    {
        return view('plugin/pickuppoint::create_pickup_points');
    }

    /**
     * store pickup point
     *
     * @param  PickupPointReuest $request
     * @return mixed
     */
    public function storePickupPoint(PickupPointReuest $request)
    {
        try {
            DB::beginTransaction();
            $pick_up_points = new PickupPoint();
            $pick_up_points->name = $request['name'];
            $pick_up_points->location = $request['location'];
            $pick_up_points->phone = $request['phone'];
            $pick_up_points->country_id = $request['country'];
            $pick_up_points->state_id = $request['state'];
            $pick_up_points->city_id = $request['city'];
            $pick_up_points->status = isset($request['status']) ?  config('settings.general_status.active') : config('settings.general_status.in_active');
            $pick_up_points->saveOrFail();
            DB::commit();
            toastNotification('success', 'Pickup point created successfully');
            return redirect()->route('plugin.pickuppoint.pickup.points');
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Unable to create pickup point');
            return back();
        }
    }

    /**
     * will return pick up point listing page
     *
     * @return mixed
     */
    public function pickupPoints()
    {
        return view('plugin/pickuppoint::index');
    }

    /**
     * will return pickup point list
     *
     * @return mixed
     */
    public function pickupPointList()
    {
        try {

            $query = $this->repository->getPickupPointList();
            $logs = DataTables::of($query)->toJson();

            return $logs;
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Unable to fetch login activity list" . $e
            ], 500);
        }
    }

    /**
     * delete pickup point
     *
     * @param  mixed $request
     * @return mixed
     */
    public function deletePickupPoint(Request $request)
    {
        try {
            $pick_up_points = PickupPoint::findOrFail($request['id']);
            $pick_up_points->delete();
            toastNotification('success', 'Pickup point deleted successfully');
            return redirect()->route('plugin.pickuppoint.pickup.points');
        } catch (Exception $e) {
            toastNotification('error', 'Unable to delete pickup point');
            return back();
        }
    }

    /**
     * Delete pickup point in bulk 
     *
     * @param  Request $request
     * @return mixed
     */
    public function deleteBulkPickupPoint(Request $request)
    {
        try {
            if ($request->has('data')) {
                foreach ($request['data'] as $pick_up_id) {
                    $pick_up_points = PickupPoint::findOrFail($pick_up_id);
                    $pick_up_points->delete();
                }
            }
            toastNotification('success', 'Items Deleted Successfully');
        } catch (\Exception $e) {
            toastNotification('error', 'Action Failed');
        } catch (\Error $e) {
            toastNotification('error', 'Action Failed');
        }
    }

    /**
     * will redirect to pick up point editing page
     *
     * @param  Request $request
     * @return mixed
     */
    public function editPickupPoint(Request $request)
    {

        try {
            $pick_up_point = PickupPoint::findOrFail((int)$request['id']);

            $lang = $request['lang'] ? $request['lang'] : getDefaultLang();

            if ($lang != getDefaultLang()) {
                $pick_up_point->name = $this->getTranslatedPickUpPoint($pick_up_point->id, $request['lang'], $pick_up_point->name);
            }

            return view('plugin/pickuppoint::edit_pickup_points', compact('pick_up_point', 'lang'));
        } catch (Exception $e) {
            toastNotification('error', 'Action failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getTranslatedPickUpPoint($pick_up_point_id, $lang, $pick_up_point_name)
    {
        $translation = DB::table('tl_pick_up_points_translations')
            ->where('pic_up_point_id', '=', $pick_up_point_id)
            ->where('lang', '=', $lang);

        if ($translation->exists()) {
            return $translation->first()->name;
        } else {
            return $pick_up_point_name;
        }
    }


    /**
     * update pickup point
     *
     * @param  PickupPointReuest $request
     * @return mixed
     */
    public function updatePickupPoint(PickupPointReuest $request)
    {
        try {
            DB::beginTransaction();
            $pick_up_points = PickupPoint::find($request['id']);

            if (isset($request['lang']) && $request['lang'] == getDefaultLang()) {
                $pick_up_points->name = $request['name'];
                $pick_up_points->location = $request['location'];
                $pick_up_points->phone = $request['phone'];
                $pick_up_points->country_id = $request['country'];
                $pick_up_points->state_id = $request['state'];
                $pick_up_points->city_id = $request['city'];
                $pick_up_points->status = isset($request['status']) ?  config('settings.general_status.active') : config('settings.general_status.in_active');
                $pick_up_points->update();
            }


            if (isset($request['lang']) && $request['lang'] != getDefaultLang()) {
                $pick_up_point_trans_details = DB::table('tl_pick_up_points_translations')
                    ->where('pic_up_point_id', '=', $pick_up_points->id)
                    ->where('lang', '=', $request['lang']);
                if ($pick_up_point_trans_details->exists()) {
                    $pick_up_point_trans = PickupPointTranslation::find($pick_up_point_trans_details->first()->id);
                    $pick_up_point_trans->name = $request['name'];
                    $pick_up_point_trans->update();
                } else {
                    $pick_up_point_trans = new PickupPointTranslation();
                    $pick_up_point_trans->name = $request['name'];
                    $pick_up_point_trans->pic_up_point_id = $pick_up_points->id;
                    $pick_up_point_trans->lang = $request['lang'];
                    $pick_up_point_trans->saveOrFail();
                }
            }
            DB::commit();
            toastNotification('success', 'Pickup point updated successfully');
            return redirect()->route('plugin.pickuppoint.edit.pickup.point', ['id' => $request['id'], 'lang' => $request['lang']]);
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Unable to update pickup point');
            return back();
        }
    }

    /**
     * update pickup point status
     *
     * @param  Request $request
     * @return mixed
     */
    public function updatePickupPointStatus(Request $request)
    {
        try {
            $pickup_point = PickupPoint::find($request['id']);
            $pickup_point->status = $request['status'];
            $pickup_point->update();
            return response()->json([
                'success' => true,
                'message' => 'Pickup point status updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Unable to update pickup point status" . $e
            ], 500);
        }
    }
}
