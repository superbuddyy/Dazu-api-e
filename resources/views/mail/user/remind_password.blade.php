@component('mail.layout')
    @lang('mail.remind_password_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/ustaw-haslo?token='.{{$verification_token}}])
        @lang('mail.remind_password_btn')
    @endcomponent
@endcomponent
