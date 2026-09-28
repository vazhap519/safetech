<x-filament-panels::page>
    <style>
        .bar-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}.bar-card{border:1px solid #94a3b844;padding:1.25rem;border-radius:1rem;margin-bottom:1rem}.bar-field{display:grid;gap:.35rem;font-size:.85rem}.bar-field input,.bar-field select{width:100%;padding:.62rem .7rem;border:1px solid #94a3b866;border-radius:.55rem;background:transparent;color:inherit}.bar-field select option{color:#111827}.bar-note{font-size:.84rem;line-height:1.6;color:#94a3b8}.bar-table{width:100%;border-collapse:collapse}.bar-table th,.bar-table td{padding:.7rem;border-bottom:1px solid #94a3b833;text-align:left;vertical-align:top}.bar-ok{border:1px solid #22c55e66;background:#22c55e12;padding:.8rem 1rem;border-radius:.7rem}.bar-fix{border:1px solid #f59e0b66;background:#f59e0b12;padding:.8rem 1rem;border-radius:.7rem}
    </style>

    <p class="bar-note mb-4">აირჩიეთ ზოლების რაოდენობა, boom length და მართვის ტიპი. თუ არჩეული შლაგბაუმი, LPR კამერა ან UHF reader შეუსაბამოა, სისტემა ავტომატურად ჩაანაცვლებს თავსებადი ვარიანტით.</p>

    @if($lastCorrection)<div class="bar-fix mb-4"><strong>ავტომატური კორექტირება:</strong> {{ $lastCorrection }}</div>@endif

    <section class="bar-card">
        <h2 class="text-lg font-bold mb-4">ძირითადი პარამეტრები</h2>
        <div class="bar-grid">
            <label class="bar-field"><span>ზოლების რაოდენობა</span><input type="number" min="1" max="8" wire:model.live.debounce.300ms="config.lanes"></label>
            <label class="bar-field"><span>Boom type</span><select wire:model.live="config.boom_type"><option value="straight">Straight boom</option></select></label>
            <label class="bar-field"><span>Boom length</span><select wire:model.live="config.boom_length"><option value="3">3 m</option><option value="4">4 m</option><option value="4.5">4.5 m</option><option value="6">6 m</option></select></label>
            <label class="bar-field"><span>შლაგბაუმი</span><select wire:model.live="config.barrier_id">@foreach($this->barrierOptions() as $id=>$label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
            <label class="bar-field"><span>გახსნის ლოგიკა</span><select wire:model.live="config.access_mode"><option value="lpr">LPR / ANPR ნომრით</option><option value="uhf">UHF Tag</option><option value="remote">Remote / Button / Relay</option></select></label>
        </div>
    </section>

    @if(($config['access_mode'] ?? 'lpr') === 'lpr')
        <section class="bar-card">
            <h2 class="text-lg font-bold mb-4">LPR / ANPR</h2>
            <div class="bar-grid">
                <label class="bar-field"><span>LPR კამერა</span><select wire:model.live="config.lpr_camera_id">@foreach($this->lprOptions() as $id=>$label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="bar-field"><span>Trigger</span><select wire:model.live="config.vehicle_trigger"><option value="video">Video</option><option value="loop">Loop</option><option value="radar">Radar</option></select></label>
            </div>
        </section>
    @elseif(($config['access_mode'] ?? '') === 'uhf')
        <section class="bar-card">
            <h2 class="text-lg font-bold mb-4">UHF ავტორიზაცია</h2>
            <div class="bar-grid">
                <label class="bar-field"><span>UHF Reader</span><select wire:model.live="config.uhf_reader_id">@foreach($this->uhfOptions() as $id=>$label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></label>
                <label class="bar-field"><span>Controller interface</span><select wire:model.live="config.controller_interface"><option value="wiegand">Wiegand</option><option value="rs485">RS-485</option></select></label>
                <label class="bar-field"><span>საწყისი Tag-ების რაოდენობა</span><input type="number" min="1" max="5000" wire:model.live.debounce.300ms="config.tag_count"></label>
            </div>
        </section>
    @endif

    @php($r=$this->result())
    <div class="bar-ok mb-4"><strong>✓ თავსებადი კომპლექტაცია</strong> — {{ $r['summary'] }}</div>

    <section class="bar-card">
        <h2 class="text-xl font-bold mb-4">არჩეული მოწყობილობები</h2>
        <div class="bar-grid bar-note">@foreach($r['selection'] as $label=>$value)<div><strong>{{ str_replace('_',' ',ucfirst($label)) }}</strong><br>{{ $value }}</div>@endforeach</div>
    </section>

    <section class="bar-card">
        <h2 class="text-xl font-bold mb-4">სრული კომპლექტაცია</h2>
        <div class="overflow-x-auto"><table class="bar-table"><thead><tr><th>ჯგუფი</th><th>რაოდ.</th><th>მოწყობილობა</th><th>შენიშვნა</th></tr></thead><tbody>
            @foreach($r['items'] as $item)<tr><td>{{ $item['group'] }}</td><td>{{ $item['qty'] }}</td><td><strong>{{ $item['item'] }}</strong></td><td class="bar-note">{{ $item['why'] }}</td></tr>@endforeach
        </tbody></table></div>
    </section>

    <section class="bar-card"><h3 class="text-lg font-bold mb-3">თავსებადობის შემოწმება</h3><ul class="list-disc ms-5 bar-note">@foreach($r['checks'] as $check)<li>{{ $check }}</li>@endforeach</ul></section>
    @if($r['warnings'])<section class="bar-card"><h3 class="text-lg font-bold mb-3">გაფრთხილებები</h3><ul class="list-disc ms-5 bar-note">@foreach($r['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></section>@endif
</x-filament-panels::page>
