<?php

namespace Database\Seeders;

/**
 * Granular services mirrored from the SafeTech Google Business Profile.
 *
 * These definitions intentionally stay separate from the seeding mechanics so
 * the public website catalog and the Google profile can be audited side by side.
 */
final class GoogleBusinessServiceDefinitions
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return [
            // Security system installation service.
            self::item('ip-camera-installation', 'security-access-automation', 'videocam', 'cctv', 'IP კამერების მონტაჟი', 'IP Camera Installation', 'Монтаж IP-камер'),
            self::item('video-surveillance-system-installation', 'security-access-automation', 'monitoring', 'cctv', 'ვიდეომეთვალყურეობის სისტემების მონტაჟი', 'Video Surveillance System Installation', 'Монтаж систем видеонаблюдения'),
            self::item('intercom-installation', 'security-access-automation', 'door_front', 'access-control', 'დომოფონების მონტაჟი', 'Intercom Installation', 'Монтаж домофонов'),
            self::item('access-control-system-installation', 'security-access-automation', 'fingerprint', 'access-control', 'დაშვების კონტროლის სისტემების მონტაჟი', 'Access Control System Installation', 'Монтаж систем контроля доступа'),
            self::item('alarm-system-installation', 'security-access-automation', 'shield_lock', 'access-control', 'სიგნალიზაციის მონტაჟი', 'Alarm System Installation', 'Монтаж охранной сигнализации'),
            self::item('barrier-gate-setup', 'security-access-automation', 'toll', 'access-control', 'შლაგბაუმების მონტაჟი და გამართვა', 'Barrier Gate Installation and Setup', 'Монтаж и настройка шлагбаумов'),

            // Computer service.
            self::item('onsite-computer-service', 'computer-services', 'support_agent', 'it-support', 'კომპიუტერის ადგილზე მომსახურება', 'On-site Computer Service', 'Выездное обслуживание компьютеров'),
            self::item('personal-computer-assembly', 'computer-services', 'memory', 'it-support', 'პერსონალური კომპიუტერის აწყობა', 'Custom Personal Computer Assembly', 'Сборка персонального компьютера'),
            self::item('computer-upgrade-optimization', 'computer-services', 'speed', 'it-support', 'კომპიუტერის განახლება და ოპტიმიზაცია', 'Computer Upgrade and Optimization', 'Модернизация и оптимизация компьютера'),
            self::item('computer-component-replacement', 'computer-services', 'settings_input_component', 'it-support', 'კომპიუტერის კომპონენტების ჩანაცვლება', 'Computer Component Replacement', 'Замена компонентов компьютера'),
            self::item('ram-upgrade', 'computer-services', 'memory', 'it-support', 'ოპერატიული მეხსიერების (RAM) განახლება', 'RAM Upgrade', 'Увеличение оперативной памяти (RAM)'),
            self::item('computer-preventive-maintenance', 'computer-services', 'cleaning_services', 'it-support', 'კომპიუტერის პროფილაქტიკური მომსახურება', 'Preventive Computer Maintenance', 'Профилактическое обслуживание компьютера'),
            self::item('new-computer-setup', 'computer-services', 'desktop_windows', 'it-support', 'ახალი კომპიუტერის მომზადება სამუშაოდ', 'New Computer Setup', 'Настройка нового компьютера'),
            self::item('computer-peripheral-troubleshooting', 'computer-services', 'query_stats', 'it-support', 'კომპიუტერისა და პერიფერიული მოწყობილობების დიაგნოსტიკა', 'Computer and Peripheral Troubleshooting', 'Диагностика компьютера и периферии'),

            // Computer networking service.
            self::item('low-voltage-cabling', 'network-infrastructure', 'cable', 'networking', 'სუსტი დენების გაყვანა', 'Low-voltage Cabling', 'Прокладка слаботочных кабелей'),
            self::item('server-room-installation', 'network-infrastructure', 'dns', 'server-infrastructure', 'სერვერ ოთახის კომპლექსური მოწყობა', 'Complete Server Room Installation', 'Комплексное оснащение серверной'),
            self::item('cat6-cabling', 'network-infrastructure', 'lan', 'networking', 'CAT6 ქსელის კაბელის გაყვანა', 'CAT6 Network Cabling', 'Прокладка сетевого кабеля CAT6'),
            self::item('rj45-connector-installation', 'network-infrastructure', 'settings_input_component', 'networking', 'RJ45 ქსელური კონექტორების მონტაჟი', 'RJ45 Network Connector Installation', 'Монтаж сетевых коннекторов RJ45'),
            self::item('lan-installation', 'network-infrastructure', 'lan', 'networking', 'LAN ქსელის მონტაჟი', 'LAN Network Installation', 'Монтаж локальной сети LAN'),
            self::item('wifi-network-installation', 'network-infrastructure', 'wifi', 'networking', 'Wi-Fi ქსელის მონტაჟი და გამართვა', 'Wi-Fi Network Installation and Setup', 'Монтаж и настройка сети Wi-Fi'),
            self::item('network-rack-installation', 'network-infrastructure', 'dns', 'server-infrastructure', 'ქსელური კარადების მონტაჟი', 'Network Rack Installation', 'Монтаж телекоммуникационных шкафов'),
            self::item('patch-panel-installation', 'network-infrastructure', 'settings_input_component', 'networking', 'Patch Panel-ის მონტაჟი და დაერთება', 'Patch Panel Installation and Termination', 'Монтаж и расключение патч-панели'),

            // Computer support and services.
            self::item('it-technical-support', 'business-it', 'support_agent', 'it-support', 'IT ტექნიკური მხარდაჭერა', 'IT Technical Support', 'Техническая IT-поддержка'),
            self::item('office-it-infrastructure', 'business-it', 'business', 'server-infrastructure', 'ოფისების IT ინფრასტრუქტურის მოწყობა', 'Office IT Infrastructure Setup', 'Организация IT-инфраструктуры офиса'),
            self::item('computers-workstations-setup', 'business-it', 'desktop_windows', 'it-support', 'კომპიუტერებისა და სამუშაო ადგილების გამართვა', 'Computer and Workstation Setup', 'Настройка компьютеров и рабочих мест'),
            self::item('software-installation', 'business-it', 'settings', 'it-support', 'პროგრამული უზრუნველყოფის ინსტალაცია', 'Software Installation', 'Установка программного обеспечения'),
            self::item('virus-removal-optimization', 'business-it', 'security', 'it-support', 'ვირუსების მოცილება და სისტემის ოპტიმიზაცია', 'Virus Removal and System Optimization', 'Удаление вирусов и оптимизация системы'),
            self::item('network-printer-scanner-setup', 'business-it', 'dynamic_feed', 'networking', 'ქსელური პრინტერებისა და სკანერების გამართვა', 'Network Printer and Scanner Setup', 'Настройка сетевых принтеров и сканеров'),
            self::item('corporate-email-setup', 'business-it', 'mail', 'it-support', 'კორპორაციული ელფოსტის გამართვა', 'Corporate Email Setup', 'Настройка корпоративной почты'),
            self::item('microsoft-365-setup-migration', 'business-it', 'cloud_done', 'it-support', 'Microsoft 365-ის გამართვა და მიგრაცია', 'Microsoft 365 Setup and Migration', 'Настройка и миграция Microsoft 365'),
            self::item('remote-it-support', 'business-it', 'monitoring', 'it-support', 'დისტანციური IT მხარდაჭერა', 'Remote IT Support', 'Удаленная IT-поддержка'),
            self::item('computer-network-diagnostics', 'business-it', 'query_stats', 'networking', 'კომპიუტერული ქსელების დიაგნოსტიკა', 'Computer Network Diagnostics', 'Диагностика компьютерных сетей'),
            self::item('data-backup-recovery', 'business-it', 'backup', 'it-support', 'მონაცემების სარეზერვო ასლების შექმნა და აღდგენა', 'Data Backup and Recovery', 'Резервное копирование и восстановление данных'),

            // Apple/macOS services shown in the same Google category.
            self::item('macos-installation-configuration', 'computer-services', 'desktop_windows', 'it-support', 'macOS-ის ინსტალაცია და კონფიგურაცია', 'macOS Installation and Configuration', 'Установка и настройка macOS'),
            self::item('macbook-imac-software-setup', 'computer-services', 'settings', 'it-support', 'MacBook-ისა და iMac-ის პროგრამული გამართვა', 'MacBook and iMac Software Setup', 'Программная настройка MacBook и iMac'),
            self::item('mac-software-installation', 'computer-services', 'cloud_done', 'it-support', 'Mac-ზე პროგრამების ინსტალაცია', 'Mac Software Installation', 'Установка программ на Mac'),
            self::item('mac-diagnostics-repair', 'computer-services', 'query_stats', 'it-support', 'MacBook-ისა და iMac-ის დიაგნოსტიკა', 'MacBook and iMac Diagnostics', 'Диагностика MacBook и iMac'),
            self::item('macos-update-optimization', 'computer-services', 'speed', 'it-support', 'macOS-ის განახლება და ოპტიმიზაცია', 'macOS Update and Optimization', 'Обновление и оптимизация macOS'),
            self::item('mac-data-recovery-backup', 'computer-services', 'backup', 'it-support', 'Mac-ის მონაცემების გადატანა და სარეზერვო კოპირება', 'Mac Data Migration and Backup', 'Перенос и резервное копирование данных Mac'),
            self::item('mac-printer-wifi-setup', 'computer-services', 'wifi', 'networking', 'Mac-ის პრინტერთან და Wi-Fi ქსელთან დაკავშირება', 'Mac Printer and Wi-Fi Setup', 'Подключение Mac к принтеру и Wi-Fi'),

            // Telecommunications contractor.
            self::item('structured-cabling-installation', 'telecommunications-infrastructure', 'lan', 'networking', 'სტრუქტურირებული საკაბელო სისტემების მონტაჟი', 'Structured Cabling System Installation', 'Монтаж структурированных кабельных систем'),
            self::item('low-voltage-design-estimation', 'telecommunications-infrastructure', 'architecture', 'networking', 'სუსტი დენების ინფრასტრუქტურის პროექტირება და ხარჯთაღრიცხვა', 'Low-voltage Infrastructure Design and Estimation', 'Проектирование и расчет слаботочной инфраструктуры'),
            self::item('telecommunications-infrastructure-installation', 'telecommunications-infrastructure', 'hub', 'networking', 'სატელეკომუნიკაციო ინფრასტრუქტურის მოწყობა', 'Telecommunications Infrastructure Installation', 'Монтаж телекоммуникационной инфраструктуры'),
            self::item('fiber-optic-commercial-network-installation', 'telecommunications-infrastructure', 'settings_input_component', 'networking', 'ოპტიკური და კომერციული ქსელების დამონტაჟება', 'Fiber-optic and Commercial Network Installation', 'Монтаж оптоволоконных и коммерческих сетей'),
            self::item('office-digital-infrastructure', 'telecommunications-infrastructure', 'apartment', 'server-infrastructure', 'ოფისებისა და ბიზნესობიექტების ციფრული ინფრასტრუქტურა', 'Digital Infrastructure for Offices and Businesses', 'Цифровая инфраструктура офисов и коммерческих объектов'),
        ];
    }

    /** @return array<string, mixed> */
    private static function item(
        string $slug,
        string $category,
        string $icon,
        string $calculatorProfile,
        string $ka,
        string $en,
        string $ru,
    ): array {
        return [
            'slug' => $slug,
            'category' => $category,
            'icon' => $icon,
            'calculator_profile' => $calculatorProfile,
            'name' => self::t($ka, $en, $ru),
            'description' => self::t(
                "{$ka} — მოთხოვნის შეფასება, თავსებადი მოწყობილობების შერჩევა, პროფესიონალური მონტაჟი ან გამართვა და საბოლოო ტესტირება თბილისში და საქართველოს მასშტაბით.",
                "{$en} with requirements assessment, compatible equipment selection, professional installation or configuration, and final testing in Tbilisi and across Georgia.",
                "{$ru}: оценка требований, подбор совместимого оборудования, профессиональный монтаж или настройка и итоговое тестирование в Тбилиси и по всей Грузии.",
            ),
            'keywords' => [
                self::t($ka, $en, $ru),
                self::t("{$ka} თბილისი", "{$en} Tbilisi", "{$ru} Тбилиси"),
                self::t("{$ka} საქართველო", "{$en} Georgia", "{$ru} Грузия"),
            ],
        ];
    }

    /** @return array{ka: string, en: string, ru: string} */
    private static function t(string $ka, string $en, string $ru): array
    {
        return compact('ka', 'en', 'ru');
    }
}
