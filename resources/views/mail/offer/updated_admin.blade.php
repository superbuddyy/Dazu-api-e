@component('mail.layout')
    @lang('mail.offer_updated_admin_content')<br>
    @component('mail::button', ['url' => env('FRONT_URL').'/ogloszenia/'.$offer['slug']])
        @lang('mail.offer_updated_admin_button')
    @endcomponent
@endcomponent
