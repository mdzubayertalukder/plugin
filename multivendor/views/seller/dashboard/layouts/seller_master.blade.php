@php
    use Core\Views\Composer\Core;
    $shareable_data = new Core();
    $active_langs = $shareable_data->active_langs;
    $active_lang = $shareable_data->active_lang;
    $style_path = $shareable_data->style_path;
    $mood = $shareable_data->mood;
    
    $logo_details = getGeneralSettingsDetails();
    
    $chink_size_object = getChunkSize();
    $chink_size = 256000000;
    if ($chink_size_object != null) {
        $chink_size = $chink_size_object->value;
    }
    
    $placeholder_info = getPlaceHolderImage();
    $placeholder_image = '';
    $placeholder_image_alt = '';
    
    if ($placeholder_info != null) {
        $placeholder_image = $placeholder_info->placeholder_image;
        $placeholder_image_alt = $placeholder_info->placeholder_image_alt;
    }
@endphp
@include('core::base.layouts.head')

<body>
    <!-- Offcanval Overlay -->
    <div class="offcanvas-overlay"></div>
    <!-- Offcanval Overlay -->
    <!-- Wrapper -->
    <div class="wrapper">
        <!-- Header -->
        @include('plugin/multivendor::seller.dashboard.layouts.seller_header')
        <!-- End Header -->

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Sidebar -->
            @include('plugin/multivendor::seller.dashboard.layouts.side_bar')
            <!-- End Sidebar -->

            <!-- Main Content -->
            <div class="main-content">
                <div class="container-fluid">
                    @include('core::base.layouts.dark_light_switcher')
                    @if (auth()->user()->status == config('settings.general_status.active'))
                        @yield('seller_main_content')
                    @else
                        <p class="alert alert-info">Your Account is Inactive. Please contact with Administration </p>
                    @endif
                </div>
            </div>
            <!-- End Main Content -->
        </div>
        <!-- End Main Wrapper -->

        <!-- Footer -->
        @include('core::base.layouts.footer')
        <!-- End Footer -->
    </div>
    <!-- End wrapper -->
    @include('core::base.layouts.script')
</body>

</html>
