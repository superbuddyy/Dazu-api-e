@component('mail.layout')
    @lang('mail.paid_offer_created_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/moje-ogloszenia/oplac/'.$offer['slug']])
        @lang('mail.paid_offer_created_button')
    @endcomponent
    @lang('mail.end_summary')<br>
@endcomponent
