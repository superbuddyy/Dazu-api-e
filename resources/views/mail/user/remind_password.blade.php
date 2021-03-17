@component('mail.layout')
    @lang('mail.remind_password_content')<br>
    @component('mail::button', ['url' => env('FRONT_URL').'/ustaw-haslo?token='.$user['verification_token']])
        @lang('mail.remind_password_btn')
    @endcomponent
@endcomponent
