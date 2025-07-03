<?php

use Illuminate\Support\Facades\Route;
use Plugin\SupportTicket\Http\Controllers\SupportTicketController;


Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => getSaasPrefix()], function () {
        //ticket categories
        Route::post('/support-ticket-update-category', [SupportTicketController::class, 'updateCategory'])->name('update.ticket.category')->middleware('demo');
        Route::post('/support-ticket-category-delete', [SupportTicketController::class, 'deleteCategory'])->name('ticket.category.delete')->middleware('demo');
        Route::post('/support-create-ticket-category', [SupportTicketController::class, 'createCategory'])->name('store.ticket.category')->middleware('demo');

        //Support Ticket
        Route::post('/store-support-ticket', [SupportTicketController::class, 'storeSupportTicket'])->name('store.support.ticket')->middleware('demo');
        Route::post('/support-ticket-content-image', [SupportTicketController::class, 'ticketContentImage'])->name('support.ticket.content.image');
        Route::post('/update-support-tickets', [SupportTicketController::class, 'updateSupportTicket'])->name('update.support.ticket')->middleware('demo');
        Route::post('/reply-support-ticket', [SupportTicketController::class, 'replySupportTicket'])->name('reply.support.ticket')->middleware('demo');
        Route::post('/support-ticket-delete', [SupportTicketController::class, 'supportTicketDelete'])->name('admin.support.ticket.delete')->middleware('demo');
    });

    Route::group(['prefix' => getAdminPrefix()], function () {
        //ticket categories
        Route::get('/support-ticket-categories', [SupportTicketController::class, 'allCategories'])->name('support.ticket.categories');

        //Support Ticket
        Route::get('/create-support-ticket', [SupportTicketController::class, 'createSupportTicket'])->name('create.support.ticket');
        Route::get('/support-tickets', [SupportTicketController::class, 'supportTickets'])->name('saas.support.tickets');
        Route::get('/edit-support-tickets/{id}', [SupportTicketController::class, 'editSupportTickets'])->name('edit.support.tickets');
        Route::get('/support-ticket-details/{id}', [SupportTicketController::class, 'supportTicketDetails'])->name('support.ticket.details');
    });

    Route::group(['prefix' => getSaasPrefix()], function () {
        //Support Ticket
        Route::get('/create-support-ticket', [SupportTicketController::class, 'createSupportTicket'])->name('create.subscriber.support.ticket');
        Route::get('/support-tickets', [SupportTicketController::class, 'supportTickets'])->name('saas.subscriber.support.tickets');
        Route::get('/edit-support-tickets/{id}', [SupportTicketController::class, 'editSupportTickets'])->name('edit.subscriber.support.tickets');
        Route::get('/support-ticket-details/{id}', [SupportTicketController::class, 'supportTicketDetails'])->name('subscriber.support.ticket.details');
    });
});
