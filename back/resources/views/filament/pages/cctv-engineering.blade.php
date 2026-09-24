<x-filament-panels::page>
    <style>
        .cctv-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem}
        .cctv-field{display:grid;gap:.35rem;font-size:.85rem}
        .cctv-field input,.cctv-field select{width:100%;padding:.62rem .7rem;border:1px solid #94a3b866;border-radius:.55rem;background:transparent;color:inherit}
        .cctv-field select option{color:#111827}
        .cctv-card{border:1px solid #94a3b844;padding:1.25rem;border-radius:1rem;margin-bottom:1rem}
        .cctv-metric{border-radius:.75rem;border:1px solid #94a3b844;padding:1rem}
        .cctv-metric strong{display:block;font-size:1.35rem;margin-top:.4rem;color:#f59e0b}
        .cctv-button{border:1px solid #f59e0b;border-radius:.6rem;padding:.6rem 1rem;color:#f59e0b}
        .cctv-note{font-size:.85rem;line-height:1.6;color:#94a3b8}
    </style>
    <p class="cctv-note">დაამატეთ კამერების ჯგუფები განსხვავებული მეგაპიქსელით, ბიტრეიტით, კოდეკით ან სიმძლავრით. კამერის გარჩევადობა აირჩიეთ 2 MP-იანი ნაბიჯით: 2, 4, 6, 8 და ასე შემდეგ. ყველა გამოთვლა სრულდება სერვერზე. MP/FPS-ით გამოთვლილი ბიტრეიტი მხოლოდ მიახლოებითია — ზუსტი შედეგისთვის მიუთითეთ რეალურად დაყენებული Mbps.</p>
    <section class="cctv-card">
        <h2 class="text-lg font-bold mb-4">ჩაწერა და დისკის პირობები</h2>
        <div class="cctv-grid">
            @foreach ([
                'days' => ['ჩაწერის დღეები', 1, 365, 1],
                'reserve_percent' => ['საცავის რეზერვი (%)', 0, 100, 1],
                'disk_usable_percent' => ['დისკის გამოსაყენებელი წილი (%)', 1, 100, 1],
            ] as $key => $field)
                <label class="cctv-field"><span>{{ $field[0] }}</span><input type="number" min="{{ $field[1] }}" max="{{ $field[2] }}" step="{{ $field[3] }}" wire:model.live.debounce.500ms="settings.{{ $key }}"></label>
            @endforeach
        </div>
    </section>
    @foreach ($groups as $i => $group)
        <section wire:key="camera-group-{{ $i }}" class="cctv-card">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg font-bold">კამერის ჯგუფი №{{ $i + 1 }}</h2>
                <button type="button" class="cctv-button" wire:click="removeGroup({{ $i }})" @disabled(count($groups) === 1)>წაშლა</button>
            </div>
            <div class="cctv-grid">
                <label class="cctv-field">
                    <span>კამერის გარჩევადობა (MP)</span>
                    <select wire:model.live="groups.{{ $i }}.megapixels">
                        @foreach (range(2, 64, 2) as $megapixels)
                            <option value="{{ $megapixels }}">{{ $megapixels }} MP</option>
                        @endforeach
                    </select>
                </label>
                @foreach ([
                    'count' => ['კამერების რაოდენობა', 0, 256, 1],
                    'fps' => ['კადრი წამში (FPS)', 1, 60, 1],
                    'bitrate_mbps' => ['ვიდეო ბიტრეიტი / კამერა (Mbps; 0 = მიახლოებითი)', 0, 128, 0.1],
                    'audio_mbps' => ['აუდიო / კამერა (Mbps)', 0, 1, 0.01],
                    'camera_watts' => ['ერთი კამერის მაქს. მოხმარება (W)', 0, 120, 0.1],
                    'voltage' => ['კამერის კვების ძაბვა (V DC)', 5, 60, 1],
                ] as $key => $field)
                    <label class="cctv-field"><span>{{ $field[0] }}</span><input type="number" min="{{ $field[1] }}" max="{{ $field[2] }}" step="{{ $field[3] }}" wire:model.live.debounce.500ms="groups.{{ $i }}.{{ $key }}"></label>
                @endforeach
                <label class="cctv-field"><span>შეკუმშვა</span><select wire:model.live="groups.{{ $i }}.codec"><option value="h264">H.264</option><option value="h265">H.265</option><option value="h265plus">H.265+ (მიახლოებითი)</option></select></label>
                <label class="cctv-field"><span>კვება</span><select wire:model.live="groups.{{ $i }}.power_type"><option value="poe">PoE</option><option value="dc">ცალკე DC ადაპტერი</option></select></label>
                <label class="cctv-field"><span>ჩაწერის ტიპი</span><select wire:model.live="groups.{{ $i }}.mode"><option value="continuous">24/7 უწყვეტი</option><option value="schedule">გრაფიკით</option><option value="motion">მოძრაობით</option></select></label>
                @if (($group['mode'] ?? 'continuous') !== 'continuous')
                    <label class="cctv-field"><span>გრაფიკი (სთ/დღე)</span><input type="number" min="0" max="24" step=".5" wire:model.live.debounce.500ms="groups.{{ $i }}.hours_per_day"></label>
                @endif
                @if (($group['mode'] ?? '') === 'motion')
                    <label class="cctv-field"><span>მოძრაობით რეალური ჩაწერა (%)</span><input type="number" min="0" max="100" step="1" wire:model.live.debounce.500ms="groups.{{ $i }}.motion_percent"></label>
                @endif
            </div>
        </section>
    @endforeach
    <button type="button" class="cctv-button mb-6" wire:click="addGroup">+ განსხვავებული კამერის ჯგუფი</button>
    <section class="cctv-card">
        <h2 class="text-lg font-bold mb-4">UPS, ქსელი და ენერგია</h2>
        <div class="cctv-grid">
            @foreach ([
                'nvr_watts' => ['NVR/DVR + HDD (W)', 0, 1000, .1],
                'switch_watts' => ['PoE სვიჩის საკუთარი მოხმარება (W)', 0, 1000, .1],
                'other_watts' => ['როუტერი/მონიტორი/სხვა (W)', 0, 3000, .1],
                'poe_efficiency_percent' => ['PoE ბლოკის ეფექტიანობა (%)', 50, 100, 1],
                'runtime_hours' => ['სასურველი ავტონომია (სთ)', .1, 72, .1],
                'headroom_percent' => ['სიმძლავრის რეზერვი (%)', 0, 100, 1],
                'ups_power_factor' => ['UPS PF (გამომავალი W/VA)', .4, 1, .01],
                'battery_voltage' => ['UPS ბატარეების საერთო DC ძაბვა (V)', 6, 384, 1],
                'battery_dod_percent' => ['გამოსაყენებელი განმუხტვა DoD (%)', 10, 100, 1],
                'inverter_efficiency_percent' => ['UPS ინვერტორის ეფექტიანობა (%)', 30, 100, 1],
            ] as $key => $field)
                <label class="cctv-field"><span>{{ $field[0] }}</span><input type="number" min="{{ $field[1] }}" max="{{ $field[2] }}" step="{{ $field[3] }}" wire:model.live.debounce.500ms="settings.{{ $key }}"></label>
            @endforeach
        </div>
    </section>
    @php($r = $this->engineeringResult())
    <section class="cctv-card" aria-live="polite">
        <h2 class="text-xl font-bold mb-4">საინჟინრო შედეგი</h2>
        <div class="cctv-grid">
            @foreach ([
                'კამერები' => $r['camera_count'],
                'ჩაწერის მოცულობა (raw)' => $r['storage_raw_tb'].' TB',
                'საჭირო სასარგებლო მოცულობა' => $r['storage_required_tb'].' TB',
                'ერთი დისკის შესაძლო ზომა' => $r['suggested_single_disk_tb'] ? $r['suggested_single_disk_tb'].' TB' : 'რამდენიმე HDD / NVR bays',
                'ქსელის პიკური ნაკადი' => $r['video_peak_mbps'].' Mbps',
                'რეზერვით ქსელის ნაკადი' => $r['network_recommended_mbps'].' Mbps',
                'PoE კამერების ჯამი' => $r['poe_load_watts'].' W',
                'PoE ბიუჯეტი რეზერვით' => $r['poe_budget_watts'].' W',
                'UPS-ზე AC დატვირთვა' => $r['ac_load_watts'].' W',
                'UPS გამომავალი, მინიმუმ' => $r['ups_min_output_watts'].' W',
                'UPS მინიმალური VA' => $r['ups_min_va'].' VA',
                'ბატარეის ნომინალური ენერგია' => $r['battery_nominal_wh'].' Wh',
                'ბატარეა '.$r['battery_voltage'].' V-ზე' => $r['battery_nominal_ah'].' Ah',
            ] as $label => $value)
                <div class="cctv-metric"><span class="cctv-note">{{ $label }}</span><strong>{{ $value }}</strong></div>
            @endforeach
        </div>
        <h3 class="text-lg font-bold mt-6 mb-3">კვება ძაბვის მიხედვით</h3>
        @forelse ($r['power_by_voltage'] as $voltage => $watts)
            <p>{{ $voltage }} — {{ $watts }} W</p>
        @empty
            <p class="cctv-note">ჯერ კამერები არაა დამატებული.</p>
        @endforelse
        <h3 class="text-lg font-bold mt-6 mb-3">ჯგუფების გამოთვლა</h3>
        @foreach ($r['groups'] as $i => $group)
            <p class="cctv-note">№{{ $i + 1 }} — {{ $group['count'] }} × {{ $group['megapixels'] }} MP / {{ strtoupper($group['codec']) }} · {{ $group['video_mbps_each'] }} Mbps ({{ $group['bitrate_source'] }}) · {{ $group['recording_hours_equivalent'] }} სთ/დღე · {{ $group['storage_tb_raw'] }} TB raw</p>
        @endforeach
        <h3 class="text-lg font-bold mt-6 mb-3">მნიშვნელოვანი დაშვებები</h3>
        <ul class="list-disc ms-5 cctv-note">
            <li>ბიტრეიტი დამოკიდებულია განათებაზე, მოძრაობაზე, ხარისხის პარამეტრებზე და VBR/CBR რეჟიმზე; შეამოწმეთ მოწყობილობაში.</li>
            <li>HDD არის decimal TB; NVR-ის bays, SATA მაქსიმუმი, RAID, ფაილური სისტემა და რეალური გამოსაყენებელი მოცულობა გადაამოწმეთ.</li>
            <li>PoE კამერების W არ ემატება UPS-ის AC დატვირთვას ორჯერ. UPS VA და Ah თავსებადი მოდელის დაპირება არ არის.</li>
            <li>შეამოწმეთ UPS-ის მწარმოებლის runtime curve, ბატარეის ტექნოლოგია, სიბერე/ტემპერატურა, გამოსასვლელი ფორმა და მოწყობილობის DC/PoE შესაბამისობა.</li>
        </ul>
    </section>
</x-filament-panels::page>
