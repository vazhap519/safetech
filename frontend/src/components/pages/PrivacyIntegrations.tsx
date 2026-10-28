import ConsentSettingsButton from "@/components/analytics/ConsentSettingsButton";
import type { Locale } from "@/lib/locales";
import { getSiteSettings } from "@/lib/site-settings";
import { resolveMarketingIds } from "@/lib/marketing-config";

const copy = {
    ka: {
        title: "საიტზე ჩართული ანალიტიკა და თქვენი არჩევანი",
        consent: "ქვემოთ ჩამოთვლილი დამატებითი ტექნოლოგიები იტვირთება მხოლოდ თქვენი თანხმობის შემდეგ. ფორმით დაკავშირების მოთხოვნა ანალიტიკაზე თანხმობის გარეშეც შეგიძლიათ. თანხმობის არჩევანი ინახება 180 დღემდე; მის შეცვლას შეძლებთ ქვემოთ მოცემული ღილაკით ან საიტის ბოლოში კონფიდენციალურობის პარამეტრებიდან.",
        ga: "Google Analytics 4 (Google): ზომავს გვერდების ნახვებს, კამპანიების წყაროს, მოწყობილობისა და ბრაუზერის ტექნიკურ მონაცემებს, საკონტაქტო ღილაკებზე დაჭერებსა და წარმატებით გაგზავნილ მოთხოვნებს. ანალიტიკის მოვლენებში ფორმის სახელი, ტელეფონი, ელფოსტა და შეტყობინება არ იგზავნება.",
        gtm: "Google Tag Manager (Google): მართავს საიტზე კონფიგურირებული ანალიტიკისა და მარკეტინგული ტეგების ჩატვირთვას.",
        meta: "Meta Pixel (Meta): ზომავს გვერდების ნახვებსა და მოთხოვნის/დაკავშირების მოქმედებებს რეკლამის შედეგების შესაფასებლად. Meta-მ შესაძლოა გამოიყენოს cookies და ბრაუზერის იდენტიფიკატორები თავისივე პოლიტიკის შესაბამისად.",
        top: "TOP.GE: ითვლის ვიზიტებს და ამუშავებს გვერდის, ბრაუზერისა და მოწყობილობის ტექნიკურ მონაცემებს საიტის სტატისტიკისთვის.",
        disabled: "ამჟამად დამატებითი ანალიტიკური და მარკეტინგული ინტეგრაციები გამორთულია.",
    },
    en: {
        title: "Active analytics and your choices",
        consent: "The optional technologies listed below load only after your consent. You can submit an enquiry without analytics consent. Your choice is stored for up to 180 days; change it using the button below or Privacy settings in the footer.",
        ga: "Google Analytics 4 (Google): measures page views, campaign sources, device/browser information, contact clicks and successfully submitted enquiries. Analytics events do not send the form's name, phone number, email address or message.",
        gtm: "Google Tag Manager (Google): manages loading of the analytics and marketing tags configured for this website.",
        meta: "Meta Pixel (Meta): measures page views and enquiry/contact actions to evaluate advertising. Meta may use cookies and browser identifiers under its own privacy policy.",
        top: "TOP.GE: counts visits and processes page, browser and device information for website statistics.",
        disabled: "Optional analytics and marketing integrations are currently disabled.",
    },
    ru: {
        title: "Активная аналитика и ваш выбор",
        consent: "Перечисленные дополнительные технологии загружаются только после согласия. Можно отправить заявку без согласия на аналитику. Выбор хранится до 180 дней; изменить его можно кнопкой ниже или через настройки конфиденциальности внизу сайта.",
        ga: "Google Analytics 4 (Google): измеряет просмотры, источники кампаний, сведения об устройстве и браузере, клики для связи и успешно отправленные заявки. Имя, телефон, email и текст из формы не передаются в событиях аналитики.",
        gtm: "Google Tag Manager (Google): управляет загрузкой настроенных для сайта тегов аналитики и маркетинга.",
        meta: "Meta Pixel (Meta): измеряет просмотры и действия для заявки/связи, чтобы оценивать рекламу. Meta может использовать cookies и идентификаторы браузера в соответствии со своей политикой.",
        top: "TOP.GE: подсчитывает посещения и обрабатывает сведения о странице, браузере и устройстве для статистики сайта.",
        disabled: "Дополнительные интеграции аналитики и маркетинга сейчас отключены.",
    },
};

export default async function PrivacyIntegrations({ locale }: { locale: Locale }) {
    const { integrations } = await getSiteSettings();
    const text = copy[locale];
    const enabled = integrations.marketingEnabled;
    const { googleAnalyticsId: ga, googleTagManagerId: gtm, metaPixelId: pixel } = resolveMarketingIds(integrations);
    const top = /counter\.top\.ge/i.test(integrations.footerCounterCode);

    return (
        <section className="mt-9" aria-label={text.title}>
            <h2 className="mb-4 text-2xl font-bold text-on-surface">{text.title}</h2>
            {enabled ? <>
                <p className="my-5">{text.consent}</p>
                <ul className="my-5 list-disc space-y-3 pl-6">
                    {ga ? <li>{text.ga}</li> : null}
                    {gtm ? <li>{text.gtm}</li> : null}
                    {pixel ? <li>{text.meta}</li> : null}
                    {top ? <li>{text.top}</li> : null}
                </ul>
                <ConsentSettingsButton enabled />
            </> : <p>{text.disabled}</p>}
        </section>
    );
}
