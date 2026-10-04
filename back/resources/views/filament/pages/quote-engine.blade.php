<x-filament-panels::page>
    <style>
        .qe-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr));gap:1rem}
        .qe-card{border:1px solid #94a3b844;padding:1.25rem;border-radius:1rem;margin-bottom:1rem}
        .qe-field{display:grid;gap:.35rem;font-size:.85rem}
        .qe-field input,.qe-field select,.qe-field textarea{width:100%;padding:.62rem .7rem;border:1px solid #94a3b866;border-radius:.55rem;background:transparent;color:inherit}
        .qe-field select option{color:#111827}
        .qe-note{font-size:.84rem;line-height:1.6;color:#64748b}.dark .qe-note{color:#94a3b8}
        .qe-table{width:100%;border-collapse:collapse;min-width:980px}
        .qe-table th,.qe-table td{padding:.65rem;border-bottom:1px solid #94a3b833;text-align:left;vertical-align:middle}
        .qe-table input{width:110px;padding:.48rem;border:1px solid #94a3b866;border-radius:.45rem;background:transparent;color:inherit}
        .qe-money{text-align:right;white-space:nowrap}
        .qe-metric{border:1px solid #94a3b844;border-radius:.75rem;padding:1rem}
        .qe-metric strong{display:block;font-size:1.25rem;margin-top:.3rem}
        .qe-warning{border:1px solid #f59e0b66;background:#f59e0b12;padding:.8rem 1rem;border-radius:.7rem}
        .qe-ok{border:1px solid #22c55e66;background:#22c55e12;padding:.8rem 1rem;border-radius:.7rem}
        .qe-actions{display:flex;flex-wrap:wrap;gap:.75rem}
        .qe-copy{width:100%;min-height:180px;border:1px solid #94a3b866;border-radius:.65rem;padding:.8rem;background:transparent;color:inherit}
    </style>

    @php($profile = $this->profile())
    @php($quote = $this->quote())

    <section class="qe-card">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-xl font-bold">1. კლიენტი და პროექტი</h2>
                <p class="qe-note mt-1">შეთავაზება შეინახება Estimate-ში და იქიდან ჩამოტვირთავ PDF-ს.</p>
            </div>
            <div class="qe-actions">
                <x-filament::button color="gray" wire:click="syncCatalog">კატალოგის სინქრონიზაცია</x-filament::button>
                <a class="fi-btn fi-color-gray" href="{{ \App\Filament\Resources\QuoteCatalogItemResource::getUrl('index') }}">Quote Catalog</a>
            </div>
        </div>

        <div class="qe-grid mt-5">
            <label class="qe-field">
                <span>სერვისი *</span>
                <select wire:model.live="serviceId">
                    @foreach($this->serviceOptions() as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="qe-field"><span>კლიენტი</span><input wire:model.blur="clientName" type="text"></label>
            <label class="qe-field"><span>კომპანია</span><input wire:model.blur="company" type="text"></label>
            <label class="qe-field"><span>ტელეფონი</span><input wire:model.blur="phone" type="text"></label>
            <label class="qe-field"><span>ელფოსტა</span><input wire:model.blur="email" type="email"></label>
            <label class="qe-field"><span>პროექტის სათაური</span><input wire:model.blur="projectTitle" type="text" placeholder="მაგ. 9 კამერის სისტემა"></label>
            <label class="qe-field"><span>ობიექტი / მისამართი</span><input wire:model.blur="location" type="text"></label>
        </div>
    </section>

    @if($profile)
        <section class="qe-card">
            <h2 class="text-xl font-bold mb-4">2. მოთხოვნის პარამეტრები</h2>
            <div class="qe-grid">
                @if(!empty($profile['projectSize']['options']))
                    <label class="qe-field">
                        <span>{{ $profile['projectSize']['label'] }}</span>
                        <select wire:model.live="projectSize">
                            @foreach($profile['projectSize']['options'] as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if(!empty($profile['propertyType']['options']))
                    <label class="qe-field">
                        <span>{{ $profile['propertyType']['label'] }}</span>
                        <select wire:model.live="propertyType">
                            @foreach($profile['propertyType']['options'] as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if(!empty($profile['packages']))
                    <label class="qe-field">
                        <span>პაკეტი</span>
                        <select wire:model.live="packageKey">
                            @foreach($profile['packages'] as $package)
                                <option value="{{ $package['key'] }}">{{ $package['title'] }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @foreach($profile['fields'] ?? [] as $field)
                    @php($key = $field['key'])
                    @if($field['type'] === 'checkbox')
                        <label class="qe-field">
                            <span>{{ $field['label'] }}</span>
                            <span class="flex items-center gap-2 min-h-11">
                                <input style="width:auto" type="checkbox" wire:model.live="values.{{ $key }}">
                                <span class="qe-note">ჩართვა / გამორთვა</span>
                            </span>
                        </label>
                    @elseif($field['type'] === 'select')
                        <label class="qe-field">
                            <span>{{ $field['label'] }}</span>
                            <select wire:model.live="values.{{ $key }}">
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                    @elseif($field['type'] === 'textarea')
                        <label class="qe-field">
                            <span>{{ $field['label'] }}</span>
                            <textarea wire:model.live.debounce.500ms="values.{{ $key }}" rows="3"></textarea>
                        </label>
                    @else
                        <label class="qe-field">
                            <span>{{ $field['label'] }} @if($field['unit'])({{ $field['unit'] }})@endif</span>
                            <input
                                type="{{ $field['type'] === 'number' ? 'number' : 'text' }}"
                                @if($field['min'] !== null) min="{{ $field['min'] }}" @endif
                                @if($field['max'] !== null) max="{{ $field['max'] }}" @endif
                                @if($field['step'] !== null) step="{{ $field['step'] }}" @endif
                                wire:model.live.debounce.500ms="values.{{ $key }}"
                            >
                        </label>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <section class="qe-card">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
            <div>
                <h2 class="text-xl font-bold">3. ავტომატური BOM და ფასები</h2>
                <p class="qe-note">რაოდენობა, თვითღირებულება და კლიენტის ფასი შეგიძლია კონკრეტული შეთავაზებისთვის ხელით შეცვალო.</p>
            </div>
            <div class="qe-grid" style="min-width:min(100%,460px)">
                <label class="qe-field"><span>სამუშაო / მონტაჟი (₾)</span><input type="number" min="0" step=".01" wire:model.live.debounce.400ms="laborPrice"></label>
                <label class="qe-field"><span>ფასდაკლება (%)</span><input type="number" min="0" max="100" step=".1" wire:model.live.debounce.400ms="discountPercentage"></label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="qe-table">
                <thead>
                    <tr>
                        <th>✓</th>
                        <th>კომპონენტი</th>
                        <th>რაოდ.</th>
                        <th>შესყიდვა</th>
                        <th>ფასნამატი</th>
                        <th>გაყიდვა</th>
                        <th>ჯამი</th>
                        <th>მომწოდებელი</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quote['components'] ?? [] as $component)
                        <tr wire:key="quote-component-{{ $component['key'] }}">
                            <td>
                                <input
                                    type="checkbox"
                                    @checked($component['selected'])
                                    @disabled($component['required'])
                                    wire:change="setComponentValue('{{ $component['key'] }}', 'selected', $event.target.checked)"
                                >
                            </td>
                            <td>
                                <strong>{{ $component['label'] }}</strong>
                                <div class="qe-note">
                                    {{ $component['category'] }}
                                    @if($component['brand'] || $component['model'])
                                        · {{ trim(($component['brand'] ?? '').' '.($component['model'] ?? '')) }}
                                    @endif
                                    @if($component['required']) · აუცილებელი @endif
                                </div>
                            </td>
                            <td>
                                <input type="number" min="0" step=".01" value="{{ $component['quantity'] }}" wire:change="setComponentValue('{{ $component['key'] }}', 'quantity', $event.target.value)">
                            </td>
                            <td>
                                <input type="number" min="0" step=".01" value="{{ $component['purchase_price'] }}" placeholder="—" wire:change="setComponentValue('{{ $component['key'] }}', 'purchase_price', $event.target.value)">
                            </td>
                            <td>
                                <input type="number" min="0" max="1000" step=".1" value="{{ $component['markup_percentage'] }}" wire:change="setComponentValue('{{ $component['key'] }}', 'markup_percentage', $event.target.value)">
                            </td>
                            <td>
                                <input type="number" min="0" step=".01" value="{{ $component['sale_price'] }}" wire:change="setComponentValue('{{ $component['key'] }}', 'sale_price', $event.target.value)">
                            </td>
                            <td class="qe-money"><strong>{{ number_format((float)$component['sale_total'], 2, '.', ' ') }} ₾</strong></td>
                            <td class="qe-note">{{ $component['supplier'] ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="qe-note">არჩეული პარამეტრებისთვის კომპონენტები ვერ მოიძებნა.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="qe-card">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
            <div>
                <h2 class="text-xl font-bold">4. ხელით დამატებული პოზიციები</h2>
                <p class="qe-note">დაამატე ნებისმიერი მოწყობილობა, მასალა, ხელობა, ტრანსპორტი ან სხვა ხარჯი. თვითღირებულება და ფასნამატი კლიენტის PDF-ში არ გამოჩნდება.</p>
            </div>
            <x-filament::button wire:click="addManualItem">+ პოზიციის დამატება</x-filament::button>
        </div>

        <div class="overflow-x-auto">
            <table class="qe-table">
                <thead><tr><th>ტიპი</th><th>დასახელება</th><th>რაოდ.</th><th>ერთეული</th><th>თვითღ.</th><th>Markup %</th><th>გასაყიდი ფასი</th><th>ჯამი</th><th></th></tr></thead>
                <tbody>
                @forelse($manualItems as $index => $item)
                    @php($normalized = $this->normalizedManualItems())
                    <tr wire:key="manual-item-{{ $index }}">
                        <td><select wire:model.live="manualItems.{{ $index }}.category"><option value="equipment">მოწყობილობა</option><option value="material">მასალა</option><option value="labor">ხელობა</option><option value="transport">ტრანსპორტი</option><option value="other">სხვა</option></select></td>
                        <td><input style="width:220px" type="text" wire:model.live.debounce.400ms="manualItems.{{ $index }}.label" placeholder="მაგ. IP კამერა"></td>
                        <td><input type="number" min="0" step=".01" wire:model.live.debounce.300ms="manualItems.{{ $index }}.quantity"></td>
                        <td><select wire:model.live="manualItems.{{ $index }}.unit"><option value="pcs">ცალი</option><option value="m">მეტრი</option><option value="set">კომპლექტი</option><option value="job">სამუშაო</option><option value="hour">საათი</option></select></td>
                        <td><input type="number" min="0" step=".01" wire:model.live.debounce.300ms="manualItems.{{ $index }}.purchase_price"></td>
                        <td><input type="number" min="0" max="1000" step=".1" wire:model.live.debounce.300ms="manualItems.{{ $index }}.markup_percentage"></td>
                        <td><input type="number" min="0" step=".01" wire:model.live.debounce.300ms="manualItems.{{ $index }}.sale_price" placeholder="0 = ავტომატური"></td>
                        <td class="qe-money"><strong>{{ number_format((float)($this->normalizedManualItems()[$index]['sale_total'] ?? 0), 2, '.', ' ') }} ₾</strong></td>
                        <td><button type="button" wire:click="removeManualItem({{ $index }})" class="text-danger-600">წაშლა</button></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="qe-note">ხელით დამატებული პოზიციები ჯერ არ არის.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @php($manualTotals = $this->manualTotals())
        @if($manualTotals['sale'] > 0)
            <div class="qe-note mt-3">ხელით დამატებული პოზიციები: თვითღირებულება {{ number_format($manualTotals['cost'], 2, '.', ' ') }} ₾ · გაყიდვა {{ number_format($manualTotals['sale'], 2, '.', ' ') }} ₾ · მოგება {{ number_format($manualTotals['profit'], 2, '.', ' ') }} ₾</div>
        @endif
    </section>

    @if($quote['pricing_complete'])
        <div class="qe-ok mb-4"><strong>✓ ფასები სრულადაა შევსებული.</strong> მოგებისა და მარჟის დათვლა დასრულებულია.</div>
    @else
        <div class="qe-warning mb-4">
            <strong>ფინანსური მონაცემები დასაზუსტებელია.</strong>
            აკლია თვითღირებულება: {{ $quote['missing_cost_count'] ?? 0 }} პოზიციას;
            გასაყიდი ფასი: {{ $quote['missing_sale_count'] ?? 0 }} პოზიციას.
        </div>
    @endif

    <section class="qe-card">
        <h2 class="text-xl font-bold mb-4">5. ფინანსური შეჯამება</h2>
        <div class="qe-grid">
            <div class="qe-metric"><span class="qe-note">სერვისი</span><strong>{{ number_format((float)($quote['service_subtotal'] ?? 0), 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">კომპონენტები</span><strong>{{ number_format((float)($quote['component_subtotal'] ?? 0), 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">სამუშაო</span><strong>{{ number_format((float)($quote['labor_subtotal'] ?? 0), 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">ფასდაკლება</span><strong>-{{ number_format((float)($quote['discount_amount'] ?? 0), 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">ხელით დამატებული</span><strong>{{ number_format((float)$this->manualTotals()['sale'], 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">კლიენტის საბოლოო ფასი</span><strong>{{ number_format((float)($quote['final_total'] ?? 0) + (float)$this->manualTotals()['sale'], 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">ცნობილი თვითღირებულება</span><strong>{{ number_format((float)($quote['known_cost_total'] ?? 0), 2, '.', ' ') }} ₾</strong></div>
            <div class="qe-metric"><span class="qe-note">მოგება (overhead-მდე)</span><strong>{{ $quote['profit_total'] === null ? 'დასაზუსტებელია' : number_format((float)$quote['profit_total'], 2, '.', ' ').' ₾' }}</strong></div>
            <div class="qe-metric"><span class="qe-note">მარჟა</span><strong>{{ $quote['gross_margin_percentage'] === null ? '—' : number_format((float)$quote['gross_margin_percentage'], 1). '%' }}</strong></div>
        </div>
    </section>

    <section class="qe-card">
        <h2 class="text-xl font-bold mb-4">6. კლიენტის ტექსტი და შენახვა</h2>
        <div class="qe-grid">
            <label class="qe-field"><span>კლიენტისთვის შენიშვნა</span><textarea wire:model.live.debounce.500ms="clientNote" rows="4" placeholder="მაგ. შეთავაზება მოქმედებს 7 დღე."></textarea></label>
            <label class="qe-field"><span>შიდა შენიშვნა (PDF-ში არ შევა)</span><textarea wire:model.blur="internalNotes" rows="4"></textarea></label>
        </div>

        <div class="mt-5">
            <label class="qe-field">
                <span>WhatsApp / Messenger ტექსტი</span>
                <textarea id="quote-client-text" class="qe-copy" readonly>{{ $this->clientText() }}</textarea>
            </label>
        </div>

        <div class="qe-actions mt-5">
            <x-filament::button
                color="gray"
                x-on:click="navigator.clipboard.writeText(document.getElementById('quote-client-text').value)"
            >ტექსტის კოპირება</x-filament::button>
            <x-filament::button wire:click="saveQuote" wire:loading.attr="disabled">შეთავაზების შენახვა და PDF</x-filament::button>
        </div>
    </section>
</x-filament-panels::page>
