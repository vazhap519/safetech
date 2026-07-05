import WhoComponent from "@/components/About/WhoComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function WhoSection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const items = [
        {
            icon: "security",
            title: t("about.who.item.0.title", {
                ka: "უსაფრთხოების ინფრასტრუქტურა",
                en: "Security infrastructure",
                ru: "Инфраструктура безопасности",
            }),
            description: t("about.who.item.0.description", {
                ka: "კომპლექსური უსაფრთხოების სისტემები, ინტელექტუალური მონიტორინგი და დაცვა.",
                en: "Integrated security systems, intelligent monitoring, and protection.",
                ru: "Комплексные системы безопасности, интеллектуальный мониторинг и защита.",
            }),
        },
        {
            icon: "lan",
            title: t("about.who.item.1.title", {
                ka: "ქსელური არქიტექტურა",
                en: "Network architecture",
                ru: "Сетевая архитектура",
            }),
            description: t("about.who.item.1.description", {
                ka: "მასშტაბირებადი LAN, Wi-Fi და ოპტიკური ქსელები სტაბილური მუშაობისთვის.",
                en: "Scalable LAN, Wi-Fi, and fiber networks for stable operations.",
                ru: "Масштабируемые LAN, Wi-Fi и оптические сети для стабильной работы.",
            }),
        },
        {
            icon: "dns",
            title: t("about.who.item.2.title", {
                ka: "სერვერული გარემო",
                en: "Server environment",
                ru: "Серверная среда",
            }),
            description: t("about.who.item.2.description", {
                ka: "სერვერები, საცავები და მონაცემთა ხელმისაწვდომობის გამართული არქიტექტურა.",
                en: "Servers, storage, and resilient data availability architecture.",
                ru: "Серверы, хранилища и надежная архитектура доступности данных.",
            }),
        },
    ];

    return (
        <section className="py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="mb-unit-xl text-center">
                    <Typography as="h2" variant="section-title">
                        {t("about.who.title", {
                            ka: "მიმართულებები",
                            en: "Focus areas",
                            ru: "Направления",
                        })}
                    </Typography>
                    <Typography as="p" variant="section-description">
                        {t("about.who.description", {
                            ka: "პროფესიონალური გადაწყვეტილებები ნებისმიერი მასშტაბისათვის",
                            en: "Professional solutions for projects of any scale",
                            ru: "Профессиональные решения для проектов любого масштаба",
                        })}
                    </Typography>
                </div>
                <div className="grid grid-cols-1 gap-gutter md:grid-cols-3">
                    {items.map((item) => (
                        <WhoComponent key={item.title} {...item} />
                    ))}
                </div>
            </div>
        </section>
    );
}
