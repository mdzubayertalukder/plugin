<form action="#" method="POST" id="subscriberEditForm">
    @csrf
    <input type="hidden" name="id" value="{{ $user->id }}">
    <div class="form-row mb-20">
        <div class="col-md-4">
            <label class="font-14 bold black">{{ translate('Image') }}</label>
        </div>
        <div class="col-md-8">
            @include('core::base.includes.media.media_input', [
                'input' => 'edit_image',
                'data' => $user->image,
            ])
        </div>
    </div>

    <div class="form-row mb-20">
        <div class="col-md-4">
            <label class="font-14 bold black">{{ translate('Name') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" name="name" class="theme-input-style" value="{{ $user->name }}"
                placeholder="{{ translate('Give your name') }}">
        </div>
    </div>

    <div class="form-row mb-20">
        <div class="col-sm-4">
            <label class="font-14 bold black">{{ translate('Email') }}</label>
        </div>
        <div class="col-md-8">
            <input type="email" name="email" class="theme-input-style" value="{{ $user->email }}"
                placeholder="{{ translate('Give your email address') }}">
        </div>
    </div>

    <div class="form-row mb-20">
        <div class="col-md-4">
            <label class="font-14 bold black">{{ translate('Password') }}</label>
        </div>
        <div class="col-md-8">
            <input type="password" name="password" class="theme-input-style"
                placeholder="{{ translate('Give your password') }}">
            @if ($errors->has('password'))
                <div class="invalid-input">{{ $errors->first('password') }}</div>
            @endif
        </div>
    </div>

    <div class="form-row mb-20">
        <div class="col-md-4">
            <label class="font-14 bold black">{{ translate('Confirm Password') }}</label>
        </div>
        <div class="col-md-8">
            <input type="password" name="password_confirmation" class="theme-input-style"
                placeholder="{{ translate('Confirm your password') }}">
        </div>
    </div>

    <div class="form-row mb-20">
        <div class="col-md-4">
            <label class="font-14 bold black">{{ translate('Status') }}</label>
        </div>
        <div class="col-md-8">
            <label class="switch glow primary medium">
                <input type="checkbox" {{ $user->status == 1 ? 'checked' : '' }} name="status">
                <span class="control"></span>
            </label>
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn long">{{ translate('Save Chnages') }}</button>
        </div>
    </div>
</form>
