import Items from "@/components/Contact/support/Items";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function Support() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <section className="relative overflow-hidden py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 items-center gap-unit-lg px-margin-desktop lg:grid-cols-2 lg:gap-unit-xl">
                <div className="relative order-1">
                    <Image
                        variant="contact-support"
                        src={branding.defaultImage}
                        alt={t("contact.support.imageAlt", {
                            ka: "უსაფრთხოების მონიტორინგის პანელი",
                            en: "Security monitoring dashboard",
                            ru: "Панель мониторинга безопасности",
                        })}
                        sizes="(max-width: 768px) 100vw, 50vw"
                    />

                    <div className="glass-panel absolute bottom-4 right-4 rounded-xl border-secondary/30 p-3 md:bottom-6 md:right-6 md:p-4">
                        <div className="flex items-center gap-2">
                            <div className="h-2 w-2 rounded-full bg-secondary animate-pulse md:h-3 md:w-3" />
                            <span className="whitespace-nowrap text-xs uppercase tracking-wider md:text-sm">
                                {t("contact.support.badge", {
                                    ka: "აქტიური მონიტორინგი",
                                    en: "Active monitoring",
                                    ru: "Активный мониторинг",
                                })}
                            </span>
                        </div>
                    </div>
                </div>

                <div className="order-2">
                    <Typography as="h2" variant="contact-support-title">
                        {t("contact.support.title", {
                            ka: "ინფრასტრუქტურული მხარდაჭერა",
                            en: "Infrastructure support",
                            ru: "Инфраструктурная поддержка",
                        })}
                    </Typography>

                    <Typography
                        as="p"
                        variant="description"
                        className="mb-unit-md"
                    >
                        {t("contact.support.description", {
                            ka: "ჩვენი გუნდი უზრუნველყოფს 24/7 დისტანციურ მონიტორინგსა და ტექნიკურ მხარდაჭერას. ნებისმიერი ანომალია სისტემაში ფიქსირდება მომენტალურად ჩვენი ცენტრალიზებული მართვის პანელიდან.",
                            en: "Our team provides 24/7 remote monitoring and technical support. Any system anomaly is detected immediately through our centralized control panel.",
                            ru: "Наша команда обеспечивает круглосуточный удаленный мониторинг и техническую поддержку. Любая аномалия в системе фиксируется мгновенно через нашу централизованную панель управления.",
                        })}
                    </Typography>

                    <ul className="space-y-unit-sm">
                        <Items />
                    </ul>
                </div>
            </div>
        </section>
    );
}
