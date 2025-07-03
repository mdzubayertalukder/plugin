<?php

namespace Plugin\TlcommerceCore\Repositories;

use Plugin\TlcommerceCore\Models\CustomNotifications;

class MarketingRepository
{

    /**
     * Will return custom notification lists
     */

    public function customNotifications($request)
    {
        try {
            $query = CustomNotifications::query();
            $notifications = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();
            return $notifications;
        } catch (\Exception $e) {
            return [];
        }
    }
}
