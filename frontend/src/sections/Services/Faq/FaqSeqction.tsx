import Icon from "@/components/ui/Icon";
import ActionLink from "@/components/ui/ActionLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const defaultFaqs = [
    {
        question: {
            ka: "რა დრო სჭირდება კამერების მონტაჟს?",
            en: "How long does camera installation take?",
            ru: "Сколько времени занимает установка камер?",
        },
        answer: {
            ka: "მონტაჟის დრო დამოკიდებულია ობიექტის სირთულესა და კამერების რაოდენობაზე. საშუალოდ, სტანდარტული სისტემის გამართვას 1-2 სამუშაო დღე სჭირდება.",
            en: "Installation time depends on the size of the site and the number of cameras. A standard system usually takes 1-2 working days.",
            ru: "Срок монтажа зависит от сложности объекта и количества камер. Обычно стандартная система настраивается за 1-2 рабочих дня.",
        },
    },
    {
        question: {
            ka: "გაქვთ თუ არა გარანტია მოწყობილობებზე?",
            en: "Do you provide equipment warranty?",
            ru: "Предоставляете ли вы гарантию на оборудование?",
        },
        answer: {
            ka: "დიახ, ჩვენს მიერ მოწოდებულ მოწყობილობებსა და შესრულებულ სამუშაოზე ვრცელდება ოფიციალური გარანტია.",
            en: "Yes, the equipment we supply and the work we complete are covered by an official warranty.",
            ru: "Да, на поставленное нами оборудование и выполненные работы распространяется официальная гарантия.",
        },
    },
    {
        question: {
            ka: "როგორ ხდება Wi-Fi დაფარვის გათვლა?",
            en: "How do you calculate Wi-Fi coverage?",
            ru: "Как рассчитывается покрытие Wi-Fi?",
        },
        answer: {
            ka: "ვიყენებთ სპეციალურ პროგრამულ უზრუნველყოფას და Site Survey მეთოდოლოგიას, რათა განვსაზღვროთ სიგნალის ოპტიმალური გავრცელება.",
            en: "We use specialized software and a Site Survey methodology to define optimal signal coverage.",
            ru: "Мы используем специализированное ПО и методологию Site Survey, чтобы определить оптимальное распространение сигнала.",
        },
    },
];

export default async function FaqSeqAction() {
    const { locale, translations } = await getSiteSettings();
    const faqs = defaultFaqs.map((item, index) => ({
        answer: translateText(
            translations,
            `services.faq.item.${index}.answer`,
            locale,
            item.answer,
        ),
        question: translateText(
            translations,
            `services.faq.item.${index}.question`,
            locale,
            item.question,
        ),
    }));

    return (
        <section className="py-unit-xl px-margin-desktop max-w-container-max mx-auto">
            <div className="grid md:grid-cols-2 gap-unit-xl items-start">
                <div>
                    <h2 className="font-headline-lg text-headline-lg text-on-surface mb-unit-md">
                        {translateText(translations, "services.faq.title", locale, {
                            ka: "ხშირად დასმული კითხვები",
                            en: "Frequently asked questions",
                            ru: "Часто задаваемые вопросы",
                        })}
                    </h2>
                    <p className="font-body-md text-body-md text-on-surface-variant mb-unit-lg">
                        {translateText(translations, "services.faq.description", locale, {
                            ka: "თუ ვერ იპოვეთ თქვენთვის საინტერესო კითხვაზე პასუხი, მოგვწერეთ ან დაგვიკავშირდით.",
                            en: "If you cannot find the answer you need, write to us or contact our team.",
                            ru: "Если вы не нашли ответ на свой вопрос, напишите нам или свяжитесь с командой.",
                        })}
                    </p>
                    <ActionLink href="/contact">
                        {translateText(translations, "services.faq.contact", locale, {
                            ka: "კონტაქტი",
                            en: "Contact",
                            ru: "Контакты",
                        })}
                    </ActionLink>
                </div>
                <div className="space-y-unit-md">
                    {faqs.map((item, index) => (
                        <details className="glass-card rounded-xl overflow-hidden group" key={item.question} open={index === 0}>
                            <summary className="p-unit-md flex justify-between items-center cursor-pointer list-none">
                                <span className="font-headline-md text-[18px]">{item.question}</span>
                                <Icon
                                    className="transition-transform group-open:rotate-180"
                                    name="expand_more"
                                />
                            </summary>
                            <div className="px-unit-md pb-unit-md text-on-surface-variant border-t border-outline-variant/10 pt-4">
                                {item.answer}
                            </div>
                        </details>
                    ))}
                </div>
            </div>
        </section>
    )
}
