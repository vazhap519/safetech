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
                py-20
                md:py-28
                bg-surface-container-lowest/30
            "
        >

            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-5
                    md:px-10
                    xl:px-margin-desktop
                    grid
                    grid-cols-1
                    lg:grid-cols-2
                    gap-16
                    xl:gap-unit-xl
                    items-center
                "
            >

                {/* LEFT IMAGE */}
                <div
                    className="
                        relative
                        order-2
                        lg:order-1
                        group
                        w-full
                        max-w-[760px]
                        mx-auto
                    "
                >

                    {/* Glow */}
                    <div
                        className="
                            absolute
                            -inset-4
                            bg-primary/10
                            blur-3xl
                            rounded-full
                            group-hover:bg-primary/20
                            transition-all
                            duration-700
                        "
                    ></div>

                    {/* Image */}
                    <Image
                        variant="home-infrastructure"
                        src={branding.defaultImage}
                        alt="SafeTech-ის ქსელური ინფრასტრუქტურის ტოპოლოგია"
                        sizes="(max-width: 768px) 100vw, 50vw"
                        width={600}
                        height={410}
                    />
                </div>

                {/* RIGHT CONTENT */}
                <div
                    className="
                        order-1
                        lg:order-2
                        space-y-8
                        text-center
                        lg:text-left
                    "
                >

                    {/* Badge */}
                    <div
                        className="
                            inline-flex
                            items-center
                            gap-2
                            px-4
                            py-2
                            rounded-full
                            glass-card
                            mx-auto
                            lg:mx-0
                        "
                    >
                        <span className="status-dot"></span>

                        <span
                            className="
                                text-secondary
                                font-mono-sm
                                text-mono-sm
                                uppercase
                                tracking-widest
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

                    <Typography
                        as="h2"
                        variant="section-title"
                    >
                        ინფრასტრუქტურული მიდგომა
                    </Typography>

                    {/* Description */}
                    <Typography
                        as="p"
                        variant="description"
                        className="
        max-w-[620px]
        mx-auto
        lg:mx-0
    "
                    >
                        ჩვენ არ ვაყენებთ უბრალოდ მოწყობილობებს.
                        ჩვენ ვქმნით ერთიან, მართვად და
                        მასშტაბირებად ეკოსისტემას თქვენი
                        ბიზნესის ზრდისთვის.
                    </Typography>

                    {/* Features */}
                    <div
                        className="
                            space-y-5
                            pt-2
                        "
                    >
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

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    w-[500px]
                    h-[500px]
                    bg-primary/5
                    rounded-full
                    blur-[150px]
                    -z-10
                "
            ></div>
        </section>
    );
}
