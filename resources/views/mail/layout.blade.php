@component('mail::layout')
    @slot('header')
        <div style="font-size: 40px;color:#ff19b7;font-weight: bold;text-align: center;margin: 10px">
            Dazu
        </div>
    @endslot
    @isset($slot)
        {{ $slot }}
    @endisset
    @slot('footer')
        @component('mail::footer')
            <table style="width:528px">
                <tbody>
                <tr>
                    <td colspan="2" class="legal-note">
                        <hr>
                        Dazu.pl
                    </td>
                </tr>
                </tbody>
            </table>
        @endcomponent
    @endslot
@endcomponent
