<?php

namespace Plugin\PageBuilder\Http\Controllers;

use AppLoader;
use Core\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\PageBuilder\Helpers\BuilderHelper;

class PageBuilderUpdateController extends Controller
{
    public $latest_version;

    public function __construct()
    {
        if (auth()->user() != null && !auth()->user()->hasRole('Super Admin')) {
            abort(403);
        }
        $plugin_info = file_get_contents(base_path("plugins/pagebuilder/plugin.json"));
        $data = json_decode($plugin_info, true);
        $this->latest_version = $data['version'];
    }

    /**
     * Will redirect to update list
     */
    public function updateList()
    {
        $update_available = false;
        $current_version = Plugin::where('location', 'pagebuilder')->first()->version;

        if ($current_version != null) {
            if ($current_version != $this->latest_version) {
                $update_available = true;
            }
        }

        if ($current_version == null) {
            $current_version = '1.0';
            $update_available = true;
        }

        return view('plugin/pagebuilder::update.check_update', ['update_available' => $update_available, 'current_version' => $current_version, 'latest_version' => $this->latest_version]);
    }

    /**
     * Will update pagebuilder plugin
     */
    public function updatePageBuilder(Request $request)
    {
        try {
            //Store database

            $license_key = $request->purchase_key;

            $res = AppLoader::createApp($license_key, false, null, 'pagebuilder');

            if ($res == 'SUCCESS') {

                $this->updatePluginVersion($this->latest_version);

                //Import Database
                $db = base_path('updates/update.sql');
                if (file_exists($db)) {
                    DB::unprepared(file_get_contents($db));
                }

                toastNotification('success', 'Successfully Updated');
                return to_route('plugin.pagebuilder.check.update');
            }

            return $res;
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Update unsuccessful');
            return to_route('plugin.pagebuilder.check.update');
        } catch (\Error $e) {
            DB::rollBack();
            toastNotification('error', 'Update unsuccessful');
            return to_route('plugin.pagebuilder.check.update');
        }
    }

    /**
     * Will updated theme versions
     */
    public function updatePluginVersion($updated_version)
    {
        $plugin = Plugin::where('location', 'pagebuilder')->first();
        $plugin->version = $updated_version;
        $plugin->save();
    }

    /**
     * Banner Modal Show
     * @param Request $request (Ajax Request)
     * @return Response
     */
    public function showReviewModal(Request $request)
    {
        try {
            $details = empty($request->details) ? null : $request->details;
            $modal = view('plugin/saas-pagebuilder::builders.includes.review-modal', ['details' => $details, 'key' => $request->key])->render();
            return BuilderHelper::jsonResponse(200, '', $modal);
        } catch (\Exception $e) {
            return BuilderHelper::jsonResponse(500, translate('New Banner Slide Open Failed'));
        }
    }
}
