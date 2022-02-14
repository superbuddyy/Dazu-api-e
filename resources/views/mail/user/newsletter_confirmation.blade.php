@component('mail.layout')
    @lang('mail.newsletter_confirmation_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/potwierdz-newsletter?token='.$token])
        @lang('mail.confirm_registration')
    @endcomponent<br>
    @lang('mail.end_summary')<br>
@endcomponent
