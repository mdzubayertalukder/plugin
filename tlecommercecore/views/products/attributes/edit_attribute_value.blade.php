@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Attribute value') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Edit Attribute Value') }}</h4>
    </div>
    <div class="row">
        <div class="col-lg-5 mx-auto">
            <div class="form-element py-30 mb-30">
                <form action="{{ route('plugin.tlcommercecore.product.attributes.values.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" name="id" value="{{ $value_details->id }}">
                            <input type="hidden" name="attribute_id" value="{{ $value_details->attribute_id }}">
                            <input type="text" name="name" class="theme-input-style"
                                value="{{ $value_details->name }}" placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
