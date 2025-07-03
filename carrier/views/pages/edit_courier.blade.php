<form id="edit-courier-form">
    <div class="form-row mb-20">
        <label class="font-14 bold black">{{ translate('Name') }} </label>
        <input type="hidden" name="id" value="{{ $courier_info['id'] }}">
        <input type="text" name="name" placeholder="{{ translate('Type Name') }}" value="{{ $courier_info['name'] }}"
            class="theme-input-style">

    </div>
    <div class="form-row mb-20">
        <label class="font-14 bold black">{{ translate('Tracking url') }} </label>
        <input type="text" name="tracking_url" placeholder="{{ translate('Type url') }}"
            value="{{ $courier_info['tracking_url'] }}" class="theme-input-style">

    </div>
    <div class="form-row mb-20">
        <label class="font-14 bold black col-12">{{ translate('Logo') }} </label>
        @include('core::base.includes.media.media_input', [
            'input' => 'edit_logo',
            'data' => $courier_info['logo'],
        ])

    </div>
    <div class="form-row">
        <div class="col-12 text-right">
            <button type="submit" class="btn long courier-update-btn">{{ translate('Save Changes') }}</button>
        </div>
    </div>
</form>
<script>
    /**
     * Update courier information
     * 
     **/
    $('.courier-update-btn').on('click', function(e) {
        e.preventDefault();
        $(document).find(".invalid-input").remove();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            type: "POST",
            data: $('#edit-courier-form').serialize(),
            url: '{{ route('plugin.carrier.shipping.courier.update') }}',
            success: function(response) {
                location.reload();
            },
            error: function(response) {
                $.each(response.responseJSON.errors, function(field_name, error) {
                    $(document).find('[name=' + field_name + ']').after(
                        '<div class="invalid-input">' + error + '</div>')
                })
            }
        });
    });
</script>
