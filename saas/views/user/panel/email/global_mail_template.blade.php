@php
    $template = getEmailTemplateOf($template_id, $data, $keywords);
@endphp
{!! xss_clean($template) !!}
