<?php

namespace Plugin\SupportTicket\Models;

use Core\Models\User;
use Plugin\SupportTicket\Models\TicketCategory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tl_support_tickets";

    /**
     * relationship with created by user
     */
    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * relationship with assigned user
     */
    public function assignedTo()
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    /**
     * relationship with ticket category
     */
    public function categoryDetails()
    {
        return $this->hasOne(TicketCategory::class, 'id', 'category');
    }

    /**
     * relation with ticket replays
     */
    public function replays()
    {
        return $this->hasMany(TicketReplies::class, 'ticket_id', 'id');
    }

    /**
     * Get the last reply for the ticket
     */
    public function lastReply()
    {
        return $this->replays()->latest('created_at')->first();
    }

    /**
     * Return priority class
     */
    public function priority_class()
    {
        $priority_class = '';
        if ($this->priority == 'high') {
            $priority_class = 'badge badge-danger';
        }
        if ($this->priority == 'urgent') {
            $priority_class = 'badge badge-danger';
        }
        if ($this->priority == 'low') {
            $priority_class = 'badge badge-primary';
        }
        if ($this->priority == 'medium') {
            $priority_class = 'badge badge-warning';
        }

        return $priority_class;
    }

    /**
     * Return ticket status name
     */
    public function status_name()
    {
        $status_name = '';
        $ticket_status = config('support-ticket.ticket_status');
        foreach ($ticket_status as $name => $status) {
            if ($status == $this->status) {
                $status_name =
                    $status_name .
                    '' .
                    ucwords(implode(' ', explode('_', $name)));
            }
        }

        return $status_name;
    }
}
