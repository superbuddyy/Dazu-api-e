@component('mail.layout')
    @if ($variant === 'visible_in_future')
        @lang('mail.offer_created_visible_in_future_content')<br>
    @else
        @lang('mail.offer_created_content')<br>
    @endif
    @component('mail::button', ['url' => config('dazu.frontend_url').'/ogloszenia/'.$offer['slug']])
        @lang('mail.offer_created_my_offer_button')
    @endcomponent
    @lang('mail.offer_created_promo_links')<br>
    @lang('mail.end_summary')<br>
@endcomponent
