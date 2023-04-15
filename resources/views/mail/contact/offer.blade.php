@component('mail.layout')
    @lang('mail.contact_offer_content')
    <p>{{ $name }} - {{ $email }}</p>
    <br>
    <p>{{ $message }}</p><br><br>
    @lang('mail.contact_offer_footer')
@endcomponent
