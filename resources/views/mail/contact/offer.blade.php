@component('mail.layout')
    @lang('mail.contact_offer_content')
    {{ $name . ' - ' . $email }}
    <br>
    <p>{{ $message }}</p><br><br>
    @lang('mail.contact_offer_footer')
@endcomponent
