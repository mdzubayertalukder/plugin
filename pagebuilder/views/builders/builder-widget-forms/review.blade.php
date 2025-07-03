<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">

    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        <button class="btn btn-dark sm" id="add_review" type="button">{{ translate('Add Review') }}</button>
        <div class="review-list my-3">
            @isset($widget_properties)
                @foreach ($widget_properties as $key => $review)
                    <button class="btn btn-border sm btn-block radius-0 review-item" data-key="{{ $key }}"
                        data-details="{{ json_encode($review) }}" type="button">{{ $review['reviewer_name'] }}</button>
                @endforeach
            @endisset

        </div>
    </div>

</div>

<!--Create Modal-->
<div id="review-create-modal" class="review-create-modal modal fade show" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Review Update') }}</h4>
                    <button type="button" class="btn-dark" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer justify-content-around">
                <button type="button" class="btn long mt-2" id="save-btn">{{ translate('Save') }}</button>
                <button type="button" class="btn btn-danger long mt-2"
                    id="review-delete-btn">{{ translate('Delete') }}</button>
            </div>
        </div>
    </div>
</div>
<!--Create Modal-->

<script>
    (function($) {
        'use strict'

        /**
         * Add New Review
         **/
        $('#add_review').on('click', function(e) {
            e.preventDefault();
            let review_count = $('.review-list').children().length;
            let review = review_count != 0 ? $('.review-list button:last-child').data('key') : review_count;
            $('.review-list').append(`
                <button class="btn btn-border sm btn-block radius-0 review-item" data-key="${review+1}" data-details="" type="button">New Review</button>
            `);
        });

        /**
         * Review Modal Open
         **/
        $(document).on('click', '.review-item', function() {
            let key = $(this).data('key');
            let details = $(this).data('details');

            $.ajax({
                type: "get",
                url: '{{ route("plugin.builder.pageSection.review.show") }}',
                data: {
                    key,
                    details
                },
                success: function(response) {
                    $('#review-create-modal .modal-body').html(response.data);
                    $('#review-create-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    let message = "{{ translate('New Review Open Failed') }}";
                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, 'ERROR!!');
                }
            });
        });

        /**
         * Review Save
         **/
        $('#save-btn').on('click', function() {
            const fields = ['reviewer_name', 'comment', 'rating', 'reviewer_image_id'];
            let layout_widget_id = $('#properties-body').find('input[name="layout_has_widget_id"]').val();
            let key = $('#review-key').val();
            let data = checkValidation(fields);

            if (Object.keys(data).length == 4) {
                $.ajax({
                    type: "post",
                    url: '{{ route("plugin.builder.pageSection.review.save") }}',
                    data: {
                        layout_widget_id,
                        key,
                        ...data
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success!!');

                        $('.review-list button[data-key="' + key + '"]').text(response.data
                            .reviewer_name);
                        $('.review-list button[data-key="' + key + '"]').data('details',
                            response.data.details);

                        $('#review-create-modal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        let message = "{{ translate('New Review Saving Failed') }}";
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
         * Delete Review
         **/
        $('#review-delete-btn').on('click', function() {
            let key = $('#review-key').val();
            let layout_widget_id = $('#properties-body').find('input[name="layout_has_widget_id"]').val();

            $.ajax({
                type: "delete",
                url: '{{ route("plugin.builder.pageSection.review.delete") }}',
                data: {
                    key,
                    layout_widget_id
                },
                success: function(response) {
                    $('.review-list button[data-key="' + key + '"]').remove();
                    $('#review-create-modal').modal('hide');
                    toastr.success(response.message, 'Success!!');
                },
                error: function(xhr, status, error) {
                    let message = "{{ translate('Review Deleting Failed') }}";
                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, 'ERROR!!');
                }
            });
        })

    })(jQuery);
</script>
