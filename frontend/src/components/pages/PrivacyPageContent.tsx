import { getSiteSettings } from "@/lib/site-settings";
import { translateText, type TranslationFallback } from "@/lib/translations";

const sections: Array<{
    key: string;
    title: TranslationFallback;
    body: TranslationFallback;
}> = [
    {
        key: "collection",
        title: {
            ka: "რა მონაცემებს ვაგროვებთ",
            en: "Information we collect",
            ru: "Какие данные мы собираем",
        },
        body: {
            ka: "შესაძლოა მივიღოთ თქვენი სახელი, ტელეფონის ნომერი, ელფოსტა, შეტყობინების ტექსტი, შერჩეული სერვისი და ტექნიკური მოთხოვნის დეტალები. საიტის უსაფრთხოებისა და ანალიტიკისთვის შეიძლება დამუშავდეს მოწყობილობის, ბრაუზერისა და გვერდის გამოყენების ტექნიკური მონაცემებიც.",
            en: "We may receive your name, phone number, email, message, selected service, and technical request details. Device, browser, and page usage data may also be processed for security and analytics.",
            ru: "Мы можем получать ваше имя, телефон, email, текст сообщения, выбранную услугу и детали технического запроса. Для безопасности и аналитики также могут обрабатываться технические данные устройства, браузера и использования страниц.",
        },
    },
    {
        key: "use",
        title: {
            ka: "როგორ ვიყენებთ მონაცემებს",
            en: "How we use information",
            ru: "Как мы используем данные",
        },
        body: {
            ka: "მონაცემებს ვიყენებთ მოთხოვნაზე პასუხისთვის, კონსულტაციისა და შეთავაზების მოსამზადებლად, მომსახურების გასაწევად, ხარისხისა და უსაფრთხოების გასაუმჯობესებლად და კანონით გათვალისწინებული ვალდებულებების შესასრულებლად.",
            en: "We use information to answer requests, prepare consultations and quotations, deliver services, improve quality and security, and meet legal obligations.",
            ru: "Мы используем данные для ответа на запросы, подготовки консультаций и предложений, оказания услуг, повышения качества и безопасности и выполнения законных обязанностей.",
        },
    },
    {
        key: "cookies",
        title: {
            ka: "Cookies, ანალიტიკა და რეკლამა",
            en: "Cookies, analytics, and advertising",
            ru: "Cookies, аналитика и реклама",
        },
        body: {
            ka: "ანალიტიკური და სარეკლამო ტექნოლოგიები იტვირთება მხოლოდ შესაბამისი თანხმობის შემდეგ. არჩევანის შეცვლა შეგიძლიათ ბრაუზერის მონაცემების გასუფთავებით ან საიტზე ხელახლა არჩევით.",
            en: "Analytics and advertising technologies are loaded only after the relevant consent. You can change your choice by clearing browser data or selecting again on the website.",
            ru: "Аналитические и рекламные технологии загружаются только после соответствующего согласия. Изменить выбор можно, очистив данные браузера или выбрав заново на сайте.",
        },
    },
    {
        key: "sharing",
        title: {
            ka: "მონაცემების გაზიარება",
            en: "Information sharing",
            ru: "Передача данных",
        },
        body: {
            ka: "პერსონალურ მონაცემებს არ ვყიდით. საჭიროების შემთხვევაში მონაცემები შეიძლება დამუშავდეს სანდო ტექნიკურ მომწოდებლებთან მხოლოდ მომსახურების, ჰოსტინგის, ელფოსტის, ანალიტიკის ან სამართლებრივი მოთხოვნის შესრულების მიზნით.",
            en: "We do not sell personal data. Information may be processed by trusted technical providers only for service delivery, hosting, email, analytics, or legal compliance.",
            ru: "Мы не продаем персональные данные. Информация может обрабатываться надежными техническими поставщиками только для оказания услуг, хостинга, email, аналитики или выполнения требований закона.",
        },
    },
    {
        key: "retention",
        title: {
            ka: "შენახვა და უსაფრთხოება",
            en: "Retention and security",
            ru: "Хранение и безопасность",
        },
        body: {
            ka: "მონაცემებს ვინახავთ მხოლოდ იმ ვადით, რაც საჭიროა მოთხოვნისა და მომსახურების სამართავად ან კანონით გათვალისწინებული ვალდებულებისთვის. ვიყენებთ გონივრულ ტექნიკურ და ორგანიზაციულ უსაფრთხოების ზომებს.",
            en: "We retain information only as long as needed to manage the request and service or meet legal duties, and use reasonable technical and organizational safeguards.",
            ru: "Мы храним данные только столько, сколько необходимо для обработки запроса и услуги или выполнения законных обязанностей, и применяем разумные технические и организационные меры защиты.",
        },
    },
    {
        key: "rights",
        title: {
            ka: "თქვენი უფლებები",
            en: "Your rights",
            ru: "Ваши права",
        },
        body: {
            ka: "შეგიძლიათ მოითხოვოთ თქვენი მონაცემების შესახებ ინფორმაცია, გასწორება ან წაშლა, დამუშავების შეზღუდვა და თანხმობის გაუქმება, თუ სხვა სამართლებრივი საფუძველი არ არსებობს.",
            en: "You may request access, correction or deletion, restriction of processing, and withdrawal of consent where no other legal basis applies.",
            ru: "Вы можете запросить доступ, исправление или удаление данных, ограничение обработки и отзыв согласия, если нет другого законного основания.",
        },
    },
];

export default async function PrivacyPageContent() {
    const { contact, locale, translations } = await getSiteSettings();
    const eyebrow = translateText(translations, "privacy.eyebrow", locale, {
        ka: "სამართლებრივი ინფორმაცია",
        en: "Legal information",
        ru: "Правовая информация",
    });
    const title = translateText(translations, "privacy.title", locale, {
        ka: "კონფიდენციალურობის პოლიტიკა",
        en: "Privacy Policy",
        ru: "Политика конфиденциальности",
    });
    const intro = translateText(translations, "privacy.intro", locale, {
        ka: "ეს პოლიტიკა განმარტავს, რა ინფორმაციას ვიღებთ საკონტაქტო ფორმებიდან და ანალიტიკური ტექნოლოგიებიდან, რატომ ვიყენებთ მას და როგორ შეგიძლიათ თქვენი უფლებების გამოყენება.",
        en: "This policy explains what information we receive through contact forms and analytics technologies, why we use it, and how you can exercise your rights.",
        ru: "Эта политика объясняет, какие данные мы получаем через контактные формы и аналитические технологии, зачем используем их и как вы можете реализовать свои права.",
    });
    const updated = translateText(translations, "privacy.updated", locale, {
        ka: "ბოლო განახლება: 2 აგვისტო, 2026",
        en: "Last updated: August 2, 2026",
        ru: "Последнее обновление: 2 августа 2026 г.",
    });
    const contactTitle = translateText(
        translations,
        "privacy.contact.title",
        locale,
        {
            ka: "კონტაქტი კონფიდენციალურობის საკითხებზე",
            en: "Privacy contact",
            ru: "Контакты по вопросам конфиденциальности",
        },
    );
    const contactBody = translateText(
        translations,
        "privacy.contact.body",
        locale,
        {
            ka: "კონფიდენციალურობის საკითხზე დაგვიკავშირდით საიტზე მითითებული ტელეფონით ან ელფოსტით.",
            en: "For privacy questions, contact us using the phone number or email shown on the website.",
            ru: "По вопросам конфиденциальности свяжитесь с нами по телефону или email, указанным на сайте.",
        },
    );

    return (
        <div className="mx-auto max-w-4xl px-5 pb-20 pt-32 md:px-8 lg:pb-28 lg:pt-36">
            <header className="mb-12 border-b border-outline-variant/30 pb-10">
                <p className="mb-3 text-sm font-semibold uppercase tracking-wide text-secondary">
                    {eyebrow}
                </p>
                <h1 className="text-4xl font-bold leading-tight text-on-surface sm:text-5xl">
                    {title}
                </h1>
                <p className="mt-6 max-w-3xl text-lg leading-8 text-on-surface-variant">
                    {intro}
                </p>
                <p className="mt-4 text-sm text-on-surface-variant/70">{updated}</p>
            </header>

            <div className="legal-content">
                {sections.map((section) => (
                    <section key={section.key}>
                        <h2>
                            {translateText(
                                translations,
                                `privacy.${section.key}.title`,
                                locale,
                                section.title,
                            )}
                        </h2>
                        <p>
                            {translateText(
                                translations,
                                `privacy.${section.key}.body`,
                                locale,
                                section.body,
                            )}
                        </p>
                    </section>
                ))}

                <section>
                    <h2>{contactTitle}</h2>
                    <p>{contactBody}</p>
                    <div className="mt-4 flex flex-col gap-2">
                        {contact.phones.map((phone) => (
                            <a
                                className="w-fit"
                                href={`tel:${phone.replace(/[^\d+]/g, "")}`}
                                key={phone}
                            >
                                {phone}
                            </a>
                        ))}
                        {contact.email ? (
                            <a className="w-fit" href={`mailto:${contact.email}`}>
                                {contact.email}
                            </a>
                        ) : null}
                    </div>
                </section>
            </div>
        </div>
    );
}
