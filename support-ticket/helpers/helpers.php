<?php

use Core\Models\User;
use Illuminate\Support\Facades\Mail;
use Plugin\SupportTicket\Mail\CommonSaasEmail;
use Plugin\SupportTicket\Models\TicketCategory;
use Plugin\SupportTicket\Notifications\UserNotification;

if (!function_exists('getAllTicketCategories')) {
	/**
	 * get all ticket categories
	 */

	function getAllTicketCategories()
	{
		$categories = TicketCategory::all();
		return $categories;
	}
}

if (!function_exists('getAllStaffs')) {
	/**
	 * get all staffs
	 */

	function getAllStaffs()
	{
		$users = User::whereNot('user_type', config('saas.user_type.subscriber'))->get();
		return $users;
	}
}

if (!function_exists('getAllUsers')) {
	/**
	 * get all users
	 */

	function getAllUsers()
	{
		$users = User::all();
		return $users;
	}
}

if (!function_exists('newTicketArrivedNotification')) {
	/**
	 * will send new ticket arrived notification
	 *
	 */
	function newTicketArrivedNotification($ticket)
	{
		$attachments = !empty($ticket->attachment) ? explode(',', $ticket->attachment) : [];
		$attachment_string = !empty($attachments) ? '<p><b>Attachments:</b></p>' : '';
		foreach ($attachments as $key => $item) {
			$attachment_string = $attachment_string . '<a href="' . asset(getFilePath($item)) . '" style="margin-right:6px">Attachment ' . ($key + 1) . '</a> ';
		}
		$ticket_details = $ticket->details;
		$ticket_details = $attachment_string . $ticket_details;

		if (strpos($ticket_details, '/public/uploaded/ticket') !== false) {
			$replacement = 'https://' . env('CENTRAL_DOMAIN') . '/public/uploaded/ticket';
			$ticket_details = str_replace('/public/uploaded/ticket', $replacement, $ticket_details);
		}


		$mail_data = [
			'template_id' => 27,
			'subject' => getSubjectByTemplateId(27),
			'keywords' => getEmailTemplateVariables(27, true),

			'_system_name_' => env('APP_NAME'),
			'_admin_user_name_' => '',
			'_ticket_owner_name_' => $ticket->createdBy->name,
			'_ticket_uuid_' => $ticket->uuid,
			'_ticket_category_' => $ticket->categoryDetails->name,
			'_ticket_subject_' => $ticket->subject,
			'_ticket_link_' => 'https://' . env('CENTRAL_DOMAIN') . '/admin/support-ticket-details/' . $ticket->id,
			'_ticket_details_' => $ticket_details
		];

		$admins = getAllNotifiableAdmin();
		foreach ($admins as $admin) {
			$mail_data['_admin_user_name_'] = $admin->name;
			$data = [
				'message' => "A new ticket arrived",
				'link' => '/admin/support-ticket-details/' . $ticket->id
			];
			$admin->notify(new UserNotification($data));
			Mail::to($admin->email)->send(new CommonSaasEmail($mail_data));
		}
	}
}

if (!function_exists('newTicketAssignedNotification')) {
	/**
	 * will send new ticket assigned notification
	 *
	 */
	function newTicketAssignedNotification($ticket)
	{
		$attachments = !empty($ticket->attachment) ? explode(',', $ticket->attachment) : [];
		$attachment_string = !empty($attachments) ? '<p><b>Attachments:</b></p>' : '';
		foreach ($attachments as $key => $item) {
			$attachment_string = $attachment_string . '<a href="' . asset(getFilePath($item)) . '" style="margin-right:6px">Attachment ' . ($key + 1) . '</a> ';
		}

		$ticket_details = $ticket->details;
		$ticket_details = $attachment_string . $ticket_details;

		if (strpos($ticket_details, '/public/uploaded/ticket') !== false) {
			$replacement = 'https://' . env('CENTRAL_DOMAIN') . '/public/uploaded/ticket';
			$ticket_details = str_replace('/public/uploaded/ticket', $replacement, $ticket_details);
		}

		$mail_data = [
			'template_id' => 26,
			'subject' => getSubjectByTemplateId(26),
			'keywords' => getEmailTemplateVariables(26, true),

			'_system_name_' => env('APP_NAME'),
			'_assigned_to_user_name_' => $ticket->assignedTo->name,
			'_ticket_owner_name_' => $ticket->createdBy->name,
			'_ticket_uuid_' => $ticket->uuid,
			'_ticket_category_' => $ticket->categoryDetails->name,
			'_ticket_subject_' => $ticket->subject,
			'_ticket_link_' => 'https://' . env('CENTRAL_DOMAIN') . '/admin/support-ticket-details/' . $ticket->id,
			'_ticket_details_' => $ticket_details
		];

		$data = [
			'message' => "A new ticket assigned to you",
			'link' => '/admin/support-ticket-details/' . $ticket->id
		];
		$ticket->assignedTo->notify(new UserNotification($data));
		Mail::to($ticket->assignedTo->email)->send(new CommonSaasEmail($mail_data));
	}
}


if (!function_exists('newReplyArrivedNotification')) {
	/**
	 * will send new reply arrived notification
	 *
	 */
	function newReplyArrivedNotification($ticket, $ticket_reply)
	{
		$attachments = !empty($ticket_reply->attachment) ? explode(',', $ticket_reply->attachment) : [];
		$attachment_string = !empty($attachments) ? '<p><b>Attachments:</b></p>' : '';
		foreach ($attachments as $key => $item) {
			$attachment_string = $attachment_string . '<a href="' . asset(getFilePath($item)) . '" style="margin-right:6px">Attachment ' . ($key + 1) . '</a> ';
		}

		$ticket_reply->details = $attachment_string . $ticket_reply->details;

		if (strpos($ticket_reply->details, '/public/uploaded/ticket') !== false) {
			$replacement = 'https://' . env('CENTRAL_DOMAIN') . '/public/uploaded/ticket';
			$ticket_reply->details = str_replace('/public/uploaded/ticket', $replacement, $ticket_reply->details);
		}

		$mail_data = [
			'template_id' => 28,
			'subject' => getSubjectByTemplateId(28),
			'keywords' => getEmailTemplateVariables(28, true),
			'_system_name_' => env('APP_NAME'),
			'_replay_to_user_name_' => '',
			'_ticket_owner_name_' => $ticket->createdBy->name,
			'_ticket_uuid_' => $ticket->uuid,
			'_ticket_category_' => $ticket->categoryDetails->name,
			'_ticket_subject_' => $ticket->subject,
			'_ticket_link_' => '',
			'_ticket_reply_' => $ticket_reply->details
		];

		$all_users = allUsersRelatedToTheTicket($ticket, $ticket_reply);
		foreach ($all_users as $user) {
			$mail_data['_reply_to_user_name_'] = $user->name;
			if ($user->user_type == config('saas.user_type.subscriber')) {
				$mail_data['_ticket_link_'] = 'https://' . env('CENTRAL_DOMAIN') . '/' . getSaasPrefix() . '/support-ticket-details' . '/' . $ticket->id;
			} else {
				$mail_data['_ticket_link_'] = 'https://' . env('CENTRAL_DOMAIN') . '/admin/support-ticket-details/' . $ticket->id;
			}

			$data = [
				'message' => "A new reply arrived",
				'link' => ''
			];

			if ($user->user_type == config('saas.user_type.subscriber')) {
				$data['link'] = '/' . getSaasPrefix() . '/support-ticket-details' . '/' . $ticket->id;
			} else {
				$data['link'] = '/admin/support-ticket-details/' . $ticket->id;
			}

			$user->notify(new UserNotification($data));
			Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
		}
	}
}

if (!function_exists('getAllNotifiableAdmin')) {
	/**
	 * will return all permitted admin's info
	 *
	 */
	function getAllNotifiableAdmin()
	{
		$supper_admins = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'tl_users.id')
			->where('role_id', config('settings.roles.supper_admin'))
			->get();

		$other_staffs = User::join('model_has_roles', 'tl_users.id', '=', 'model_has_roles.model_id')
			->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
			->where('role_has_permissions.permission_id', config('support-ticket.support_ticket_permission'))
			->get();


		$all_admins = $supper_admins->merge($other_staffs);

		return $all_admins;
	}
}

if (!function_exists('allUsersRelatedToTheTicket')) {
	/**
	 * will return all users related to the ticket
	 *
	 */
	function allUsersRelatedToTheTicket($ticket, $ticket_reply)
	{
		$ticket_owner = $ticket->createdBy;
		$users = User::join('tl_support_ticket_replies', 'tl_support_ticket_replies.replied_by', '=', 'tl_users.id')
			->where('ticket_id', $ticket->id)
			->whereNot('tl_support_ticket_replies.replied_by', $ticket_reply->replied_by)
			->select('tl_users.*')
			->get();
		$all_users = $users->push($ticket_owner);
		return $all_users;
	}
}
if (!function_exists('getSubjectByTemplateId')) {
	function getSubjectByTemplateId($template_id)
	{
		$email_template_properties = DB::table('tl_email_template_properties')
			->where('tl_email_template_properties.email_type', '=', $template_id)
			->first();
		return $email_template_properties == null ? '' : $email_template_properties->subject;
	}
}
