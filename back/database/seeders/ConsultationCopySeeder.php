<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Illuminate\Database\Seeder;

final class ConsultationCopySeeder extends Seeder
{
    public function run(): void
    {
        $setting = SiteSetting::query()->where('key', 'translations')->first();

        if (! $setting) {
            return;
        }

        $value = is_array($setting->value) ? $setting->value : [];
        $map = MultilingualContent::mapFrom($value);

        foreach ($this->definitions() as $key => $locales) {
            $map[$key] ??= ['ka' => '', 'en' => '', 'ru' => ''];

            foreach ($locales as $locale => $definition) {
                $current = trim((string) ($map[$key][$locale] ?? ''));

                if ($current === '' || in_array($current, $definition['replace'], true)) {
                    $map[$key][$locale] = $definition['value'];
                }
            }
        }

        $value['entries'] = MultilingualContent::entriesFromMap($map);
        $setting->value = $value;
        $setting->save();
    }

    /** @return array<string, array<string, array{value: string, replace: array<int, string>}>> */
    private function definitions(): array
    {
        return [
            'forms.details' => [
                'ka' => $this->copy('ამოცანის დეტალები', ['მოთხოვნის დეტალები']),
                'en' => $this->copy('Project details', ['Project details']),
                'ru' => $this->copy('Детали задачи', ['Детали задачи']),
            ],
            'consultation.form.address' => [
                'ka' => $this->copy('ქალაქი / მომსახურების მისამართი', []),
                'en' => $this->copy('City / service address', []),
                'ru' => $this->copy('Город / адрес оказания услуги', []),
            ],
            'consultation.form.requiredHint' => [
                'ka' => $this->copy('ყველა ველი სავალდებულოა.', []),
                'en' => $this->copy('All fields are required.', []),
                'ru' => $this->copy('Все поля обязательны.', []),
            ],
            'consultation.form.submit' => [
                'ka' => $this->copy('კონსულტაციის მიღება', ['მოთხოვნის გაგზავნა', 'კონსულტაციის მოთხოვნა']),
                'en' => $this->copy('Get consultation', ['Send request', 'Request consultation', 'Submit a request']),
                'ru' => $this->copy('Получить консультацию', ['Отправить запрос', 'Запросить консультацию', 'Заполнить заявку']),
            ],
            'consultation.form.validation' => [
                'ka' => $this->copy('შეავსეთ ყველა სავალდებულო ველი და დაეთანხმეთ მონაცემების დამუშავებას.', []),
                'en' => $this->copy('Complete every required field and accept data processing.', []),
                'ru' => $this->copy('Заполните все обязательные поля и подтвердите согласие на обработку данных.', []),
            ],
            'forms.submitRequest' => [
                'ka' => $this->copy('კონსულტაციის მოთხოვნა', ['მოთხოვნის გაგზავნა']),
                'en' => $this->copy('Request consultation', ['Send request']),
                'ru' => $this->copy('Запросить консультацию', ['Отправить запрос']),
            ],
            'forms.send' => [
                'ka' => $this->copy('კონსულტაციის მოთხოვნა', ['მოთხოვნის გაგზავნა']),
                'en' => $this->copy('Request consultation', ['Send request']),
                'ru' => $this->copy('Запросить консультацию', ['Отправить запрос']),
            ],
            'forms.privacy' => [
                'ka' => $this->copy('ვეთანხმები ჩემი საკონტაქტო მონაცემების გამოყენებას კონსულტაციასთან დაკავშირებით დასაკავშირებლად.', ['ვეთანხმები ჩემი საკონტაქტო მონაცემების გამოყენებას მოთხოვნაზე პასუხისთვის.']),
                'en' => $this->copy('I agree to the use of my contact details so SafeTech can contact me about this consultation.', ['I agree to the use of my contact details to respond to this request.']),
                'ru' => $this->copy('Я согласен на использование контактных данных, чтобы SafeTech мог связаться со мной по поводу консультации.', ['Я согласен на использование контактных данных для ответа на запрос.']),
            ],
            'forms.validation.contact' => [
                'ka' => $this->copy('მიუთითეთ ტელეფონი და ელფოსტა.', ['მიუთითეთ ტელეფონი ან ელფოსტა.']),
                'en' => $this->copy('Provide both a phone number and email.', ['Provide a phone number or email.']),
                'ru' => $this->copy('Укажите телефон и электронную почту.', ['Укажите телефон или email.']),
            ],
            'forms.error.submit' => [
                'ka' => $this->copy('ფორმის გაგზავნა ვერ მოხერხდა. სცადეთ თავიდან.', ['მოთხოვნის გაგზავნა ვერ მოხერხდა. სცადეთ თავიდან.']),
                'en' => $this->copy('The consultation form could not be sent. Please try again.', ['The request could not be sent. Please try again.']),
                'ru' => $this->copy('Не удалось отправить форму консультации. Попробуйте еще раз.', ['Не удалось отправить запрос. Попробуйте еще раз.']),
            ],
            'forms.success.submit' => [
                'ka' => $this->copy('ინფორმაცია მიღებულია. მალე დაგიკავშირდებით.', ['მოთხოვნა მიღებულია. მალე დაგიკავშირდებით.']),
                'en' => $this->copy('Your information has been received. We will contact you shortly.', ['Your request has been received. We will contact you shortly.']),
                'ru' => $this->copy('Информация получена. Мы скоро свяжемся с вами.', ['Запрос получен. Мы скоро свяжемся с вами.']),
            ],
            'consultation.modal.description' => [
                'ka' => $this->copy('მოკლედ აღწერეთ ამოცანა და მიუთითეთ საკონტაქტო ინფორმაცია. შევაფასებთ დეტალებს და შემოგთავაზებთ შესაბამის გადაწყვეტას.', ['მიუთითეთ მოთხოვნა და საკონტაქტო ინფორმაცია. შევაფასებთ დავალებას და შემოგთავაზებთ შესაბამის გადაწყვეტას.']),
                'en' => $this->copy('Briefly describe the task and provide your contact details. We will assess the details and propose a suitable solution.', ['Share your requirements and contact details. We will assess the task and propose a suitable solution.']),
                'ru' => $this->copy('Кратко опишите задачу и укажите контактные данные. Мы оценим детали и предложим подходящее решение.', ['Укажите требования и контактные данные. Мы оценим задачу и предложим подходящее решение.']),
            ],
            'consultation.cta.note' => [
                'ka' => $this->copy('კონსულტაციის ფორმა ერთ ფანჯარაში გაიხსნება. შეავსეთ ყველა სავალდებულო ველი და ჩვენ დაგიკავშირდებით.', []),
                'en' => $this->copy('The consultation form opens in one window. Complete all required fields and we will contact you.', []),
                'ru' => $this->copy('Форма консультации откроется в одном окне. Заполните все обязательные поля, и мы свяжемся с вами.', []),
            ],
            'home.cta.submit' => [
                'ka' => $this->copy('კონსულტაციის მიღება', ['მოთხოვნის გაგზავნა', 'კონსულტაციის მოთხოვნა']),
                'en' => $this->copy('Get consultation', ['Send request', 'Request consultation']),
                'ru' => $this->copy('Получить консультацию', ['Отправить запрос', 'Запросить консультацию']),
            ],
            'about.cta.description' => [
                'ka' => $this->copy('მოგვიყევით თქვენი ამოცანის შესახებ და მიიღეთ ობიექტისთვის შესაბამისი ტექნიკური გეგმა.', ['მოგვწერეთ მოთხოვნა და მიიღეთ თქვენი ობიექტისთვის შესაბამისი გეგმა.']),
                'en' => $this->copy('Tell us about your project and receive a technical plan tailored to your property.', ['Send your requirements and receive a plan tailored to your property.']),
                'ru' => $this->copy('Расскажите о своей задаче и получите технический план, адаптированный под ваш объект.', ['Отправьте требования и получите план, адаптированный под ваш объект.']),
            ],
            'about.cta.button' => [
                'ka' => $this->copy('კონსულტაციის მიღება', ['კონსულტაციის მოთხოვნა']),
                'en' => $this->copy('Get consultation', ['Request consultation']),
                'ru' => $this->copy('Получить консультацию', ['Запросить консультацию']),
            ],
            'contact.form.title' => [
                'ka' => $this->copy('მიიღეთ ტექნიკური კონსულტაცია', ['მოგვწერეთ მოთხოვნა']),
                'en' => $this->copy('Get a technical consultation', ['Send your request']),
                'ru' => $this->copy('Получите техническую консультацию', ['Отправьте запрос']),
            ],
            'contact.hero.button' => [
                'ka' => $this->copy('კონსულტაციის მიღება', ['მოთხოვნის შევსება', 'მოთხოვნის გაგზავნა', 'კონსულტაციის მოთხოვნა']),
                'en' => $this->copy('Get consultation', ['Submit a request', 'Send request', 'Request consultation']),
                'ru' => $this->copy('Получить консультацию', ['Заполнить заявку', 'Отправить запрос', 'Запросить консультацию']),
            ],
            'contact.final.button' => [
                'ka' => $this->copy('კონსულტაციის მიღება', ['მოთხოვნის გაგზავნა', 'კონსულტაციის მოთხოვნა']),
                'en' => $this->copy('Get consultation', ['Send request', 'Request consultation']),
                'ru' => $this->copy('Получить консультацию', ['Отправить запрос', 'Запросить консультацию']),
            ],
            'contact.side.title' => [
                'ka' => $this->copy('შემდეგი ნაბიჯები', ['რა მოხდება მოთხოვნის შემდეგ']),
                'en' => $this->copy('Next steps', ['What happens next']),
                'ru' => $this->copy('Следующие шаги', ['Что произойдет после запроса']),
            ],
            'contact.intro.badge.1' => [
                'ka' => $this->copy('თქვენზე მორგებული გეგმა', ['მოთხოვნაზე მორგებული გეგმა']),
                'en' => $this->copy('Tailored plan', ['Tailored plan']),
                'ru' => $this->copy('План под ваши задачи', ['План под требования']),
            ],
        ];
    }

    /** @param array<int, string> $replace
     * @return array{value: string, replace: array<int, string>}
     */
    private function copy(string $value, array $replace): array
    {
        return compact('value', 'replace');
    }
}
