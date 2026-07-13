<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="utf-8">
    <title>{{ $estimate->estimate_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        .page {
            padding: 28px 32px;
        }

        .header,
        .meta,
        .summary {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
            padding-bottom: 18px;
        }

        .brand {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .muted {
            color: #475569;
            font-size: 11px;
        }

        .meta td,
        .summary td {
            padding: 6px 0;
            vertical-align: top;
        }

        .meta .label,
        .summary .label {
            width: 160px;
            color: #475569;
        }

        h2 {
            margin: 24px 0 12px;
            font-size: 15px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table.items th,
        table.items td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
        }

        table.items th {
            background: #e2e8f0;
            font-size: 11px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            margin-top: 18px;
            margin-left: auto;
            width: 260px;
            border: 1px solid #cbd5e1;
            border-collapse: collapse;
        }

        .total-box td {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
        }

        .total-box tr:last-child td {
            font-size: 14px;
            font-weight: 700;
            background: #dbeafe;
        }
    </style>
</head>
<body>
@php
    $siteName = $branding['site_name'] ?? config('app.name', 'SafeTech');
    $lineItems = $calculation['line_items'] ?? [];
    $requirements = $calculation['requirements'] ?? [];
    $projectTypeLabel = [
        'cctv' => 'CCTV',
        'network' => 'ქსელი',
        'it' => 'IT',
    ][$estimate->project_type] ?? strtoupper($estimate->project_type);
    $money = fn ($value) => '₾' . number_format((float) $value, 2, '.', ',');
    $quantity = fn ($value) => fmod((float) $value, 1.0) === 0.0
        ? number_format((float) $value, 0, '.', ',')
        : number_format((float) $value, 2, '.', ',');
@endphp

<div class="page">
    <table class="header">
        <tr>
            <td>
                <div class="brand">{{ $siteName }}</div>
                @if(! empty($contact['phone']))
                    <div class="muted">Tel: {{ $contact['phone'] }}</div>
                @endif
                @if(! empty($contact['email']))
                    <div class="muted">Email: {{ $contact['email'] }}</div>
                @endif
                @if(! empty($contact['address']))
                    <div class="muted">{{ $contact['address'] }}</div>
                @endif
            </td>
            <td style="text-align: right;">
                <div style="font-size: 18px; font-weight: 700;">კომერციული შეთავაზება</div>
                <div class="muted">№ {{ $estimate->estimate_number }}</div>
                <div class="muted">{{ $estimate->created_at?->format('Y-m-d H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td class="label">პროექტის ტიპი</td>
            <td>{{ $projectTypeLabel }}</td>
        </tr>
        @if($estimate->client_name)
            <tr>
                <td class="label">კლიენტი</td>
                <td>{{ $estimate->client_name }}</td>
            </tr>
        @endif
        @if($estimate->company)
            <tr>
                <td class="label">კომპანია</td>
                <td>{{ $estimate->company }}</td>
            </tr>
        @endif
        @if($estimate->phone)
            <tr>
                <td class="label">ტელეფონი</td>
                <td>{{ $estimate->phone }}</td>
            </tr>
        @endif
        @if($estimate->email)
            <tr>
                <td class="label">ელფოსტა</td>
                <td>{{ $estimate->email }}</td>
            </tr>
        @endif
    </table>

    <h2>საჭირო აღჭურვილობა</h2>
    <table class="summary">
        <tr>
            <td class="label">რეკორდერი</td>
            <td>{{ $requirements['recorder'] ?? 'არ არის საჭირო' }}</td>
        </tr>
        <tr>
            <td class="label">შენახვა</td>
            <td>{{ $requirements['storage'] ?? 'არ არის საჭირო' }}</td>
        </tr>
        <tr>
            <td class="label">PoE სვიჩი</td>
            <td>{{ $requirements['poe_switch'] ?? 'არ არის საჭირო' }}</td>
        </tr>
        <tr>
            <td class="label">RJ45 / BNC</td>
            <td>{{ $requirements['connectors'] ?? 'არ არის საჭირო' }}</td>
        </tr>
    </table>

    <h2>შეთავაზების დეტალები</h2>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>კომპონენტი</th>
                <th style="width: 90px;">რაოდ.</th>
                <th style="width: 70px;">ერთეული</th>
                <th style="width: 120px;">ფასი / ერთეული</th>
                <th style="width: 120px;">ჯამი</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lineItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['label'] }}</td>
                    <td class="text-right">{{ $quantity($item['quantity'] ?? 0) }}</td>
                    <td>{{ $item['unit'] ?? 'pcs' }}</td>
                    <td class="text-right">{{ $money($item['sell_unit'] ?? 0) }}</td>
                    <td class="text-right">{{ $money($item['sell_total'] ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">ხაზები ჯერ არ არის დამატებული.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td>სულ</td>
            <td class="text-right">{{ $money($estimate->final_total) }}</td>
        </tr>
    </table>

    @if($estimate->notes)
        <h2>შენიშვნები</h2>
        <div>{{ $estimate->notes }}</div>
    @endif
</div>
</body>
</html>
