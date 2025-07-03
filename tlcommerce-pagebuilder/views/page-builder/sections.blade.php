@php
    $view_path = 'plugin/tlcommerce-pagebuilder';
    $preview_url = Plugin\TlPageBuilder\Helpers\BuilderHelper::$preview_url . $data['permalink'];
    $plugin_dependencies_widgets = ['flash_deal' => 'flashdeal', 'seller_list' => 'multivendor'];
@endphp
@extends('core::base.layouts.master')

@section('title')
    {{ translate('Page Builder') }}
@endsection

@section('custom_css')
    <!-- Jquery UI -->
    <link href="{{ asset('/public/backend/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <!-- Summernote -->
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">

    @include("$view_path::page-builder.includes.styles")
@endsection

@section('main_content')
    <div class="row">
        <div class="col-md-8">
            <!-- Page Section List Start -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mt-2">{{ $data['title'] }}</h5>

                    <div class="right-action d-flex gap-10">
                        <a href="{{ $preview_url }}" target="_blank" title="Preview Page">
                            <i class="icofont-eye-alt fz-25"></i>
                        </a>
                        <a href="#" class="text-white load-widget-list" title="Widgets List">
                            <svg width="25" height="25" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="controlpanel__active___3UfF3">
                                <path
                                    d="M10.416 3H3v7.416h7.416V3ZM22 3h-7.416v7.416H22V3ZM22 14.583h-7.416V22H22v-7.417ZM10.416 14.583H3V22h7.416v-7.417Z"
                                    stroke="#fff" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="section-list mx-2">
                        @foreach ($sections as $section)
                            <div class="row single-section p-1 rounded" id="section_{{ $section->id }}">

                                <a href="#" class="black my-auto drag-layout">
                                    <i class="icofont-drag"></i>
                                </a>

                                <div class="row my-2 mx-0 col-11 bg-transparent layout-height">
                                    @foreach ($section->layouts as $layout)
                                        <div class="col-{{ $layout->col_value }} p-0 section-column"
                                            style="border:1px solid" data-section-layout-id="{{ $layout->id }}">
                                            <!-- Layout Widgets -->
                                            @if (count($layout->layout_widgets))
                                                @foreach ($layout->layout_widgets as $layout_widget)
                                                    @php
                                                        $bg = '';
                                                        $widget_name = $layout_widget->widget->name;
                                                        if (
                                                            array_key_exists($widget_name, $plugin_dependencies_widgets)
                                                        ) {
                                                            if (
                                                                !isActivePluging(
                                                                    $plugin_dependencies_widgets[$widget_name],
                                                                )
                                                            ) {
                                                                $bg = 'bg-deactive';
                                                            }
                                                        }

                                                    @endphp
                                                    <div class="section-widget single-widget"
                                                        data-widget="{{ $layout_widget->widget->name }}"
                                                        data-widget-id="{{ $layout_widget->widget->id }}"
                                                        data-layout-widget-id="{{ $layout_widget->id }}">

                                                        <div
                                                            class="{{ $bg }} card card-body flex-row bg-transparent justify-content-between px-3 py-3 flex-wrap gap-10 ">
                                                            <span
                                                                class="font-14 black bold">{{ $layout_widget->widget->full_name }}</span>

                                                            <div class="widget-icons widget-actions">
                                                                <a href="javascript:void(0);" class="black dragWidget">
                                                                    <i class="icofont-drag1"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" class="black editWidget">
                                                                    <i class="icofont-options mx-1"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" class="black removeWidget">
                                                                    <i class="icofont-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <a href="#" class="black my-auto edit-section">
                                    <i class="icofont-options"></i>
                                </a>
                                <a href="#" class="black my-auto ml-2 remove-section">
                                    <i class="icofont-trash"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if (!count($sections))
                        <p class="alert alert-danger text-center">{{ translate('No Section Found') }}</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <div class="btn btn-primary sm" id="add_new_section_btn">{{ translate('Add New Section') }}</div>
                </div>
            </div>
            <!-- Page Section List End -->
        </div>

        <div class="col-md-4 builder-sidebar">
            <div class="card mb-30" id="properties-section">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4>{{ translate('Available Widgets') }}</h4>
                    <a href="#" class="text-white load-widget-list" title="Widgets List">
                        <svg width="25" height="25" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="controlpanel__active___3UfF3">
                            <path
                                d="M10.416 3H3v7.416h7.416V3ZM22 3h-7.416v7.416H22V3ZM22 14.583h-7.416V22H22v-7.417ZM10.416 14.583H3V22h7.416v-7.417Z"
                                stroke="#fff" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </a>
                </div>
                <div class="card-body">
                    <!--Widgets List-->
                    <div id="widget-list-body" class="widget-list-wrapper">
                        <input type="text" name="" id="widget-search" class="form-control mb-3"
                            placeholder="{{ translate('Search Widget') }}">

                        <div class="widget-list custom-scroll row">
                            @foreach ($widgets as $widget)
                                @if (array_key_exists($widget['name'], $plugin_dependencies_widgets))
                                    @if (!isActivePluging($plugin_dependencies_widgets[$widget['name']]))
                                        @continue
                                    @endif
                                @endif

                                <div class="widget-single mb-2 text-center col-lg-6 mb-2"
                                    data-widget="{{ $widget['name'] }}" data-widget-id="{{ $widget['id'] }}">
                                    <div class="card card-body bg-transparent">
                                        <span class="font-14 black bold widget-title">{{ $widget['full_name'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!--End Widget List-->
                    <!--Properties-->
                    <div id="properties-body" class="properties-wrapper  d-none">
                        <form action="javascript:void(0);" method="post" id="properties-form">
                            <div class="property-fields custom-scroll">
                                {{-- Section/Widget Properties --}}
                            </div>
                            <div class="form-row save-section mt-3 properties-action-area">
                                <input type="hidden" name="type_key">
                                <input type="hidden" name="section_id">
                                <input type="hidden" name="layout_has_widget_id">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--End Properties-->

                </div>
            </div>
        </div>
    </div>

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <input type="hidden" id="delete-id" name="id">
                    <input type="hidden" id="section-id" name="section-id">
                    <button type="button" class="btn long mt-2 btn-danger"
                        data-dismiss="modal">{{ translate('cancel') }}</button>
                    <button type="submit" class="btn long mt-2" id="delete-btn">{{ translate('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->

    <!--Layout Select Modal-->
    @include("$view_path::page-builder.includes.layout-modal")
    <!--Layout Select Modal-->

    <!-- Media Modal-->
    @include('core::base.media.partial.media_modal')
    <!-- Media Modal-->
@endsection
@section('custom_scripts')
    <!-- Load The Srcripts -->
    @include("$view_path::page-builder.includes.scripts")
    @includeIf("$view_path::page-builder.includes.widgets_scripts")
@endsection
