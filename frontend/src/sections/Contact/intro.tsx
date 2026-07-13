import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function Intro() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <section className="bg-background py-unit-xl">
            <div className="container mx-auto grid max-w-container-max grid-cols-1 items-center gap-unit-lg px-margin-desktop md:grid-cols-2 lg:gap-unit-xl">
                <div className="order-2 md:order-1">
                    <Typography
                        as="h2"
                        variant="contact-intro-title"
                        className="mb-unit-md"
                    >
                        {t("contact.intro.title", {
                            ka: "ციფრული გამძლეობა",
                            en: "Digital resilience",
                            ru: "Цифровая устойчивость",
                        })}
                    </Typography>

                    <div className="space-y-unit-md text-base leading-relaxed text-on-surface-variant md:text-lg">
                        <p>
                            {t("contact.intro.paragraph.0", {
                                ka: "SafeTech-ის ექსპერტთა ჯგუფი უზრუნველყოფს კორპორატიული დონის ინფრასტრუქტურის დაპროექტებას, რომელიც პასუხობს უსაფრთხოების თანამედროვე გამოწვევებს.",
                                en: "The SafeTech expert team designs enterprise-grade infrastructure aligned with modern security challenges.",
                                ru: "Команда экспертов SafeTech проектирует инфраструктуру корпоративного уровня, отвечающую современным вызовам безопасности.",
                            })}
                        </p>

                        <p>
                            {t("contact.intro.paragraph.1", {
                                ka: "ჩვენი მიდგომა ეფუძნება სისტემურ ანალიზს და მასშტაბირებად არქიტექტურას, რაც იცავს თქვენს მონაცემებსა და ფიზიკურ აქტივებს.",
                                en: "Our approach is built on systems analysis and scalable architecture that protects your data and physical assets.",
                                ru: "Наш подход основан на системном анализе и масштабируемой архитектуре, которая защищает ваши данные и физические активы.",
                            })}
                        </p>
                    </div>

                    <div className="mt-unit-lg flex flex-col gap-unit-md sm:flex-row">
                        <div className="flex items-center gap-unit-xs">
                            <span className="h-2 w-2 rounded-full bg-secondary shadow-[0_0_8px_#4cd7f6] animate-pulse" />
                            <span className="text-xs uppercase tracking-widest text-secondary md:text-sm">
                                {t("contact.intro.badge.0", {
                                    ka: "აქტიური მონიტორინგი",
                                    en: "Active monitoring",
                                    ru: "Активный мониторинг",
                                })}
                            </span>
                        </div>

                        <div className="flex items-center gap-unit-xs">
                            <span className="h-2 w-2 rounded-full bg-primary shadow-[0_0_8px_#b4c5ff] animate-pulse" />
                            <span className="text-xs uppercase tracking-widest text-primary md:text-sm">
                                {t("contact.intro.badge.1", {
                                    ka: "კორპორატიული უსაფრთხოება",
                                    en: "Enterprise security",
                                    ru: "Корпоративная безопасность",
                                })}
                            </span>
                        </div>
                    </div>
                </div>

                <div className="order-1 md:order-2">
                    <div className="glass-panel group overflow-hidden rounded-xl p-2">
                        <Image
                            variant="contact-intro"
                            src={branding.defaultImage}
                            alt={t("contact.intro.imageAlt", {
                                ka: "სერვერული თაროების ვიზუალიზაცია",
                                en: "Server rack visualization",
                                ru: "Визуализация серверных стоек",
                            })}
                            width={610}
                            height={410}
                            sizes="(max-width: 768px) 100vw, 50vw"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
