@component('mail.layout')
    @lang('mail.offer_created_content')<br>
    @component('mail::button', ['url' => env('FRONT_URL').'/ogloszenia/'.$offer['slug']])
        @lang('mail.offer_created_my_offer_button')
    @endcomponent
    @lang('mail.offer_created_promo_links')<br>
    @lang('mail.end_summary')<br>
@endcomponent
