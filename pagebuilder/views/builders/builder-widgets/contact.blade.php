@php
    $active_theme = getActiveTheme();
    $contact_option = getThemeOption('contact', $active_theme->id);
    $title = front_translate('Get In Touch');
    $subtitle = front_translate('Whether you have a question, want to start a project or simply want to connect. Feel free to send me a message in the contact form');
    $name_placeholder = front_translate('Your Name');
    $email_placeholder = front_translate('Your Email');
    $subject_placeholder = front_translate('Subject');
    $message_placeholder = front_translate('Your Message');
    $btn_text = front_translate('Submit');
    
    //contact page field
    if (isset($contact_option) && $contact_option['custom_contact_style'] == 1) {
        $title = isset($contact_option['contact_title']) ? front_translate($contact_option['contact_title']) : '';
        $subtitle = isset($contact_option['contact_subtitle']) ? front_translate($contact_option['contact_subtitle']) : '';
        $name_placeholder = isset($contact_option['contact_name_placeholder']) ? front_translate($contact_option['contact_name_placeholder']) : '';
        $email_placeholder = isset($contact_option['contact_email_placeholder']) ? front_translate($contact_option['contact_email_placeholder']) : '';
        $subject_placeholder = isset($contact_option['contact_subject_placeholder']) ? front_translate($contact_option['contact_subject_placeholder']) : '';
        $message_placeholder = isset($contact_option['contact_message_placeholder']) ? front_translate($contact_option['contact_message_placeholder']) : '';
        $btn_text = isset($contact_option['contact_button_text']) ? front_translate($contact_option['contact_button_text']) : '';
    }
@endphp
<div class="biz-contact-form">
    <!-- Contact Form Title -->
    <div class="title text-center">
        <h2>{{ $title }}</h2>
        <p>{{ $subtitle }}</p>
    </div>
    <!-- End of Contact Form Title -->

    <div class="my-contact-form-cover">
        <form class="my-contact-form" action="{{ route('theme.default.send.message') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="{{ $name_placeholder }}"
                        required>
                    @if ($errors->has('name'))
                        <p class="text-danger">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <input type="email" name="email" class="form-control"
                        placeholder="{{ $email_placeholder }}" required>
                    @if ($errors->has('email'))
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="col-md-12">
                    <input type="text" name="subject" class="form-control"
                        placeholder="{{ $subject_placeholder }}" required>
                    @if ($errors->has('subject'))
                        <p class="text-danger">{{ $errors->first('subject') }}</p>
                    @endif
                </div>
                <div class="col-md-12">
                    <textarea name="message" class="form-control" placeholder="{{ $message_placeholder }}" required></textarea>
                    @if ($errors->has('message'))
                        <p class="text-danger">{{ $errors->first('message') }}</p>
                    @endif
                </div>
                <div class="col-md-12 text-{{ isset($data['btn_alignment']) ? $data['btn_alignment'] : 'center' }}">
                    <button type="submit" class="btn btn-primary">{{ $btn_text }}</button>
                </div>
            </div>
        </form>
    </div>
</div>