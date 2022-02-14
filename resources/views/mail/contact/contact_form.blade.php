@component('mail.layout')
    @lang('mail.contact_form_content')
    {{ $name . ' - ' . $email }}
    <br>
    <h3>Temat: {{ $topic }}</h3>
    <p>{{ $message }}</p><br><br>
    @lang('mail.contact_form_footer')
@endcomponent
