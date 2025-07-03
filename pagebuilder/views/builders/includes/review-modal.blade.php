<input type="hidden" name="key" value="{{ $key }}" id="review-key">
<div class="form-row mb-2">
    <div class="col-6">
        <label class="font-14 bold black">{{ translate('Reviewer Image') }}</label>
        <div class="d-block">
            @include('core::base.includes.media.media_input', [
                'input' => 'reviewer_image',
                'data' => isset($details['reviewer_image']['id']) ? $details['reviewer_image']['id'] : null,
            ])
        </div>
        <span
            class="text-danger font-14 reviewer_image_id-feedback d-none">{{ translate('Please choose Reviewer Image.') }}</span>
    </div>
</div>

<div class="form-group mb-2">
    <label for="reviewer_name" class="black">{{ translate('Reviewer Name') }}</label>
    <input type="text" name="reviewer_name" id="reviewer_name" class="form-control"
        placeholder="{{ translate('Reviewer Name') }}" value="{{ isset($details['reviewer_name']) ? $details['reviewer_name'] : '' }}">
    <span class="text-danger font-14 reviewer_name-feedback d-none">{{ translate('Please give reviewer name') }}</span>
</div>
<div class="form-group">
    <label class="black">{{ translate('Comment') }}</label>
    <div class="editor-wrap">
        <textarea name="comment"  id="comment" class="form-control">{{ isset($details['comment']) ? $details['comment'] : '' }}</textarea>
        <span class="text-danger font-14 comment-feedback d-none">{{ translate('Please comment.') }}</span>
    </div>
</div>
<div class="form-group mb-2">
    <label for="rating" class="black">{{ translate('Rating') }}</label>
    <input type="number" max="5" name="rating" id="rating" class="form-control"
        placeholder="{{ translate('Rating') }}" value="{{ isset($details['rating']) ? $details['rating'] : '' }}">
    <span class="text-danger font-14 rating-feedback d-none">{{ translate('Please give a rating.') }}</span>
</div>
