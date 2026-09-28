<x-filament-panels::page>
    <style>
        .ac-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:1rem}
        .ac-card{border:1px solid #94a3b844;padding:1.25rem;border-radius:1rem;margin-bottom:1rem}
        .ac-field{display:grid;gap:.35rem;font-size:.85rem}
        .ac-field input,.ac-field select{width:100%;padding:.62rem .7rem;border:1px solid #94a3b866;border-radius:.55rem;background:transparent;color:inherit}
        .ac-field select option{color:#111827}
        .ac-note{font-size:.84rem;line-height:1.6;color:#94a3b8}
        .ac-table{width:100%;border-collapse:collapse}
        .ac-table th,.ac-table td{padding:.7rem;border-bottom:1px solid #94a3b833;text-align:left;vertical-align:top}
        .ac-badge{display:inline-block;padding:.22rem .55rem;border-radius:999px;border:1px solid #94a3b855;font-size:.76rem}
    </style>

    <p class="ac-note mb-4">
        ეს არის მხოლოდ ადმინისტრატორის სწრაფი საინჟინრო დამხმარე. კონფიგურატორი ამოწმებს მთავარ თავსებადობის წერტილებს:
        reader interface, RFID credential, relay/lock logic, კვება, IP/2-wire ეკოსისტემა და საჭირო რაოდენობები.
        კონკრეტული მოდელის შეძენამდე ყოველთვის გადაამოწმეთ მწარმოებლის datasheet.
    </p>

    <section class="ac-card">
        <div class="ac-grid">
            <label class="ac-field">
                <span>სისტემის ტიპი</span>
                <select wire:model.live="config.system">
                    <option value="access">RFID / დაშვების კონტროლი</option>
                    <option value="intercom">ვიდეოდომოფონი</option>
                </select>
            </label>
        </div>
    </section>

    @if (($config['system'] ?? 'access') === 'access')
        <section class="ac-card">
            <h2 class="text-lg font-bold mb-4">RFID / დაშვების სისტემა</h2>
            <div class="ac-grid">
                <label class="ac-field"><span>კარების რაოდენობა</span><input type="number" min="1" max="16" wire:model.live.debounce.300ms="config.doors"></label>
                <label class="ac-field"><span>Reader-ის მხარეები</span><select wire:model.live="config.reader_sides"><option value="entry">შესვლა Reader-ით / გამოსვლა ღილაკით</option><option value="entry_exit">შესვლა და გამოსვლა Reader-ით</option></select></label>
                <label class="ac-field"><span>Reader interface</span><select wire:model.live="config.reader_interface"><option value="wiegand">Wiegand</option><option value="osdp">OSDP / RS-485</option></select></label>
                <label class="ac-field"><span>RFID ბარათი</span><select wire:model.live="config.credential"><option value="mifare">MIFARE 13.56 MHz</option><option value="em">EM 125 kHz</option></select></label>
                <label class="ac-field"><span>საკეტის ტიპი</span><select wire:model.live="config.lock_type"><option value="maglock">Maglock</option><option value="strike">Electric strike</option><option value="bolt">Electric bolt</option></select></label>
                <label class="ac-field"><span>ერთი საკეტის მოხმარება (A @12V)</span><input type="number" min=".1" max="5" step=".1" wire:model.live.debounce.300ms="config.lock_current_a"></label>
                <label class="ac-field"><span>ერთი Reader-ის მოხმარება (A)</span><input type="number" min=".05" max="2" step=".01" wire:model.live.debounce.300ms="config.reader_current_a"></label>
                <label class="ac-field"><span>კონტროლერის მოხმარება (A)</span><input type="number" min=".1" max="5" step=".1" wire:model.live.debounce.300ms="config.controller_current_a"></label>
                <label class="ac-field"><span>კვების რეზერვი (%)</span><input type="number" min="0" max="100" step="5" wire:model.live.debounce.300ms="config.reserve_percent"></label>
            </div>
        </section>
    @else
        <section class="ac-card">
            <h2 class="text-lg font-bold mb-4">ვიდეოდომოფონის სისტემა</h2>
            <div class="ac-grid">
                <label class="ac-field"><span>ტოპოლოგია</span><select wire:model.live="config.intercom_type"><option value="ip">IP / Ethernet / PoE</option><option value="2wire">2-wire</option></select></label>
                <label class="ac-field"><span>ბინების / აბონენტების რაოდენობა</span><input type="number" min="1" max="200" wire:model.live.debounce.300ms="config.apartments"></label>
                <label class="ac-field"><span>მონიტორი ერთ ბინაზე</span><input type="number" min="1" max="4" wire:model.live.debounce.300ms="config.monitors_per_apartment"></label>
                <label class="ac-field"><span>საკეტის ტიპი</span><select wire:model.live="config.lock_type"><option value="maglock">Maglock</option><option value="strike">Electric strike</option><option value="bolt">Electric bolt</option></select></label>
                <label class="ac-field"><span>გარე პანელის RFID</span><select wire:model.live="config.credential"><option value="mifare">MIFARE 13.56 MHz</option><option value="em">EM 125 kHz</option></select></label>
            </div>
        </section>
    @endif

    @php($r = $this->result())

    <section class="ac-card" aria-live="polite">
        <h2 class="text-xl font-bold mb-2">{{ $r['summary'] }}</h2>
        @if ($r['electrical'])
            <p class="ac-note mb-4">
                Readers: <strong>{{ $r['electrical']['readers'] }}</strong> ·
                დათვლილი 12V დატვირთვა: <strong>{{ $r['electrical']['estimated_load_a'] }}A</strong> ·
                რეკომენდებული PSU: <strong>მინ. {{ $r['electrical']['recommended_psu_a'] }}A</strong>
            </p>
        @endif

        <div class="overflow-x-auto">
            <table class="ac-table">
                <thead><tr><th>ჯგუფი</th><th>რაოდ.</th><th>რა შეარჩიო</th><th>რატომ</th></tr></thead>
                <tbody>
                @foreach ($r['items'] as $item)
                    <tr>
                        <td><span class="ac-badge">{{ $item['group'] }}</span></td>
                        <td>{{ $item['qty'] }}</td>
                        <td><strong>{{ $item['item'] }}</strong></td>
                        <td class="ac-note">{{ $item['why'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="ac-card">
        <h3 class="text-lg font-bold mb-3">თავსებადობის შემოწმება</h3>
        <ul class="list-disc ms-5 ac-note">
            @foreach ($r['checks'] as $check)
                <li>{{ $check }}</li>
            @endforeach
        </ul>
    </section>

    @if ($r['warnings'])
        <section class="ac-card">
            <h3 class="text-lg font-bold mb-3">გაფრთხილებები</h3>
            <ul class="list-disc ms-5 ac-note">
                @foreach ($r['warnings'] as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="ac-card">
        <h3 class="text-lg font-bold mb-3">სწრაფი წესები — როგორ შეუწყვილო ერთმანეთს</h3>
        <div class="ac-grid ac-note">
            <div><strong>1. Reader ↔ Controller</strong><br>ინტერფეისი უნდა დაემთხვეს: Wiegand↔Wiegand ან OSDP↔OSDP.</div>
            <div><strong>2. ბარათი ↔ Reader</strong><br>სიხშირე/ტექნოლოგია უნდა დაემთხვეს: EM 125 kHz ან MIFARE 13.56 MHz.</div>
            <div><strong>3. Controller ↔ Lock</strong><br>relay output, NO/NC ლოგიკა და contact rating უნდა შეესაბამებოდეს საკეტის სქემას.</div>
            <div><strong>4. PSU ↔ მოწყობილობები</strong><br>ძაბვა უნდა დაემთხვეს და საერთო დენს დაუმატეთ მინიმუმ 20–30% რეზერვი.</div>
            <div><strong>5. IP intercom</strong><br>Outdoor station + indoor monitor + software/PoE უნდა იყოს თავსებადი ერთ ecosystem-ში.</div>
            <div><strong>6. 2-wire intercom</strong><br>კაბელის 2 ძარღვი არ ნიშნავს უნივერსალურ თავსებადობას — distributor/power module და მონიტორები კონკრეტულ ოჯახს უნდა ეკუთვნოდეს.</div>
        </div>
    </section>
</x-filament-panels::page>
