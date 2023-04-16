@component('mail.layout')
    @lang('mail.contact_offer_content')
    <p>{{ $name . ' - ' . $email }}</p>
    <br>
    <p>{{ $message }}</p><br><br>
    @lang('mail.contact_offer_footer')
@endcomponent


<!DOCTYPE html>
 <html lang="en-EN">
  <head>
    <meta charset="utf-8">
  </head>
  <body bgcolor="#11C9FF">
<div class="container">
	<div class="text-align">
        <p>{{ $name . ' - ' . $email }}</p>
        <br/>
        <p>{{ $message }}</p><br/><br/>
	</div>
</div>  
</body>
 </html>