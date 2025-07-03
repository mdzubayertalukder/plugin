<?php

namespace Plugin\SupportTicket\Http\Controllers;

use Exception;
use Core\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Plugin\SupportTicket\Models\Ticket;
use Illuminate\Support\Facades\Validator;
use Plugin\SupportTicket\Models\TicketReplies;
use Plugin\SupportTicket\Models\TicketCategory;

class SupportTicketController extends Controller
{
    /**
     * Will return all categories
     */
    public function allCategories()
    {
        $categories = DB::table('tl_support_ticket_categories')->select('*')->paginate(10);
        return view('plugin/support-ticket::support-tickets.admin.categories', compact('categories'));
    }


    /**
     * Will store new category
     */
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:tl_support_ticket_categories,name',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('category_name');
            toastNotification('error', $errorMessage);
            return redirect()->back();
        }
        try {
            $category = new TicketCategory();
            $category->name = $request['category_name'];
            $category->saveOrFail();
            toastNotification('success', translate("Ticket category created successfully"));
            return redirect()->back();
        } catch (\Exception $ex) {
            toastNotification('error', translate("Unable to create ticket category"));
            return redirect()->back();
        }
    }

    /**
     * Will delete new category
     */
    public function deleteCategory(Request $request)
    {
        try {
            $category = TicketCategory::find((int)$request['category_id']);
            $category->delete();
            toastNotification('success', translate("Ticket category deleted successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            toastNotification('error', translate("Unable to delete ticket category"));
            return redirect()->back();
        }
    }

    /**
     * Will update category
     */
    public function updateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:tl_support_ticket_categories,name,' . $request['category_id'],
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('category_name');
            toastNotification('error', $errorMessage);
            return redirect()->back();
        }
        try {
            $category = TicketCategory::find((int)$request['category_id']);
            $category->name = $request['category_name'];
            $category->update();
            toastNotification('success', translate("Ticket category updated successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            toastNotification('error', translate("Unable to update ticket category"));
            return redirect()->back();
        }
    }

    /**
     * Will redirect to ticket creation page
     */
    public function createSupportTicket(): View
    {
        if (auth()->user()->user_type == config('saas.user_type.subscriber')) {
            return view('plugin/support-ticket::support-tickets.user.create_ticket');
        }
        return view('plugin/support-ticket::support-tickets.admin.create_ticket');
    }

    /**
     * Store support ticket
     */
    public function storeSupportTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'priority' => 'required',
            'category' => 'required',
            'details' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $timestamp = time();
            $uniqueId = uniqid();
            $uid = $timestamp . '-' . $uniqueId;

            $ticket = new Ticket();
            $ticket->uuid = $uid;
            $ticket->subject = xss_clean($request['subject']);
            $ticket->priority = $request['priority'];
            $ticket->category = $request['category'];
            $ticket->created_by = Auth::user()->id;
            $ticket->assigned_to = $request['user'];
            $ticket->details = xss_clean($request['details']);
            if ($request->hasFile('ticket_file')) {
                $files = [];
                foreach ($request->file('ticket_file') as $file) {
                    $file_id = saveFileInStorage($file, false);
                    array_push($files, $file_id);
                }
                $attachment = implode(',', $files);
                $ticket->attachment = $attachment;
            }
            $ticket->saveOrFail();


            newTicketArrivedNotification($ticket);
            if (!empty($ticket->assigned_to)) {
                newTicketAssignedNotification($ticket);
            }

            DB::commit();

            toastNotification('success', translate("Ticket creation successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate("Unable to create new ticket"));
            return redirect()->back();
        }
    }

    /**
     * Upload ticket content image
     */
    public function ticketContentImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,svg,jpeg,bmp|max:1020',
        ]);

        if ($validator->passes()) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $image = 'ticket_content_image' . time() . rand() . '.' . $extension;
                $file->move('public/uploaded/ticket/content/', $image);
                $path = '/public/uploaded/ticket/content/' . $image;
                return response()->json(['url' => $path]);
            }
        }
        return response()->json(['error' => $validator->errors()->all()]);
    }

    /**
     * get all support tickets
     */
    public function supportTickets()
    {

        $per_page = 5;
        $match_case = [];
        $request = request();
        if ($request->has('ticket_category') && $request['ticket_category'] != null && $request['ticket_category'] != '-1') {
            array_push($match_case, ['tl_support_tickets.category', '=', $request['ticket_category']]);
        }
        if ($request->has('user') && $request['user'] != null  && $request['user'] != '-1') {
            array_push($match_case, ['tl_support_tickets.created_by', '=', $request['user']]);
        }
        if ($request->has('ticket_priority') && $request['ticket_priority'] != null && $request['ticket_priority'] != '-1') {
            array_push($match_case, ['tl_support_tickets.priority', '=', $request['ticket_priority']]);
        }

        if ($request->has('per_page') && $request['per_page'] != null) {
            if ($request['per_page'] == 'all') {
                $per_page = 100;
            } else {
                $per_page = $request['per_page'];
            }
        }

        $tickets = Ticket::with('categoryDetails', 'assignedTo', 'createdBy', 'replays')
            ->where($match_case)
            ->where(function ($query) use ($request) {
                $query->orWhere('tl_support_tickets.subject', 'like', '%' . $request['text_search'] . '%')
                    ->orWhere('tl_support_tickets.details', 'like', '%' . $request['text_search'] . '%')
                    ->orWhere('tl_support_tickets.uuid', 'like', '%' . $request['text_search'] . '%');
            })
            ->where(function ($query) use ($request) {

                $user = User::find(Auth::user()->id);

                if (Auth::user()->user_type == config('saas.user_type.subscriber')) {
                    $query->orWhere('tl_support_tickets.created_by', '=', $user->id)
                        ->orWhere('tl_support_tickets.assigned_to', '=', $user->id);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        if (auth()->user()->user_type == config('saas.user_type.subscriber')) {
            return view('plugin/support-ticket::support-tickets.user.all_tickets', compact('tickets'));
        }
        return view('plugin/support-ticket::support-tickets.admin.all_tickets', compact('tickets'));
    }

    /**
     * edit support tickets
     */
    public function editSupportTickets($id)
    {
        $ticket = Ticket::find((int)$id);
        if (auth()->user()->user_type == config('saas.user_type.subscriber')) {
            return view('plugin/support-ticket::support-tickets.user.edit_ticket', compact('ticket'));
        }

        return view('plugin/support-ticket::support-tickets.admin.edit_ticket', compact('ticket'));
    }

    /**
     * Will update support ticket
     */
    public function updateSupportTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'priority' => 'required',
            'category' => 'required',
            'details' => 'required',
        ]);

        try {

            $ticket = Ticket::find($request['id']);

            $assigned_to = $ticket->assigned_to;

            $ticket->subject = xss_clean($request['subject']);
            $ticket->priority = $request['priority'];
            $ticket->category = $request['category'];
            $ticket->assigned_to = $request['user'];
            $ticket->details = xss_clean($request['details']);
            if ($request->hasFile('ticket_file')) {
                $files = [];
                foreach ($request->file('ticket_file') as $file) {
                    $file_id = saveFileInStorage($file, false);
                    array_push($files, $file_id);
                }
                $attachment = implode(',', $files);
                $ticket->attachment = $attachment;
            }
            $ticket->update();


            if (!empty($ticket->assigned_to) && $assigned_to != $ticket->assigned_to) {
                newTicketAssignedNotification($ticket);
            }

            toastNotification('success', translate("Ticket updated successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            toastNotification('error', translate("Unable to update ticket"));
            return redirect()->back();
        }
    }

    /**
     * Will show support ticket details
     */
    public function supportTicketDetails($id)
    {
        $ticket = Ticket::with('categoryDetails', 'createdBy', 'assignedTo')
            ->find($id);

        $ticket_replies = TicketReplies::with('repliedBy')
            ->where('ticket_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        $all_other_tickets = Ticket::where('created_by', $ticket->created_by)->with('categoryDetails', 'assignedTo', 'createdBy', 'replays')->get();

        if (auth()->user()->user_type == config('saas.user_type.subscriber')) {
            return view('plugin/support-ticket::support-tickets.user.ticket_details', compact('ticket', 'ticket_replies', 'all_other_tickets'));
        }

        return view('plugin/support-ticket::support-tickets.admin.ticket_details', compact('ticket', 'ticket_replies', 'all_other_tickets'));
    }

    /**
     * Reply to support ticket
     */
    public function replySupportTicket(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'details' => 'required',
            'status' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $ticket_replay = new TicketReplies();
            $ticket_replay->ticket_id = $request['id'];
            $ticket_replay->details = xss_clean($request['details']);

            if ($request->hasFile('ticket_file')) {
                $files = [];
                foreach ($request->file('ticket_file') as $file) {
                    $file_id = saveFileInStorage($file, false);
                    array_push($files, $file_id);
                }
                $attachment = implode(',', $files);
                $ticket_replay->attachment = $attachment;
            }

            if (!empty($request['status'] || $request['status'] == 0)) {
                $ticket_replay->status = $request['status'];
            }
            $ticket_replay->replied_by = Auth::user()->id;
            $ticket_replay->saveOrFail();

            $ticket = Ticket::find((int)$request['id']);
            if (!empty($request['status']) || $request['status'] == 0) {
                $ticket->status = $request['status'];
                $ticket->update();
            }

            newReplyArrivedNotification($ticket, $ticket_replay);
            DB::commit();
            toastNotification('success', translate("Replied Successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            DB::rollback();
            toastNotification('error', translate("Unable to reply"));
            return redirect()->back();
        }
    }

    /**
     * Delete support ticket
     */
    public function supportTicketDelete(Request $request)
    {
        try {
            $ticket = Ticket::find((int)$request['ticket_id']);
            $ticket->delete();
            toastNotification('success', translate("Ticket deleted successfully"));
            return redirect()->back();
        } catch (Exception $ex) {
            toastNotification('error', translate("Unable to delete ticket"));
            return redirect()->back();
        }
    }
}
