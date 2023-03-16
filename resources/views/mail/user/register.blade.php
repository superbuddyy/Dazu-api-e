<!-- @component('mail.layout')
    @slot('support_email')
        {{ 'change me' }}
    @endslot
    @slot('support_website')
        {{ 'change me' }}
    @endslot
    @lang('mail.registration_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/dokoncz-rejestracje?token='.$user['verification_token']])
        @lang('mail.confirm_registration')
    @endcomponent
    @lang('mail.end_summary')<br>
@endcomponent -->
<!DOCTYPE html>
 <html lang="en-EN">
  <head>
    <meta charset="utf-8">
  </head>
  <body bgcolor="#11C9FF">
<div class="container">
	<div class="text-align">
        	<h1 style="color:#334488">{{$emailTitle}}</h1>
		<p>Please link to the following link to reset your password.</p><br/>
		<a href={{$link}}>{{$link}}</a>
	</div>
</div>  
</body>
 </html>