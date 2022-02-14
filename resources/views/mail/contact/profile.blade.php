@component('mail.layout')
    @lang('mail.contact_form_content')
      {{ $name . ' - ' . $email }}
    <br>
    <p>{{ $message }}</p><br><br>
    @lang('mail.contact_form_footer')
@endcomponent
