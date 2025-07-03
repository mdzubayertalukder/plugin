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
        @include('plugin/saas::user.panel.includes.header')
        <!-- End Header -->

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Sidebar -->
            @include('core::base.layouts.navbar')
            <!-- End Sidebar -->

            <!-- Main Content -->
            <div class="main-content">
                <div class="container-fluid">
                    @php
                        $crossed_privileges = [];
                        if (session()->get('product_limit_over') == 1) {
                            array_push($crossed_privileges, 'product creation');
                        }
                        if (session()->get('blog_limit_over') == 1) {
                            array_push($crossed_privileges, 'blog creation');
                        }
                        if (session()->get('page_limit_over') == 1) {
                            array_push($crossed_privileges, 'page creation');
                        }
                        if (session()->get('storage_limit_over') == 1) {
                            array_push($crossed_privileges, 'storage');
                        }
                        if (session()->get('customer_limit_over') == 1) {
                            array_push($crossed_privileges, 'customer');
                        }

                        $request = app('request');
                        $current_domain = $request->getHost();
                        $saas_account_id = session()->get('saas_account_id');
                        $upgrade_url =
                            env('APP_URL') .
                            '/' .
                            getSaasPrefix() .
                            '/change-subscription-plan' .
                            '/' .
                            $saas_account_id;
                        $user_type = auth()->user()->user_type;
                    @endphp
                    @if (auth()->check() &&
                            $user_type == config('tlecommercecore.user_type.admin') &&
                            !empty($crossed_privileges) &&
                            isTenant())
                        <div class="alert alert-warning align-items-center d-flex justify-content-between" role="alert">
                            {{ translate('Your ' . implode(', ', $crossed_privileges) . ' quota is over, to increase quota please upgrade your subscription plan.') }}
                            <a href="{{ $upgrade_url }}"
                                class="btn btn-danger sm btn-square float-right p-2">{{ translate('Upgrade Subscription') }}</a>
                        </div>
                    @endif
                    @if (auth()->check() &&
                            $user_type == config('tlecommercecore.user_type.seller') &&
                            session()->get('product_limit_over') == 1 &&
                            isTenant())
                        <div class="alert alert-warning align-items-center d-flex justify-content-between"
                            role="alert">
                            {{ translate('Your product creation quota is over.Please contact with admin.') }}
                        </div>
                    @endif
                    @yield('main_content')
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
