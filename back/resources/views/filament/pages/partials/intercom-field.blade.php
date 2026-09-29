<label class="ac-field" wire:key="intercom-{{ $field['key'] }}">
    <span>{{ $field['ka'] }}</span>
    @if($field['type'] === 'select')
        <select
            @if(in_array($field['key'], ['apartments', 'doors'], true))
                wire:model="config.{{ $field['key'] }}" x-on:change="$wire.scopeConfirmed = false"
            @else
                wire:model.live="config.{{ $field['key'] }}"
            @endif
            wire:loading.attr="disabled"
        >
            @foreach($this->intercomFieldOptions($field) as $option)
                <option value="{{ $option['value'] }}">{{ $option['ka'] }}</option>
            @endforeach
        </select>
    @elseif($field['type'] === 'checkbox')
        <input type="checkbox" style="width:1.25rem;height:1.25rem" wire:model.live="config.{{ $field['key'] }}" wire:loading.attr="disabled">
    @else
        <input type="number" min="{{ $field['min'] ?? 0 }}" max="{{ $field['max'] ?? 100000 }}" step="{{ $field['step'] ?? 1 }}" wire:model.live.debounce.400ms="config.{{ $field['key'] }}" wire:loading.attr="disabled">
    @endif
</label>
