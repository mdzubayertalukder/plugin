<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">

    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        <button class="btn btn-dark sm" id="add_slide" type="button">{{ translate('Add Slide') }}</button>
        <div class="slider-list my-3">
            @isset($widget_properties)
                @foreach ($widget_properties as $key => $slide)
                    <button class="btn btn-border sm btn-block radius-0 slide-item" data-key="{{ $key }}"
                        data-details="{{ json_encode($slide) }}" type="button">{{ $slide['title'] }}</button>
                @endforeach
            @endisset

        </div>
    </div>

</div>

<!--Create Modal-->
<div id="slide-create-modal" class="slide-create-modal modal fade show" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Slide Update') }}</h4>
                    <button type="button" class="btn-dark" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger long mt-2"
                    id="banner-delete-btn">{{ translate('Delete') }}</button>
                <button type="button" class="btn long mt-2" id="save-btn">{{ translate('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!--Create Modal-->

<script>
    (function($) {
        'use strict'

        /**
         * Add New Slide
         **/
        $('#add_slide').on('click', function(e) {
            e.preventDefault();
            let slide_count = $('.slider-list').children().length;
            let slide = slide_count != 0 ? $('.slider-list button:last-child').data('key') : slide_count;
            $('.slider-list').append(`
                <button class="btn btn-border sm btn-block radius-0 slide-item" data-key="${slide+1}" data-details="" type="button">New Slide</button>
            `);
        });

        /**
         * Slide Modal Open
         **/
        $(document).on('click', '.slide-item', function() {
            let key = $(this).data('key');
            let details = $(this).data('details');

            $.ajax({
                type: "get",
                url: '{{ route('plugin.builder.pageSection.banner.show') }}',
                data: {
                    key,
                    details
                },
                success: function(response) {
                    $('#slide-create-modal .modal-body').html(response.data);
                    $('#slide-create-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    let message = "{{ translate('New Banner Slide Open Failed') }}";
                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, 'ERROR!!');
                }
            });
        });

        /**
         * Slide Save
         **/
        $('#save-btn').on('click', function() {
            const fields = ['title', 'url', 'desktop_image_id', 'mobile_image_id'];
            let layout_widget_id = $('#properties-body').find('input[name="layout_has_widget_id"]').val();
            let key = $('#slide-key').val();
            let data = checkValidation(fields);

            if (Object.keys(data).length === 4) {
                $.ajax({
                    type: "post",
                    url: '{{ route('plugin.builder.pageSection.banner.save') }}',
                    data: {
                        layout_widget_id,
                        key,
                        ...data
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success!!');

                        $('.slider-list button[data-key="' + key + '"]').text(response.data
                            .title);
                        $('.slider-list button[data-key="' + key + '"]').data('details',
                            response.data.details);

                        $('#slide-create-modal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        let message = "{{ translate('New Banner Slider Saving Failed') }}";
                        if (xhr.responseJSON) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message, 'ERROR!!');
                    }
                });
            }
        });

        /** 
         * Check Field Validation 
         **/
        function checkValidation(fields) {
            let data = {}
            for (let field of fields) {
                let value = $('#' + field).val().trim();
                if (value == '') {
                    $('.' + field + '-feedback').removeClass('d-none');
                } else {
                    $('.' + field + '-feedback').addClass('d-none');
                    data[field] = value;
                }
            }
            return data;
        }

        /**
         * Delete Slide
         **/
        $('#banner-delete-btn').on('click', function() {
            let key = $('#slide-key').val();
            let layout_widget_id = $('#properties-body').find('input[name="layout_has_widget_id"]').val();

            $.ajax({
                type: "delete",
                url: '{{ route('plugin.builder.pageSection.banner.delete') }}',
                data: {
                    key,
                    layout_widget_id
                },
                success: function(response) {
                    $('.slider-list button[data-key="' + key + '"]').remove();
                    $('#slide-create-modal').modal('hide');
                    toastr.success(response.message, 'Success!!');
                },
                error: function(xhr, status, error) {
                    let message = "{{ translate('Banner Slide Deleting Failed') }}";
                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, 'ERROR!!');
                }
            });
        })

    })(jQuery);
</script>
