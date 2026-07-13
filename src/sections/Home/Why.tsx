import WhyComponent from "@/components/Home/Whycomponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const whyItems = [
    {
        icon: "verified",
        key: "home.why.items.0",
        title: {
            ka: "სერტიფიცირებული პარტნიორობა",
            en: "Certified Partnerships",
            ru: "Сертифицированные партнерства",
        },
        description: {
            ka: "ვმუშაობთ სანდო საერთაშორისო ბრენდებთან და ავტორიზებულ ეკოსისტემაში.",
            en: "We work with trusted global vendors and authorized partner programs.",
            ru: "Мы работаем с надежными международными вендорами и авторизованными программами.",
        },
    },
    {
        icon: "security",
        key: "home.why.items.1",
        title: {
            ka: "უსაფრთხოებაზე ფოკუსი",
            en: "Security-First Approach",
            ru: "Подход с фокусом на безопасность",
        },
        description: {
            ka: "ფიზიკურ და ციფრულ რისკებს ერთიან ინფრასტრუქტურულ ხედვაში ვაერთიანებთ.",
            en: "We align physical and digital risk controls into one infrastructure strategy.",
            ru: "Мы объединяем физические и цифровые меры защиты в единую стратегию.",
        },
    },
    {
        icon: "hub",
        key: "home.why.items.2",
        title: {
            ka: "ინტეგრირებული სისტემები",
            en: "Integrated Systems",
            ru: "Интегрированные системы",
        },
        description: {
            ka: "CCTV, წვდომა, ქსელი და სერვერები მუშაობს ერთიანი ლოგიკით და მართვით.",
            en: "CCTV, access control, networking, and servers operate as one managed stack.",
            ru: "Видеонаблюдение, доступ, сеть и серверы работают как единая система.",
        },
    },
    {
        icon: "support_agent",
        key: "home.why.items.3",
        title: {
            ka: "ოპერატიული მხარდაჭერა",
            en: "Responsive Support",
            ru: "Быстрая поддержка",
        },
        description: {
            ka: "პრობლემებზე რეაგირებას და შემდგომ მომსახურებას ერთ გუნდში იღებთ.",
            en: "Implementation and ongoing support stay under one accountable team.",
            ru: "Внедрение и дальнейшее обслуживание обеспечивает одна команда.",
        },
    },
    {
        icon: "schema",
        key: "home.why.items.4",
        title: {
            ka: "მასშტაბირებადი არქიტექტურა",
            en: "Scalable Architecture",
            ru: "Масштабируемая архитектура",
        },
        description: {
            ka: "ვაწყობთ ინფრასტრუქტურას ისე, რომ ახალი ლოკაციები და მოწყობილობები მარტივად დაემატოს.",
            en: "Your infrastructure is designed to grow cleanly with new locations and devices.",
            ru: "Инфраструктура проектируется с учетом роста и новых площадок.",
        },
    },
    {
        icon: "monitoring",
        key: "home.why.items.5",
        title: {
            ka: "რეალური ხილვადობა",
            en: "Operational Visibility",
            ru: "Полная видимость процессов",
        },
        description: {
            ka: "მონიტორინგი, ლოგები და ცენტრალიზებული კონტროლი ზრდის გამჭვირვალობას.",
            en: "Monitoring, logs, and centralized control improve day-to-day visibility.",
            ru: "Мониторинг, журналы и централизованный контроль повышают прозрачность.",
        },
    },
];

export default async function Why() {
    const { locale, translations } = await getSiteSettings();

    return (
        <section
            className="
                relative
                overflow-hidden
                bg-surface-container-low
                py-20
                md:py-28
            "
        >
            <div
                className="
                    mx-auto
                    max-w-container-max
                    px-5
                    md:px-10
                    xl:px-margin-desktop
                "
            >
                <div
                    className="
                        mb-14
                        text-center
                        md:mb-20
                    "
                >
                    <span
                        className="
                            mb-4
                            inline-block
                            font-mono-sm
                            text-mono-sm
                            uppercase
                            tracking-[0.3em]
                            text-primary
                        "
                    >
                        {translateText(translations, "home.why.eyebrow", locale, {
                            ka: "რატომ ჩვენ",
                            en: "Why Choose Us",
                            ru: "Почему мы",
                        })}
                    </span>

                    <Typography as="h2" variant="section-title">
                        {translateText(translations, "home.why.title", locale, {
                            ka: "რატომ SafeTech?",
                            en: "Why SafeTech?",
                            ru: "Почему SafeTech?",
                        })}
                    </Typography>

                    <Typography
                        as="p"
                        variant="section-description"
                        className="mx-auto max-w-2xl"
                    >
                        {translateText(
                            translations,
                            "home.why.description",
                            locale,
                            {
                                ka: "ჩვენი გამოცდილება და ტექნოლოგიური პარტნიორობა გლობალურ ლიდერებთან საშუალებას გვაძლევს შემოგთავაზოთ საუკეთესო ხარისხი.",
                                en: "Our experience and technology partnerships with global leaders let us deliver dependable, high-quality outcomes.",
                                ru: "Наш опыт и технологические партнерства с мировыми лидерами позволяют нам обеспечивать надежный результат высокого качества.",
                            },
                        )}
                    </Typography>
                </div>

                <div
                    className="
                        grid
                        grid-cols-1
                        gap-6
                        md:gap-gutter
                        sm:grid-cols-2
                        xl:grid-cols-3
                    "
                >
                    {whyItems.map((item) => (
                        <WhyComponent
                            description={translateText(
                                translations,
                                `${item.key}.description`,
                                locale,
                                item.description,
                            )}
                            icon={item.icon}
                            key={item.key}
                            title={translateText(
                                translations,
                                `${item.key}.title`,
                                locale,
                                item.title,
                            )}
                        />
                    ))}
                </div>
            </div>

            <div
                className="
                    absolute
                    left-[-250px]
                    top-[-250px]
                    -z-10
                    h-[600px]
                    w-[600px]
                    rounded-full
                    bg-primary/5
                    blur-[160px]
                "
            />
        </section>
    );
}
