import Infrastructurecomponent from "@/components/Home/Infrastructurecomponent";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const infrastructureItems = [
    {
        icon: "schema",
        key: "home.infrastructure.items.0",
        title: {
            ka: "მასშტაბირებადი არქიტექტურა",
            en: "Scalable Architecture",
            ru: "Масштабируемая архитектура",
        },
        description: {
            ka: "სისტემა, რომელიც ბიზნესის ზრდასთან ერთად მარტივად ვითარდება.",
            en: "Infrastructure that grows cleanly with your business and future locations.",
            ru: "Система, которая развивается вместе с ростом бизнеса.",
        },
    },
    {
        icon: "lan",
        key: "home.infrastructure.items.1",
        title: {
            ka: "ერთიანი ქსელური ლოგიკა",
            en: "Unified Network Logic",
            ru: "Единая сетевая логика",
        },
        description: {
            ka: "უსაფრთხოება, წვდომა და სერვერული სერვისები ერთიანად მუშაობს.",
            en: "Security, access, and server layers operate as one coordinated environment.",
            ru: "Безопасность, доступ и серверные сервисы работают согласованно.",
        },
    },
    {
        icon: "monitoring",
        key: "home.infrastructure.items.2",
        title: {
            ka: "მართვადობა და მონიტორინგი",
            en: "Manageability and Monitoring",
            ru: "Управляемость и мониторинг",
        },
        description: {
            ka: "ცენტრალიზებული ხილვადობა ამცირებს გაუთვალისწინებელ გაჩერებებს.",
            en: "Centralized visibility reduces downtime and improves daily operations.",
            ru: "Централизованная видимость снижает простои и повышает контроль.",
        },
    },
];

export default async function Infrastructure() {
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <section
            className="
                relative
                overflow-hidden
                bg-surface-container-lowest/30
                py-20
                md:py-28
            "
        >
            <div
                className="
                    mx-auto
                    grid
                    max-w-container-max
                    grid-cols-1
                    items-center
                    gap-16
                    px-5
                    md:px-10
                    lg:grid-cols-2
                    xl:gap-unit-xl
                    xl:px-margin-desktop
                "
            >
                <div
                    className="
                        group
                        order-2
                        relative
                        mx-auto
                        w-full
                        max-w-[760px]
                        lg:order-1
                    "
                >
                    <div
                        className="
                            absolute
                            -inset-4
                            rounded-full
                            bg-primary/10
                            blur-3xl
                            transition-all
                            duration-700
                            group-hover:bg-primary/20
                        "
                    />

                    <Image
                        variant="home-infrastructure"
                        src={branding.defaultImage}
                        alt={translateText(
                            translations,
                            "home.infrastructure.imageAlt",
                            locale,
                            {
                                ka: "SafeTech-ის ქსელური ინფრასტრუქტურის ტოპოლოგია",
                                en: "SafeTech network infrastructure topology",
                                ru: "Топология сетевой инфраструктуры SafeTech",
                            },
                        )}
                        sizes="(max-width: 768px) 100vw, 50vw"
                        width={600}
                        height={410}
                    />
                </div>

                <div
                    className="
                        order-1
                        space-y-8
                        text-center
                        lg:order-2
                        lg:text-left
                    "
                >
                    <div
                        className="
                            mx-auto
                            inline-flex
                            items-center
                            gap-2
                            rounded-full
                            px-4
                            py-2
                            glass-card
                            lg:mx-0
                        "
                    >
                        <span className="status-dot" />

                        <span
                            className="
                                font-mono-sm
                                text-mono-sm
                                uppercase
                                tracking-widest
                                text-secondary
                            "
                        >
                            {translateText(
                                translations,
                                "home.infrastructure.eyebrow",
                                locale,
                                {
                                    ka: "ინფრასტრუქტურული არქიტექტურა",
                                    en: "Enterprise Architecture",
                                    ru: "Архитектура инфраструктуры",
                                },
                            )}
                        </span>
                    </div>

                    <Typography as="h2" variant="section-title">
                        {translateText(
                            translations,
                            "home.infrastructure.title",
                            locale,
                            {
                                ka: "ინფრასტრუქტურული მიდგომა",
                                en: "Infrastructure Approach",
                                ru: "Инфраструктурный подход",
                            },
                        )}
                    </Typography>

                    <Typography
                        as="p"
                        variant="description"
                        className="mx-auto max-w-[620px] lg:mx-0"
                    >
                        {translateText(
                            translations,
                            "home.infrastructure.description",
                            locale,
                            {
                                ka: "ჩვენ არ ვაყენებთ უბრალოდ მოწყობილობებს. ჩვენ ვქმნით ერთიან, მართვად და მასშტაბირებად ეკოსისტემას თქვენი ბიზნესის ზრდისთვის.",
                                en: "We do more than install devices. We build one manageable and scalable ecosystem for your business growth.",
                                ru: "Мы не просто устанавливаем устройства. Мы создаем единую, управляемую и масштабируемую экосистему для роста вашего бизнеса.",
                            },
                        )}
                    </Typography>

                    <div className="space-y-5 pt-2">
                        {infrastructureItems.map((item) => (
                            <Infrastructurecomponent
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
            </div>

            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    -z-10
                    h-[500px]
                    w-[500px]
                    rounded-full
                    bg-primary/5
                    blur-[150px]
                "
            />
        </section>
    );
}
