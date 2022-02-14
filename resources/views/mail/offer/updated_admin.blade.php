@component('mail.layout')
    @lang('mail.offer_updated_admin_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/ogloszenia/'.$offer['slug']])
        @lang('mail.offer_updated_admin_button')
    @endcomponent
@endcomponent
