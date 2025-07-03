<?php

namespace Plugin\SupportTicket\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class TicketReplies extends Model
{
    protected $table = "tl_support_ticket_replies";

    protected $casts = [
        'replied_at' => 'datetime',
    ];
    /**
     * making relation with replied by user
     */
    public function repliedBy()
    {
        return $this->hasOne(User::class, 'id', 'replied_by');
    }

    public function ticket_status_name()
    {
        $status_name = '';
        $ticket_status = config('support-ticket.ticket_status');
        foreach ($ticket_status as $name => $status) {
            if ($status == $this->status) {
                $status_name =
                    $status_name . '' . ucwords(implode(' ', explode('_', $name)));
            }
        }
        return $status_name;
    }
}
