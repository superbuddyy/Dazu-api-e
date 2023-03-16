<!-- @component('mail.layout')
    @lang('mail.remind_password_content')<br>
    @component('mail::button', ['url' => config('dazu.frontend_url').'/ustaw-haslo?token='])
        @lang('mail.remind_password_btn')
    @endcomponent
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
