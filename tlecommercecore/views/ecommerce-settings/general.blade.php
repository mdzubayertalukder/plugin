<div class="card">
    <div class="card-body">
        @if (count($currencies) > 0)
            <div class="form-row mb-20">
                <div class="col-sm-4">
                    <label class="font-14 bold black">{{ translate('Defalt currency') }}
                    </label>
                </div>
                <div class="col-sm-4">
                    <select class="form-control" name="default_currency">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}" @if (getEcommerceSetting('default_currency') == $currency->id) selected @endif>
                                {{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <p class="mt-0 font-13"> {{ translate('You can manage your currencies from') }}
                        <a href="{{ route('plugin.tlcommercecore.ecommerce.all.currencies') }}"
                            class="btn-link">{{ translate('Currencies Module') }}
                        </a>
                    </p>
                </div>
            </div>
        @else
            <p class="mt-0 font-13">
                {{ translate('To set default currency, plaese create a currency') }} <a
                    href="{{ route('plugin.tlcommercecore.ecommerce.all.currencies') }}"
                    class="btn-link">{{ translate('click here') }}</a></p>
        @endif

        @php
            $all_pages = \Core\Models\TlPage::where('publish_status', config('settings.general_status.active'))
                ->select('id', 'title')
                ->get();
        @endphp
        <div class="form-row mb-20">
            <div class="col-sm-4">
                <label class="font-14 bold black">{{ translate('Customer Term & Condition Page') }}
                </label>
            </div>
            <div class="col-sm-4">
                <select class="form-control" name="customer_term_condition_page">
                    <option value="">{{ translate('Select a page') }}</option>
                    @foreach ($all_pages as $page)
                        <option value="{{ $page->id }}" @selected(getEcommerceSetting('customer_term_condition_page') == $page->id)>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <p class="mt-0 font-13">
                    {{ translate('To create new page or manage existing pages from') }}
                    <a href="{{ route('core.page') }}" class="btn-link">{{ translate('Pages Module') }}
                    </a>
                </p>
            </div>
        </div>
        @if (isActivePluging('multivendor'))
            <div class="form-row mb-20">
                <div class="col-sm-4">
                    <label class="font-14 bold black">{{ translate('Seller Term & Condition Page') }}
                    </label>
                </div>
                <div class="col-sm-4">
                    <select class="form-control" name="seller_term_condition_page">
                        <option value="">{{ translate('Select a page') }}</option>
                        @foreach ($all_pages as $page)
                            <option value="{{ $page->id }}" @selected(getEcommerceSetting('seller_term_condition_page') == $page->id)>
                                {{ $page->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <p class="mt-0 font-13">
                        {{ translate('To create new page or manage existing pages from') }}
                        <a href="{{ route('core.page') }}" class="btn-link">{{ translate('Pages Module') }}
                        </a>
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
