<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Translate known canonical Georgian blocks exactly; do not guess translations
 * for changed CMS questions or claim unverified project details.
 */
final class LegacyLocalLandingItemsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LocalServiceLanding::query()
            ->whereIn('location_slug', ['tbilisi', 'khashuri', 'bakuriani', 'borjomi', 'surami'])
            ->get() as $landing) {
            $dirty = false;
            $benefits = $landing->benefits ?? [];
            $faq = $landing->faq ?? [];

            foreach ($benefits as &$item) {
                $localized = $this->benefits()[$item['title'] ?? ''] ?? null;

                if ($localized) {
                    $dirty = $this->addMissing($item, $localized, ['title', 'description']) || $dirty;
                }
            }
            unset($item);

            foreach ($faq as &$item) {
                $georgian = $item['question'] ?? '';
                $localized = $this->faqs()[$georgian] ?? null;

                if (! $localized && str_starts_with($georgian, 'რა ღირს კამერების მონტაჟი ')) {
                    $city = ['tbilisi' => ['Tbilisi', 'Тбилиси'], 'khashuri' => ['Khashuri', 'Хашури'],
                        'bakuriani' => ['Bakuriani', 'Бакуриани'], 'borjomi' => ['Borjomi', 'Боржоми'],
                        'surami' => ['Surami', 'Сурами']][$landing->location_slug] ?? null;

                    if ($city) {
                        $localized = [
                            'en' => ['question' => "How much does CCTV installation cost in {$city[0]}?",
                                'answer' => 'Pricing depends on camera count, wiring, recorder, storage, access and installation complexity. Share your property details for a quote.'],
                            'ru' => ['question' => "Сколько стоит монтаж камер в {$city[1]}?",
                                'answer' => 'Цена зависит от количества камер, кабелей, регистратора, архива и сложности монтажа. Пришлите параметры объекта для расчёта.'],
                        ];
                    }
                }

                if ($localized) {
                    $dirty = $this->addMissing($item, $localized, ['question', 'answer']) || $dirty;
                }
            }
            unset($item);

            if ($dirty) {
                $landing->benefits = $benefits;
                $landing->faq = $faq;
                $landing->save();
            }
        }
    }

    private function addMissing(array &$item, array $localized, array $fields): bool
    {
        $changed = false;

        foreach (['en', 'ru'] as $locale) {
            foreach ($fields as $field) {
                $path = "translations.{$locale}.{$field}";

                if (! Arr::has($item, $path)) {
                    Arr::set($item, $path, $localized[$locale][$field]);
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    /** @return array<string, array<string, array<string, string>>> */
    private function benefits(): array
    {
        return [
            'სწორი ხედვის წერტილები' => $this->benefit('Planned camera viewpoints', 'Positions consider actual risk and required visibility.', 'Планирование обзора', 'Позиции учитывают риски объекта и нужные зоны обзора.'),
            '24/7 ჩანაწერი' => $this->benefit('Recording retention', 'NVR/DVR and HDD capacity are planned around retention requirements.', 'Архив записей', 'NVR/DVR и HDD подбираются под нужный срок хранения.'),
            'მობილურიდან კონტროლი' => $this->benefit('Mobile viewing', 'Live and recorded video can be accessed through compatible, secured apps.', 'Просмотр с телефона', 'Живое видео и архив доступны в совместимом защищённом приложении.'),
            'სწორი მარშრუტი' => $this->benefit('Planned cable routes', 'Distance, mains separation and future expansion are considered.', 'Планирование трасс', 'Учитываем расстояния, силовые линии и расширение сети.'),
            'მარკირება და ტესტირება' => $this->benefit('Labelling and tests', 'Both ends are labelled and the installed link is tested.', 'Маркировка и тест', 'Маркируем оба конца и проверяем готовую линию.'),
            'LAN ინფრასტრუქტურა' => $this->benefit('LAN infrastructure', 'Designed for cameras, computers, POS and wireless access points.', 'Сетевая инфраструктура', 'Подключаем камеры, ПК, POS и точки доступа Wi-Fi.'),
            'სტაბილური Wi‑Fi' => $this->benefit('Reliable Wi-Fi', 'Access points are positioned for walls, floors and usable coverage.', 'Стабильный Wi-Fi', 'Размещение точек зависит от стен, этажей и зоны покрытия.'),
            'უსაფრთხო კონფიგურაცია' => $this->benefit('Protected configuration', 'Admin access, Wi-Fi encryption, DHCP and firewall rules are reviewed.', 'Безопасная настройка', 'Проверяем доступ администратора, защиту Wi-Fi, DHCP и firewall.'),
            'MikroTik და ბიზნეს ქსელი' => $this->benefit('MikroTik and business networks', 'VLAN, VPN and port forwarding are configured when required.', 'MikroTik и сеть бизнеса', 'Настраиваем VLAN, VPN и проброс портов по требованиям.'),
            'ერთი IT პარტნიორი' => $this->benefit('One technical contact', 'Support for computers, printers, networks and day-to-day IT tasks.', 'Единый IT-специалист', 'Поддержка ПК, принтеров, сети и повседневных IT-задач.'),
            'დისტანციური და ადგილზე დახმარება' => $this->benefit('Remote or onsite support', 'The fault determines whether remote assistance or a visit is needed.', 'Удалённо или с выездом', 'Тип неисправности определяет способ поддержки.'),
            'ერთჯერადი ან აბონენტური მომსახურება' => $this->benefit('Flexible support scope', 'Single-issue work or a separately agreed maintenance plan.', 'Разовый или регулярный сервис', 'Разовая помощь или согласованное регулярное обслуживание.'),
            'უსაფრთხო ავტომატიკა' => $this->benefit('Safety sensors', 'Photocells and/or loops detect obstacles and vehicles as specified.', 'Датчики безопасности', 'Фотоэлементы и петли выявляют препятствия и автомобили.'),
            'LPR ნომრის ამოცნობა' => $this->benefit('Licence plate recognition', 'Compatible LPR cameras can trigger entry for approved plates.', 'Распознавание номеров LPR', 'Совместимая камера может открывать въезд для разрешённых номеров.'),
            'პულტი, GSM ან Access Control' => $this->benefit('Remote, GSM or access control', 'Opening method follows site and controller compatibility.', 'Пульт, GSM или СКУД', 'Способ открытия выбирается под объект и совместимость контроллера.'),
            'კარის უსაფრთხო მართვა' => $this->benefit('Safe door control', 'Choose the lock and controller for the door and egress requirements.', 'Безопасное управление дверью', 'Замок и контроллер подбираются с учётом двери и выхода.'),
            'ბარათი, PIN და ბიომეტრია' => $this->benefit('Cards, PIN and biometrics', 'Compatible controllers can combine multiple access methods.', 'Карты, PIN и биометрия', 'Совместимые контроллеры объединяют разные способы доступа.'),
            'დომოფონი და მობილური აპი' => $this->benefit('Intercom and mobile app', 'Compatible IP intercoms can accept calls and unlock remotely.', 'Домофон и приложение', 'Совместимые IP-домофоны принимают вызовы и открывают двери удалённо.'),
        ];
    }

    /** @return array<string, array<string, array<string, string>>> */
    private function faqs(): array
    {
        return [
            'ტელეფონიდან კამერების ნახვა შეიძლება?' => $this->faq('Can I view cameras on my phone?', 'Yes, after compatible remote-view setup and secure user access.', 'Можно смотреть камеры с телефона?', 'Да, при наличии совместимой системы и настроенного безопасного доступа.'),
            'ინტერნეტი თუ გაითიშა, კამერები ჩაიწერს?' => $this->faq('Will recording continue without internet?', 'Local NVR/DVR recording can continue if the recorder and cameras have power; remote viewing will pause.', 'Будет ли запись без интернета?', 'Локальная запись NVR/DVR продолжается при наличии питания; удалённый просмотр временно недоступен.'),
            'ღამით ფერადი გამოსახულება შესაძლებელია?' => $this->faq('Is colour night video possible?', 'Full Colour camera performance depends on model and ambient lighting.', 'Возможно цветное ночное видео?', 'Возможности Full Colour зависят от модели и доступного освещения.'),
            'CAT6 კაბელის გაყვანის ფასი როგორ ითვლება?' => $this->faq('How is CAT6 installation priced?', 'By route length, cable channel, number of RJ45 outlets and site conditions.', 'Как рассчитывается цена прокладки CAT6?', 'По длине трассы, кабель-каналу, числу розеток RJ45 и условиям объекта.'),
            'დამონტაჟებული ხაზები იტესტება?' => $this->faq('Are installed LAN lines tested?', 'Yes, continuity and wiring are checked; specialist certification is agreed separately.', 'Проверяете смонтированные линии?', 'Да, проверяем целостность и распиновку; сертификация отдельно.'),
            'რემონტამდე ქსელის დაგეგმვაც შეგიძლიათ?' => $this->faq('Can LAN cabling be planned before renovation?', 'Yes, cable routes and outlets can be agreed before walls and ceilings are closed.', 'Можно спланировать сеть до ремонта?', 'Да, трассы и розетки можно согласовать до отделки стен и потолка.'),
            'რატომ არის Wi‑Fi სუსტი ზოგ ოთახში?' => $this->faq('Why is Wi-Fi weak in some rooms?', 'Walls, floors, placement and radio interference can reduce coverage; an access point or mesh may help.', 'Почему Wi-Fi плохо ловит в комнатах?', 'Мешают стены, этажи, расположение и помехи; иногда нужна точка доступа или mesh.'),
            'MikroTik-ის სრულ კონფიგურაციას აკეთებთ?' => $this->faq('Do you configure MikroTik?', 'WAN, DHCP, NAT, firewall and needed VLAN/VPN features are agreed for the network.', 'Настраиваете MikroTik?', 'WAN, DHCP, NAT, firewall, а также VLAN/VPN согласуются под задачи сети.'),
            'პროვაიდერის შეცვლის შემდეგ ქსელს თავიდან გამართავთ?' => $this->faq('Can you reconfigure the network after changing ISP?', 'Yes, we review WAN, local addressing and dependent business devices.', 'Настроите сеть после смены провайдера?', 'Да, проверяем WAN, локальные адреса и зависимые устройства бизнеса.'),
            'IT მხარდაჭერა მხოლოდ კომპანიებისთვისაა?' => $this->faq('Is IT support only for businesses?', 'Business support is the main scope; other one-off tasks can be discussed.', 'IT-поддержка только для компаний?', 'Основной фокус — бизнес; разовые задачи можно обсудить отдельно.'),
            'აბონენტური IT მომსახურება შესაძლებელია?' => $this->faq('Is recurring IT maintenance available?', 'A separate plan can be agreed based on users, equipment and response needs.', 'Есть абонентское IT-обслуживание?', 'План согласуется по числу пользователей, оборудования и времени реакции.'),
            'დისტანციურად რა პრობლემებს აგვარებთ?' => $this->faq('Which issues can be fixed remotely?', 'Some Windows, software, printer and network configuration issues; hardware work needs a visit.', 'Какие задачи решаете удалённо?', 'Часть проблем Windows, ПО, принтеров и сети; аппаратный ремонт требует выезда.'),
            'შლაგბაუმი ნომრის ამოცნობით ავტომატურად გაიღება?' => $this->faq('Can an LPR camera open a barrier automatically?', 'Yes, with a compatible LPR/controller setup and approved plate list.', 'Шлагбаум откроется по номеру?', 'Да, с совместимой камерой LPR, контроллером и списком разрешённых номеров.'),
            'Loop Detector და Photocell ორივე საჭიროა?' => $this->faq('Are both loop detector and photocell needed?', 'They have different functions; sensor selection follows lane geometry and safety needs.', 'Нужны и петля, и фотоэлемент?', 'У них разные задачи; состав датчиков зависит от проезда и безопасности.'),
            'ტელეფონით გახსნა შეიძლება?' => $this->faq('Can the barrier open by phone?', 'Depending on compatibility, GSM, an app or access controller can be used.', 'Можно открыть с телефона?', 'В зависимости от совместимости — через GSM, приложение или контроллер.'),
            'Fail-safe და fail-secure საკეტს შორის რა განსხვავებაა?' => $this->faq('What is fail-safe versus fail-secure?', 'Fail-safe unlocks when power is lost; fail-secure stays locked. Egress and fire rules matter.', 'Чем различаются fail-safe и fail-secure?', 'Fail-safe отпирается без питания, fail-secure остаётся закрытым; важны правила выхода и пожарной безопасности.'),
            'დენის გათიშვისას სისტემა იმუშავებს?' => $this->faq('Will access control work during a power outage?', 'Backup supply can keep the controller, lock and required network running for a planned period.', 'Будет ли СКУД работать без электричества?', 'Резервное питание позволит работать контроллеру, замку и необходимой сети ограниченное время.'),
            'ბარათით და PIN-ით ერთდროულად შეიძლება დაშვება?' => $this->faq('Can card and PIN access be combined?', 'Yes, if the chosen controller supports the desired card, PIN or biometric combinations.', 'Можно совместить карту и PIN?', 'Да, если контроллер поддерживает нужные сочетания карты, PIN или биометрии.'),
        ];
    }

    private function benefit(string $enTitle, string $enDescription, string $ruTitle, string $ruDescription): array
    {
        return [
            'en' => ['title' => $enTitle, 'description' => $enDescription],
            'ru' => ['title' => $ruTitle, 'description' => $ruDescription],
        ];
    }

    private function faq(string $enQuestion, string $enAnswer, string $ruQuestion, string $ruAnswer): array
    {
        return [
            'en' => ['question' => $enQuestion, 'answer' => $enAnswer],
            'ru' => ['question' => $ruQuestion, 'answer' => $ruAnswer],
        ];
    }
}
