@component('mail.layout')
    @lang('mail.new_invoice_content') {{ $transaction['description']  }}<br><br>
    @lang('mail.offer_created_promo_links')<br>
    @lang('mail.end_summary')<br>
@endcomponent
