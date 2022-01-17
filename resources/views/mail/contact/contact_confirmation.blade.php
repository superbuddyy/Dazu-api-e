@component('mail.layout')
    @slot('support_email')
        {{ 'change me' }}
    @endslot
    @slot('support_website')
        {{ 'change me' }}
    @endslot
    @lang('mail.registration_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/?token='.$user['verification_token']])
        @lang('mail.confirm_registration')
    @endcomponent
    @lang('mail.end_summary')<br>
@endcomponent
