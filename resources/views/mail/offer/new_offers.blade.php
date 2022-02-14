@component('mail.layout')
    @lang('mail.new_offers_content')<br>
    @foreach($offers as $offer)
        <div style="">
            <img src="{{ $offer->main_photo->file->url ?? null}}" alt="">
            @component('mail::button', ['url' => config('dazu.frontend_url').'/ogloszenia/'.$offer['slug']])
                @lang('mail.new_offers_button')
            @endcomponent
        </div>
    @endforeach
    @lang('mail.new_offers_content')<br>
    @lang('mail.end_summary')<br>
@endcomponent
