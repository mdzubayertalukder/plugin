@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Payout Info') }}
@endsection
@section('custom_css')
@endsection
@section('seller_main_content')
    @if (auth()->user()->shop->status == config('settings.general_status.active'))
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-3 pb-3">
            <h4><i class="icofont-ui-settings"></i> {{ translate('Payout Settings') }}</h4>
        </div>
        <div class="row">
            <!--Payout settings-->
            <div class="col-lg-12">
                <form method="POST"
                    action="{{ route('plugin.multivendor.seller.dashboard.earning.payout.settings.update') }}">
                    @csrf
                    <div class="card mb-30">
                        <div class="card-header bg-white border-bottom2">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h4 class="py-2">{{ translate('Payout Information') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Bank Name') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="hidden" name="seller_id" value="{{ auth()->user()->id }}">
                                    <input type="text" name="bank_name" class="theme-input-style"
                                        placeholder="{{ translate('Enter Bank Name') }}"
                                        value="{{ $payout_info != null ? $payout_info->bank_name : '' }}">
                                    @if ($errors->has('bank_name'))
                                        <div class="invalid-input">{{ $errors->first('bank_name') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Bank Code') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="bank_code" class="theme-input-style"
                                        placeholder="{{ translate('Enter Bank Code') }}"
                                        value="{{ $payout_info != null ? $payout_info->bank_code : '' }}">
                                    @if ($errors->has('bank_code'))
                                        <div class="invalid-input">{{ $errors->first('bank_code') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Account Name') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="account_name" class="theme-input-style"
                                        placeholder="{{ translate('Account Name') }}"
                                        value="{{ $payout_info != null ? $payout_info->account_name : '' }}">
                                    @if ($errors->has('account_name'))
                                        <div class="invalid-input">{{ $errors->first('account_name') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Account Number') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="account_number" class="theme-input-style"
                                        placeholder="{{ translate('Enter Account Number') }}"
                                        value="{{ $payout_info != null ? $payout_info->account_number : '' }}">
                                    @if ($errors->has('account_number'))
                                        <div class="invalid-input">{{ $errors->first('account_number') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Account Holder Name') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="account_holder_name" class="theme-input-style"
                                        placeholder="{{ translate('Account Holder Name') }}"
                                        value="{{ $payout_info != null ? $payout_info->account_holder_name : '' }}">
                                    @if ($errors->has('account_holder_name'))
                                        <div class="invalid-input">{{ $errors->first('account_holder_name') }}</div>
                                    @endif
                                </div>
                            </div>


                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black">{{ translate('Bank Routing Number') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="bank_routing_number" class="theme-input-style"
                                        placeholder="{{ translate('Enter Bank Routing Number') }}"
                                        value="{{ $payout_info != null ? $payout_info->bank_routing_number : '' }}">
                                    @if ($errors->has('bank_routing_number'))
                                        <div class="invalid-input">{{ $errors->first('bank_routing_number') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Save Change') }}</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <!--End Payout settings-->
        </div>
    @else
        <p class="alert alert-info">Your Shop is Inactive. Please contact with Administration </p>
    @endif
@endsection
@section('custom_scripts')
@endsection
