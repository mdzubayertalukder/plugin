<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'message' => $this->getMessage($data),
                    'link' => $this->getLink($data),
                    'time' => $this->getTime($data)
                ];
            })
        ];
    }

    public function getMessage($item)
    {
        $message = $item->data['message'];
        return $message;
    }

    public function getLink($item)
    {
        $link = $item->data['link'];
        return $link;
    }

    public function getTime($item)
    {
        $start_time = $item->created_at;
        $end_time = now();

        $time1 = new DateTime($start_time);
        $time2 = new DateTime($end_time);
        $interval = $time1->diff($time2);

        $month = $interval->format('%m');
        if ($month > 0) {
            return  $month == 1 || $month == 0 ? $month . ' ' . 'Month ago' : $month . ' ' . 'Months ago';
        }
        $day = $interval->format('%d');
        if ($day > 0) {
            return $day == 1 || $day == 0 ? $day . ' ' . 'Day ago' : $day . ' ' . 'Days ago';
        }
        $hour = $interval->format('%h');
        if ($hour > 0) {
            return $hour == 1 || $hour == 0 ? $hour . ' ' . 'Hour ago' : $hour . ' ' . 'Hours ago';
        }
        $min = $interval->format('%i');
        if ($min > 0) {
            return $min == 1 || $min == 0 ? $min . ' Minute ago' : $min . ' Minutes ago';
        }
        $sec = $interval->format('%s');
        if ($sec > 0) {
            return  $sec == 1 || $sec == 0 ? $sec . ' ' . 'Second ago' : $sec . ' ' . 'Seconds ago';
        }

        return null;
    }
}
