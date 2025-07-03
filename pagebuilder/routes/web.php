<?php

use Illuminate\Support\Facades\Route;
use Plugin\PageBuilder\Http\Controllers\PageBuilderController;
use Plugin\PageBuilder\Http\Controllers\PageBuilderUpdateController;

Route::group(['prefix' => getAdminPrefix()], function () {

    Route::middleware(['can:Manage Page Builder'])->group(function () {
        // Section Routes
        Route::get('page-builder-sections', [PageBuilderController::class, 'pageSections'])->name('plugin.builder.pageSections');
        Route::post('page-builder/section/create', [PageBuilderController::class, 'newSection'])->name('plugin.builder.pageSection.new');
        Route::post('page-builder/section/remove', [PageBuilderController::class, 'removeSection'])->name('plugin.builder.pageSection.remove');
        Route::post('page-builder/section/sorting', [PageBuilderController::class, 'sortingSection'])->name('plugin.builder.pageSection.sorting');
        Route::post('page-builder/section/get-properties', [PageBuilderController::class, 'getSectionProperties'])->name('plugin.builder.pageSection.get.properties');
        Route::post('page-builder/update-properties', [PageBuilderController::class, 'updateSectionProperties'])->name('plugin.builder.pageSection.update.properties');

        // Widget Routes
        Route::post('page-builder/widget/add', [PageBuilderController::class, 'addWidget'])->name('plugin.builder.pageSection.widget.add');
        Route::post('page-builder/widget/remove', [PageBuilderController::class, 'removeWidget'])->name('plugin.builder.pageSection.widget.remove');
        Route::post('page-builder/widget/update-position', [PageBuilderController::class, 'updateWidgetPosition'])->name('plugin.builder.pageSection.widget.updatePosition');
        Route::post('page-builder/widget/order', [PageBuilderController::class, 'orderWidget'])->name('plugin.builder.pageSection.widget.order');
        Route::post('page-builder/widget/get-properties', [PageBuilderController::class, 'getWidgetProperties'])->name('plugin.builder.pageSection.widget.get.properties');
        Route::post('page-builder/widget/update-properties', [PageBuilderController::class, 'updateWidgetProperties'])->name('plugin.builder.pageSection.widget.update.properties');

        // Text Editor Image Upload
        Route::post('page-builder/text-editor/upload', [PageBuilderController::class, 'imageUpload'])->name('plugin.builder.pageSection.text-editor.upload');

        // Plugin Update Routes
        Route::get('pagebuilder-updater', [PageBuilderUpdateController::class, 'updateList'])->name('plugin.pagebuilder.check.update');
        Route::post('update-pagebuilder', [PageBuilderUpdateController::class, 'updatePageBuilder'])->name('plugin.pagebuilder.update');

        // Banner Widgets Routes
        Route::get('review/modal/show', [PageBuilderController::class, 'showReviewModal'])->name('plugin.builder.pageSection.review.show');
        Route::post('review/modal/save', [PageBuilderController::class, 'saveReview'])->name('plugin.builder.pageSection.review.save');
        Route::delete('review/modal/delete', [PageBuilderController::class, 'deleteReview'])->name('plugin.builder.pageSection.review.delete');
    });
});
