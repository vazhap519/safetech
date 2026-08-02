<?php

namespace App\Filament\Support;

final class AdminIconOptions
{
    /** @return array<string, string> */
    public static function content(): array
    {
        return [
            'settings' => 'Settings',
            'security' => 'Security',
            'shield_lock' => 'Shield Lock',
            'verified' => 'Verified',
            'fingerprint' => 'Fingerprint',
            'videocam' => 'Video Camera',
            'lan' => 'LAN / Network',
            'dns' => 'Server / DNS',
            'wifi' => 'Wi-Fi',
            'storage' => 'Storage',
            'backup' => 'Backup Cloud',
            'cloud_done' => 'Cloud Complete',
            'monitoring' => 'Monitoring',
            'query_stats' => 'Analytics',
            'speed' => 'Speed',
            'business' => 'Business',
            'apartment' => 'Apartment / Building',
            'storefront' => 'Storefront',
            'warehouse' => 'Warehouse',
            'manufacturing' => 'Manufacturing',
            'directions_car' => 'Vehicle',
            'door_front' => 'Door Access',
            'toll' => 'Gate / Barrier',
            'support_agent' => 'Support Agent',
            'location_on' => 'Location',
            'call' => 'Phone',
            'mail' => 'Mail',
            'history' => 'History',
            'architecture' => 'Architecture',
            'schema' => 'Schema / Automation',
            'hub' => 'Hub',
            'dynamic_feed' => 'Feed / Sharing',
            'settings_input_component' => 'Control Panel',
        ];
    }

    /** @return array<string, string> */
    public static function socials(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'tiktok' => 'TikTok',
            'x' => 'X',
            'youtube' => 'YouTube',
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp',
            'viber' => 'Viber',
            'pinterest' => 'Pinterest',
            'email' => 'Email',
        ];
    }

    /** @return array<string, string> */
    public static function shareNetworks(): array
    {
        return [
            'facebook' => 'Facebook',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'linkedin' => 'LinkedIn',
            'x' => 'X',
            'pinterest' => 'Pinterest',
            'viber' => 'Viber',
            'email' => 'Email',
            'native' => 'Device share menu',
            'copy' => 'Copy link',
        ];
    }
}
