<div class="card">
    <div class="card-body">
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black">{{ translate('Customer auto approval') }}
                </label>
            </div>
            <div class="col-sm-6">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="customer_auto_approved" @checked(getEcommerceSetting('customer_auto_approved') == config('settings.general_status.active'))>
                    <span class="control"></span>
                </label>
            </div>
        </div>
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black">{{ translate('Customer email verification') }}
                </label>
            </div>
            <div class="col-sm-1">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="customer_email_varification" @checked(getEcommerceSetting('customer_email_varification') == config('settings.general_status.active'))>
                    <span class="control"></span>
                </label>
            </div>
            <div class="col-sm-5">
                <p class="mt-0 font-13">
                    {{ translate('Enable customer email verification you need to complete email configuration and Cron Job setup') }}
                    <br>
                    <a href="{{ route('core.email.smtp.configuration') }}"
                        class="btn-link">{{ translate('Configure Email') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
