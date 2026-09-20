<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Backfill missing EN/RU editorial fields for existing canonical local pages.
 * Preserve every administrator-authored translation and Georgian body. Do not
 * create more city-keyword permutations or touch publication/indexing states.
 */
final class ExistingLocalLandingTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LocalServiceLanding::query()
            ->with('service')
            ->whereIn('location_slug', array_keys($this->cities()))
            ->get() as $landing) {
            $service = $landing->service;
            $city = $this->cities()[$landing->location_slug];
            $guidance = $this->guidance()[$service?->slug] ?? null;

            if (! $service || ! $guidance) {
                continue;
            }

            $translations = is_array($landing->translations) ? $landing->translations : [];
            $updated = false;

            foreach (['en', 'ru'] as $locale) {
                $name = trim((string) data_get($service->translations, "fields.name.{$locale}"));
                $description = trim((string) data_get($service->translations, "fields.description.{$locale}"));

                if ($name === '' || $description === '') {
                    continue;
                }

                $where = $city[$locale];
                $h1 = $locale === 'en' ? "{$name} in {$where}" : "{$name} в {$where}";
                $intro = $locale === 'en'
                    ? "SafeTech provides {$name} for homes and businesses in {$where}. {$description}"
                    : "SafeTech предлагает {$name} для частных клиентов и бизнеса в {$where}. {$description}";
                $text = implode("\n\n", [
                    $intro,
                    $guidance[$locale],
                    $city[$locale.'_context'],
                    $locale === 'en'
                        ? 'Tell us the object type, equipment models, site address and your main requirement. We confirm the technical scope and quote before installation, without assuming that every device supports every feature.'
                        : 'Укажите тип объекта, модели оборудования, адрес и задачу. До монтажа согласуем технический объём и стоимость; совместимость функций проверяется отдельно.',
                ]);
                $seoDescription = $locale === 'en'
                    ? "{$name} in {$where}. {$guidance['en_seo']}"
                    : "{$name} в {$where}. {$guidance['ru_seo']}";
                $copy = [
                    'locationName' => $where,
                    'title' => $h1,
                    'excerpt' => $intro,
                    'content' => $text,
                    'seoTitle' => "{$h1} | SafeTech",
                    'seoDescription' => $seoDescription,
                    'ogTitle' => "{$h1} | SafeTech",
                    'ogDescription' => $seoDescription,
                    'ctaTitle' => $locale === 'en' ? "Request {$name} in {$where}" : "Заказать {$name} в {$where}",
                    'ctaText' => $locale === 'en'
                        ? 'Describe your property, equipment and preferred schedule so we can propose a suitable service plan.'
                        : 'Опишите объект, оборудование и удобное время, чтобы мы могли предложить подходящий план работ.',
                    'primaryKeyword' => $h1,
                ];

                foreach ($copy as $field => $value) {
                    $path = "fields.{$field}.{$locale}";

                    // Only genuinely absent keys; an explicit blank can be an
                    // intentional CMS/editorial state and must not be overwritten.
                    if (! Arr::has($translations, $path)) {
                        Arr::set($translations, $path, $value);
                        $updated = true;
                    }
                }

                if (! Arr::has($translations, "keywords.{$locale}")) {
                    Arr::set($translations, "keywords.{$locale}", [$h1, "{$name} {$where}"]);
                    $updated = true;
                }
            }

            if ($updated) {
                $landing->translations = $translations;
                $landing->save();
            }
        }
    }

    private function cities(): array
    {
        return [
            'tbilisi' => [
                'en' => 'Tbilisi', 'ru' => 'Тбилиси',
                'en_context' => 'For city apartments and offices, share the building access rules, usable cable routes and the possibility of an agreed technician visit. Travel and any disruptive work are confirmed before scheduling.',
                'ru_context' => 'Для городских квартир и офисов уточните доступ в здание, кабельные трассы и возможность согласованного визита техника. Выезд и работы с отключением обсуждаем до назначения времени.',
            ],
            'khashuri' => [
                'en' => 'Khashuri', 'ru' => 'Хашури',
                'en_context' => 'In Khashuri, an existing home or shop may already have wiring or devices worth reusing. Send a picture of the existing connections and the site address to decide whether the system needs maintenance or new equipment.',
                'ru_context' => 'В Хашури в доме или магазине могут уже быть кабели и устройства, которые можно сохранить. Пришлите фото подключений и адрес, чтобы определить необходимость ремонта или новых компонентов.',
            ],
            'bakuriani' => [
                'en' => 'Bakuriani', 'ru' => 'Бакуриани',
                'en_context' => 'For a Bakuriani cottage or hospitality property, specify seasonal access, guest turnover, outdoor equipment exposure and who will maintain the system while the owner is away. Equipment and travel are scoped accordingly.',
                'ru_context' => 'Для коттеджа или гостиницы в Бакуриани уточните сезонный доступ, смену гостей, условия внешнего размещения и кто обслуживает систему в отсутствие владельца. Состав оборудования и выезд согласуются отдельно.',
            ],
            'borjomi' => [
                'en' => 'Borjomi', 'ru' => 'Боржоми',
                'en_context' => 'For Borjomi guesthouses, shops and homes, note the working hours, occupied rooms and permitted maintenance windows. Access and cable routes should be planned without unnecessary disruption to guests or staff.',
                'ru_context' => 'Для гостевых домов, магазинов и жилья в Боржоми уточните рабочие часы, занятые помещения и окно обслуживания. Доступ и прокладку кабелей планируем с учётом гостей и персонала.',
            ],
            'surami' => [
                'en' => 'Surami', 'ru' => 'Сурами',
                'en_context' => 'For a Surami residence, holiday property or small business, describe existing equipment, utility access and whether you need remote support after the technician leaves. Any visit and materials are quoted in advance.',
                'ru_context' => 'Для дома, дачи или малого бизнеса в Сурами опишите оборудование, доступ к коммуникациям и потребность в удалённой поддержке после отъезда специалиста. Выезд и материалы согласуются заранее.',
            ],
        ];
    }

    private function guidance(): array
    {
        return [
            'security-camera-installation' => [
                'en' => 'Camera positions are reviewed for entrances, lighting and privacy. The NVR/DVR, storage bitrate and retention target, PoE cabling and backup power are selected for the actual property. Remote viewing depends on network availability; local recording can continue with suitable power and recorder.',
                'ru' => 'Позиции камер оцениваются с учётом входов, освещения и приватности. NVR/DVR, битрейт и срок архива, PoE-кабели и резервное питание подбираются под объект. Удалённый просмотр зависит от сети; локальная запись может продолжаться при исправном регистраторе и питании.',
                'en_seo' => 'IP/PoE CCTV, NVR recording, HDD planning and remote viewing matched to your property.',
                'ru_seo' => 'IP/PoE камеры, NVR, расчёт архива и удалённый просмотр под задачи объекта.',
            ],
            'network-cable-installation' => [
                'en' => 'CAT5e/CAT6 runs are planned around distance, separation from mains wiring and the number of network ports. We identify both cable ends, terminate compatible connectors and check the installed links. Switch ports and PoE load are estimated separately where cameras or wireless access points are involved.',
                'ru' => 'Трассы CAT5e/CAT6 проектируются с учётом длины, расстояния от силовых линий и количества портов. Маркируем концы, устанавливаем совместимые разъёмы и проверяем линии. Порты коммутатора и PoE-нагрузка рассчитываются отдельно при подключении камер и точек Wi-Fi.',
                'en_seo' => 'CAT6 LAN cabling, RJ45 outlets, labelled runs and line testing for homes and business.',
                'ru_seo' => 'Прокладка CAT6, RJ45-розетки, маркировка и тест линий для дома и бизнеса.',
            ],
            'router-wifi-configuration' => [
                'en' => 'Router and access point positions depend on floor layout, wall materials and radio interference. We review provider WAN settings, DHCP and Wi-Fi encryption; MikroTik VLAN, VPN and firewall requirements are configured only if the network design needs them. Coverage is checked on the premises.',
                'ru' => 'Расположение роутера и точек доступа зависит от планировки, стен и радиопомех. Проверяем WAN провайдера, DHCP и защиту Wi-Fi; VLAN, VPN и firewall MikroTik настраиваем по требованиям сети. Покрытие проверяется на объекте.',
                'en_seo' => 'MikroTik, routers, wireless access points, VLAN/VPN and Wi-Fi coverage assessment.',
                'ru_seo' => 'MikroTik, роутеры, точки доступа, VLAN/VPN и оценка покрытия Wi-Fi.',
            ],
            'business-it-support' => [
                'en' => 'We assess the number of workstations, printer models, OS versions, network access and how urgently the business needs support. Some driver and software tasks can be handled remotely; cabling, hardware failures and physical replacements require an arranged visit.',
                'ru' => 'Уточняем число рабочих мест, модели принтеров, версии ОС, сеть и срочность поддержки. Настройка драйверов и некоторых программ возможна удалённо; кабели и аппаратные неисправности требуют согласованного визита.',
                'en_seo' => 'Business PC, printer and LAN troubleshooting with onsite or remote IT support.',
                'ru_seo' => 'Поддержка ПК, принтеров и сети бизнеса удалённо или с выездом.',
            ],
            'barrier-gate-installation' => [
                'en' => 'Barrier design starts with entry width, traffic and safe exit paths. Photocells and loop detectors serve different detection roles. Remote control, GSM and licence plate recognition (LPR) require compatible controllers and cameras; fail-safe behavior is checked before activation.',
                'ru' => 'Проект шлагбаума начинается с ширины проезда, потока машин и безопасного выезда. Фотоэлементы и индукционные петли решают разные задачи. Пульт, GSM и распознавание номеров (LPR) требуют совместимых контроллеров и камер.',
                'en_seo' => 'Barrier gates, safety sensors, remote controls, GSM and compatible LPR access.',
                'ru_seo' => 'Шлагбаумы, датчики безопасности, пульты, GSM и совместимый LPR-доступ.',
            ],
            'intercom-access-control-installation' => [
                'en' => 'We check door type, lock power requirements, fire egress, controller compatibility and cable routes before selecting a video intercom or RFID/PIN reader. Exit buttons, backup supply and phone unlocking depend on the specific model and wiring. Shared building access requires authorization from the relevant residents or manager.',
                'ru' => 'Проверяем дверь, питание замка, безопасный выход, совместимость контроллера и кабели перед выбором видеодомофона или RFID/PIN. Кнопка выхода, резервное питание и открытие с телефона зависят от модели и проводки. Для общего подъезда требуется согласование.',
                'en_seo' => 'Video intercoms, RFID/PIN readers, locks, exit buttons and access controllers.',
                'ru_seo' => 'Видеодомофоны, RFID/PIN, замки, кнопки выхода и контроллеры доступа.',
            ],
        ];
    }
}
