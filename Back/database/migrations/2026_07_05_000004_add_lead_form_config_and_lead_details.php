<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->json('lead_form')->nullable()->after('sla');
        });

        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->string('service_slug')->nullable()->after('service');
            $table->json('details')->nullable()->after('property_type');
        });

        foreach ($this->defaultLeadForms() as $slug => $leadForm) {
            Service::query()
                ->where('slug', $slug)
                ->update(['lead_form' => $leadForm]);
        }
    }

    public function down(): void
    {
        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->dropColumn(['service_slug', 'details']);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('lead_form');
        });
    }

    private function defaultLeadForms(): array
    {
        return [
            'cctv' => [
                'project_size_label_ka' => 'კამერების მასშტაბი',
                'project_size_label_en' => 'Camera scale',
                'project_size_label_ru' => 'Масштаб камер',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => '1-16 კამერა', 'en' => '1-16 cameras', 'ru' => '1-16 камер'],
                    ['value' => 'medium', 'ka' => '16-64 კამერა', 'en' => '16-64 cameras', 'ru' => '16-64 камеры'],
                    ['value' => 'large', 'ka' => '64+ კამერა', 'en' => '64+ cameras', 'ru' => '64+ камер'],
                ],
                'property_type_label_ka' => 'ობიექტის ტიპი',
                'property_type_label_en' => 'Property type',
                'property_type_label_ru' => 'Тип объекта',
                'property_type_options' => [
                    ['value' => 'office', 'ka' => 'ოფისი', 'en' => 'Office', 'ru' => 'Офис'],
                    ['value' => 'warehouse', 'ka' => 'საწყობი', 'en' => 'Warehouse', 'ru' => 'Склад'],
                    ['value' => 'retail', 'ka' => 'მაღაზია', 'en' => 'Retail', 'ru' => 'Магазин'],
                    ['value' => 'residential', 'ka' => 'საცხოვრებელი', 'en' => 'Residential', 'ru' => 'Жилой объект'],
                ],
                'extra_fields' => [
                    [
                        'key' => 'camera_count',
                        'type' => 'number',
                        'required' => true,
                        'ka' => 'კამერების რაოდენობა',
                        'en' => 'Number of cameras',
                        'ru' => 'Количество камер',
                        'placeholder_ka' => 'მაგ: 24',
                        'placeholder_en' => 'Example: 24',
                        'placeholder_ru' => 'Например: 24',
                    ],
                    [
                        'key' => 'recording_days',
                        'type' => 'number',
                        'required' => false,
                        'ka' => 'ჩანაწერის დღეები',
                        'en' => 'Recording days',
                        'ru' => 'Дней хранения',
                        'placeholder_ka' => 'მაგ: 30',
                        'placeholder_en' => 'Example: 30',
                        'placeholder_ru' => 'Например: 30',
                    ],
                    [
                        'key' => 'camera_resolution',
                        'type' => 'select',
                        'required' => false,
                        'ka' => 'სასურველი გარჩევადობა',
                        'en' => 'Preferred resolution',
                        'ru' => 'Желаемое разрешение',
                        'options' => [
                            ['value' => '2mp', 'ka' => '2MP', 'en' => '2MP', 'ru' => '2MP'],
                            ['value' => '4mp', 'ka' => '4MP', 'en' => '4MP', 'ru' => '4MP'],
                            ['value' => '8mp', 'ka' => '8MP / 4K', 'en' => '8MP / 4K', 'ru' => '8MP / 4K'],
                        ],
                    ],
                ],
            ],
            'access-control' => [
                'project_size_label_ka' => 'წვდომის წერტილების მასშტაბი',
                'project_size_label_en' => 'Access scale',
                'project_size_label_ru' => 'Масштаб точек доступа',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => '1-5 კარი', 'en' => '1-5 doors', 'ru' => '1-5 дверей'],
                    ['value' => 'medium', 'ka' => '6-20 კარი', 'en' => '6-20 doors', 'ru' => '6-20 дверей'],
                    ['value' => 'large', 'ka' => '20+ კარი', 'en' => '20+ doors', 'ru' => '20+ дверей'],
                ],
                'property_type_label_ka' => 'ობიექტის ტიპი',
                'property_type_label_en' => 'Property type',
                'property_type_label_ru' => 'Тип объекта',
                'property_type_options' => [
                    ['value' => 'office', 'ka' => 'ოფისი', 'en' => 'Office', 'ru' => 'Офис'],
                    ['value' => 'clinic', 'ka' => 'კლინიკა', 'en' => 'Clinic', 'ru' => 'Клиника'],
                    ['value' => 'hotel', 'ka' => 'სასტუმრო', 'en' => 'Hotel', 'ru' => 'Отель'],
                    ['value' => 'enterprise', 'ka' => 'საწარმო', 'en' => 'Enterprise', 'ru' => 'Предприятие'],
                ],
                'extra_fields' => [
                    [
                        'key' => 'door_count',
                        'type' => 'number',
                        'required' => true,
                        'ka' => 'კარების რაოდენობა',
                        'en' => 'Door count',
                        'ru' => 'Количество дверей',
                        'placeholder_ka' => 'მაგ: 8',
                        'placeholder_en' => 'Example: 8',
                        'placeholder_ru' => 'Например: 8',
                    ],
                    [
                        'key' => 'reader_type',
                        'type' => 'select',
                        'required' => false,
                        'ka' => 'რიდერის ტიპი',
                        'en' => 'Reader type',
                        'ru' => 'Тип считывателя',
                        'options' => [
                            ['value' => 'card', 'ka' => 'ბარათი', 'en' => 'Card', 'ru' => 'Карта'],
                            ['value' => 'biometric', 'ka' => 'ბიომეტრია', 'en' => 'Biometric', 'ru' => 'Биометрия'],
                            ['value' => 'hybrid', 'ka' => 'შერეული', 'en' => 'Hybrid', 'ru' => 'Гибрид'],
                        ],
                    ],
                ],
            ],
            'networking' => [
                'project_size_label_ka' => 'ქსელის მასშტაბი',
                'project_size_label_en' => 'Network size',
                'project_size_label_ru' => 'Размер сети',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა ობიექტი', 'en' => 'Small site', 'ru' => 'Малый объект'],
                    ['value' => 'medium', 'ka' => 'საშუალო ობიექტი', 'en' => 'Medium site', 'ru' => 'Средний объект'],
                    ['value' => 'large', 'ka' => 'დიდი ობიექტი', 'en' => 'Large site', 'ru' => 'Крупный объект'],
                ],
                'property_type_label_ka' => 'ქსელის გარემო',
                'property_type_label_en' => 'Network environment',
                'property_type_label_ru' => 'Тип сети',
                'property_type_options' => [
                    ['value' => 'office', 'ka' => 'ოფისი', 'en' => 'Office', 'ru' => 'Офис'],
                    ['value' => 'hotel', 'ka' => 'სასტუმრო', 'en' => 'Hotel', 'ru' => 'Отель'],
                    ['value' => 'warehouse', 'ka' => 'საწყობი', 'en' => 'Warehouse', 'ru' => 'Склад'],
                    ['value' => 'campus', 'ka' => 'კამპუსი', 'en' => 'Campus', 'ru' => 'Кампус'],
                ],
                'extra_fields' => [
                    [
                        'key' => 'router_count',
                        'type' => 'number',
                        'required' => false,
                        'ka' => 'როუტერების რაოდენობა',
                        'en' => 'Router count',
                        'ru' => 'Количество роутеров',
                        'placeholder_ka' => 'მაგ: 4',
                        'placeholder_en' => 'Example: 4',
                        'placeholder_ru' => 'Например: 4',
                    ],
                    [
                        'key' => 'switch_count',
                        'type' => 'number',
                        'required' => false,
                        'ka' => 'სვიჩების რაოდენობა',
                        'en' => 'Switch count',
                        'ru' => 'Количество свичей',
                        'placeholder_ka' => 'მაგ: 12',
                        'placeholder_en' => 'Example: 12',
                        'placeholder_ru' => 'Например: 12',
                    ],
                    [
                        'key' => 'access_point_count',
                        'type' => 'number',
                        'required' => false,
                        'ka' => 'Access Point-ების რაოდენობა',
                        'en' => 'Access point count',
                        'ru' => 'Количество точек доступа',
                        'placeholder_ka' => 'მაგ: 18',
                        'placeholder_en' => 'Example: 18',
                        'placeholder_ru' => 'Например: 18',
                    ],
                    [
                        'key' => 'backbone_type',
                        'type' => 'select',
                        'required' => false,
                        'ka' => 'Backbone ტიპი',
                        'en' => 'Backbone type',
                        'ru' => 'Тип backbone',
                        'options' => [
                            ['value' => 'copper', 'ka' => 'სპილენძი', 'en' => 'Copper', 'ru' => 'Медь'],
                            ['value' => 'fiber', 'ka' => 'ოპტიკა', 'en' => 'Fiber', 'ru' => 'Оптика'],
                            ['value' => 'hybrid', 'ka' => 'ჰიბრიდი', 'en' => 'Hybrid', 'ru' => 'Гибрид'],
                        ],
                    ],
                ],
            ],
            'server-infrastructure' => [
                'project_size_label_ka' => 'ინფრასტრუქტურის მასშტაბი',
                'project_size_label_en' => 'Infrastructure size',
                'project_size_label_ru' => 'Размер инфраструктуры',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა გარემო', 'en' => 'Small environment', 'ru' => 'Малая среда'],
                    ['value' => 'medium', 'ka' => 'საშუალო გარემო', 'en' => 'Medium environment', 'ru' => 'Средняя среда'],
                    ['value' => 'large', 'ka' => 'დიდი გარემო', 'en' => 'Large environment', 'ru' => 'Крупная среда'],
                ],
                'property_type_label_ka' => 'ინფრასტრუქტურის ტიპი',
                'property_type_label_en' => 'Infrastructure type',
                'property_type_label_ru' => 'Тип инфраструктуры',
                'property_type_options' => [
                    ['value' => 'onprem', 'ka' => 'ადგილობრივი', 'en' => 'On-premise', 'ru' => 'Локальная'],
                    ['value' => 'hybrid', 'ka' => 'ჰიბრიდული', 'en' => 'Hybrid', 'ru' => 'Гибридная'],
                    ['value' => 'datacenter', 'ka' => 'დატაცენტრი', 'en' => 'Data center', 'ru' => 'Дата-центр'],
                ],
                'extra_fields' => [
                    [
                        'key' => 'server_count',
                        'type' => 'number',
                        'required' => false,
                        'ka' => 'სერვერების რაოდენობა',
                        'en' => 'Server count',
                        'ru' => 'Количество серверов',
                        'placeholder_ka' => 'მაგ: 6',
                        'placeholder_en' => 'Example: 6',
                        'placeholder_ru' => 'Например: 6',
                    ],
                    [
                        'key' => 'storage_needed',
                        'type' => 'text',
                        'required' => false,
                        'ka' => 'საჭირო Storage',
                        'en' => 'Required storage',
                        'ru' => 'Требуемое хранилище',
                        'placeholder_ka' => 'მაგ: 40 TB',
                        'placeholder_en' => 'Example: 40 TB',
                        'placeholder_ru' => 'Например: 40 TB',
                    ],
                    [
                        'key' => 'backup_required',
                        'type' => 'select',
                        'required' => false,
                        'ka' => 'საჭიროა Backup?',
                        'en' => 'Backup required?',
                        'ru' => 'Нужен backup?',
                        'options' => [
                            ['value' => 'yes', 'ka' => 'დიახ', 'en' => 'Yes', 'ru' => 'Да'],
                            ['value' => 'no', 'ka' => 'არა', 'en' => 'No', 'ru' => 'Нет'],
                        ],
                    ],
                ],
            ],
        ];
    }
};
