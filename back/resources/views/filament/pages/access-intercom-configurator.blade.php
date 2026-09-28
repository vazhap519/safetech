<x-filament-panels::page>
    <style>
        .ac-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}.ac-card{border:1px solid #94a3b844;padding:1.25rem;border-radius:1rem;margin-bottom:1rem}.ac-field{display:grid;gap:.35rem;font-size:.85rem}.ac-field input,.ac-field select{width:100%;padding:.62rem .7rem;border:1px solid #94a3b866;border-radius:.55rem;background:transparent;color:inherit}.ac-field select option{color:#111827}.ac-note{font-size:.84rem;line-height:1.6;color:#94a3b8}.ac-table{width:100%;border-collapse:collapse}.ac-table th,.ac-table td{padding:.7rem;border-bottom:1px solid #94a3b833;text-align:left;vertical-align:top}.ac-ok{border:1px solid #22c55e66;background:#22c55e12;padding:.8rem 1rem;border-radius:.7rem}.ac-fix{border:1px solid #f59e0b66;background:#f59e0b12;padding:.8rem 1rem;border-radius:.7rem}
    </style>

    <p class="ac-note mb-4">აირჩიეთ კომპონენტი. თუ არჩეული მოდელი სხვა კომპონენტთან ან ტოპოლოგიასთან შეუთავსებელია, სისტემა ავტომატურად ჩაანაცვლებს თავსებადი მოდელით და ქვემოთ გაჩვენებთ საბოლოო კომპლექტაციას.</p>

    @if ($lastCorrection)
        <div class="ac-fix mb-4"><strong>ავტომატური კორექტირება:</strong> {{ $lastCorrection }}</div>
    @endif

    <section class="ac-card">
        <div class="ac-grid">
            <label class="ac-field"><span>სისტემის ტიპი</span><select wire:model.live="config.system"><option value="access">RFID / დაშვების კონტროლი</option><option value="intercom">ვიდეოდომოფონი</option></select></label>
        </div>
    </section>

    @if (($config['system'] ?? 'access') === 'access')
        <section class="ac-card">
            <h2 class="text-lg font-bold mb-4">RFID / Access Control — რეალური მოწყობილობების შერჩევა</h2>
            <div class="ac-grid">
                <label class="ac-field"><span>კარების რაოდენობა</span><input type="number" min="1" max="16" wire:model.live.debounce.300ms="config.doors"></label>
                <label class="ac-field"><span>Reader-ის მხარეები</span><select wire:model.live="config.reader_sides"><option value="entry">Entry reader / Exit button</option><option value="entry_exit">Entry + Exit reader</option></select></label>
                <label class="ac-field"><span>Reader interface</span><select wire:model.live="config.reader_interface"><option value="wiegand">Wiegand</option><option value="osdp">OSDP</option><option value="rs485">RS-485</option></select></label>
                <label class="ac-field"><span>RFID ტექნოლოგია</span><select wire:model.live="config.credential"><option value="mifare">MIFARE 13.56 MHz</option><option value="em">EM 125 kHz</option></select></label>
                <label class="ac-field"><span>კონტროლერი</span><select wire:model.live="config.controller_id">@foreach($this->controllerOptions() as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="ac-field"><span>Reader</span><select wire:model.live="config.reader_id">@foreach($this->readerOptions() as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="ac-field"><span>საკეტის ტიპი</span><select wire:model.live="config.lock_type"><option value="maglock">Maglock</option><option value="strike">Electric strike</option><option value="bolt">Electric bolt</option></select></label>
                <label class="ac-field"><span>ერთი საკეტის მოხმარება (A @12V)</span><input type="number" min=".1" max="5" step=".1" wire:model.live.debounce.300ms="config.lock_current_a"></label>
                <label class="ac-field"><span>ერთი Reader-ის მოხმარება (A)</span><input type="number" min=".05" max="2" step=".01" wire:model.live.debounce.300ms="config.reader_current_a"></label>
                <label class="ac-field"><span>კონტროლერის მოხმარება (A)</span><input type="number" min=".1" max="5" step=".1" wire:model.live.debounce.300ms="config.controller_current_a"></label>
                <label class="ac-field"><span>კვების რეზერვი (%)</span><input type="number" min="0" max="100" step="5" wire:model.live.debounce.300ms="config.reserve_percent"></label>
            </div>
        </section>
    @else
        <section class="ac-card">
            <h2 class="text-lg font-bold mb-4">IP ვიდეოდომოფონი — თავსებადი მოწყობილობების შერჩევა</h2>
            <div class="ac-grid">
                <label class="ac-field"><span>ბინების / აბონენტების რაოდენობა</span><input type="number" min="1" max="200" wire:model.live.debounce.300ms="config.apartments"></label>
                <label class="ac-field"><span>მონიტორი ერთ ბინაზე</span><input type="number" min="1" max="4" wire:model.live.debounce.300ms="config.monitors_per_apartment"></label>
                <label class="ac-field"><span>გარე პანელი</span><select wire:model.live="config.door_station_id">@foreach($this->doorStationOptions() as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="ac-field"><span>შიდა მონიტორი</span><select wire:model.live="config.indoor_station_id">@foreach($this->indoorStationOptions() as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="ac-field"><span>PoE switch</span><select wire:model.live="config.switch_id">@foreach($this->switchOptions() as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="ac-field"><span>საკეტი</span><select wire:model.live="config.lock_type"><option value="maglock">Maglock</option><option value="strike">Electric strike</option><option value="bolt">Electric bolt</option></select></label>
            </div>
        </section>
    @endif

    @php($r = $this->result())
    <div class="ac-ok mb-4"><strong>✓ თავსებადი კომპლექტაცია</strong> — {{ $r['summary'] }}</div>

    <section class="ac-card">
        <h2 class="text-xl font-bold mb-4">არჩეული თავსებადი მოწყობილობები</h2>
        <div class="ac-grid ac-note">
            @foreach($r['selection'] as $label => $value)
                <div><strong>{{ str_replace('_', ' ', ucfirst($label)) }}</strong><br>{{ $value }}</div>
            @endforeach
        </div>
    </section>

    <section class="ac-card">
        <h2 class="text-xl font-bold mb-4">სრული კომპლექტაცია</h2>
        <div class="overflow-x-auto"><table class="ac-table"><thead><tr><th>ჯგუფი</th><th>რაოდ.</th><th>მოწყობილობა</th><th>შენიშვნა</th></tr></thead><tbody>
        @foreach($r['items'] as $item)<tr><td>{{ $item['group'] }}</td><td>{{ $item['qty'] }}</td><td><strong>{{ $item['item'] }}</strong></td><td class="ac-note">{{ $item['why'] }}</td></tr>@endforeach
        </tbody></table></div>
    </section>

    @if($r['electrical'])
        <section class="ac-card"><h3 class="text-lg font-bold mb-3">კვების დათვლა</h3><p class="ac-note">კარები: {{ $r['electrical']['doors'] }} · Reader: {{ $r['electrical']['readers'] }} · დატვირთვა ≈ {{ $r['electrical']['estimated_load_a'] }}A · რეკომენდებული PSU მინ. {{ $r['electrical']['recommended_psu_a'] }}A</p></section>
    @endif

    <section class="ac-card"><h3 class="text-lg font-bold mb-3">თავსებადობის შემოწმება</h3><ul class="list-disc ms-5 ac-note">@foreach($r['checks'] as $check)<li>{{ $check }}</li>@endforeach</ul></section>
    @if($r['warnings'])<section class="ac-card"><h3 class="text-lg font-bold mb-3">გაფრთხილებები</h3><ul class="list-disc ms-5 ac-note">@foreach($r['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></section>@endif
</x-filament-panels::page>
