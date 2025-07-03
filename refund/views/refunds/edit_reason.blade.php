@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Refund Reason') }}
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Edit Refund Reason') }}</h4>
    </div>
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="mb-3">
                <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version
                </p>
            </div>
            <ul class="nav nav-tabs nav-fill border-light border-0">
                @foreach ($languages as $key => $language)
                    <li class="nav-item">
                        <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                            href="{{ route('plugin.refund.reason.edit', ['id' => $reason->id, 'lang' => $language->code]) }}">
                            <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}" width="20px">
                            <span>{{ $language->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="form-element py-30 mb-30">
                <form action="{{ route('plugin.refund.reason.update') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black">{{ translate('Reason') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="hidden" name="id" value="{{ $reason->id }}">
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <input type="text" name="name" class="theme-input-style"
                                value="{{ $reason->translation('name', $lang) }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
