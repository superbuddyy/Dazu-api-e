@component('mail.layout')
    @lang('mail.set_password_content')<br>
    @component('mail::button', ['url' => env('FRONT_URL').'/ustaw-haslo?token='.$user['verification_token']])
        @lang('mail.set_password_btn')
    @endcomponent
@endcomponent
