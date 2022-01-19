@component('mail.layout')
    @slot('support_email')
        {{ 'change me' }}
    @endslot
    @slot('support_website')
        {{ 'change me' }}
    @endslot
    @lang('mail.contact_confirmation_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').$url.'?token='.$user['verification_token']])
        @lang('mail.contact_confirm_registration')
    @endcomponent
    @lang('mail.end_summary')<br>
@endcomponent
