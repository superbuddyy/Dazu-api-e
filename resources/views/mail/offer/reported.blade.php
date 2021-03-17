@component('mail.layout')
    @lang('mail.offer_reported_content')<br>
    @component('mail::button', ['url' => env('FRONT_URL').'/ogloszenia/'.$offer['slug']])
        @lang('mail.offer_reported_button')
    @endcomponent
    @lang('mail.offer_reported_description')
    <i>{{ $message }}</i>
@endcomponent
