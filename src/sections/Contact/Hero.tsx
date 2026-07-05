import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function Hero() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <section
            className="
                hero-mesh
                topology-grid
                relative
                flex
                min-h-[70vh]
                items-center
                justify-center
                overflow-hidden
                px-4
                pt-24
                md:min-h-[75vh]
                md:pt-28
            "
        >
            <div className="pointer-events-none absolute inset-0">
                <div className="animated-glow absolute left-1/4 top-1/4 h-48 w-48 rounded-full bg-primary/10 blur-[80px] md:h-96 md:w-96 md:blur-[120px]" />
                <div
                    className="animated-glow absolute bottom-1/4 right-1/4 h-32 w-32 rounded-full bg-secondary/10 blur-[60px] md:h-64 md:w-64 md:blur-[100px]"
                    style={{
                        animationDelay: "-2s",
                    }}
                />
            </div>

            <div className="relative z-10 mx-auto max-w-container-max px-margin-desktop text-center">
                <Typography
                    as="h1"
                    variant="contact-title"
                    className="mx-auto max-w-5xl"
                >
                    {t("contact.hero.title", {
                        ka: "დავგეგმოთ თქვენი უსაფრთხო IT ინფრასტრუქტურა",
                        en: "Let's plan your secure IT infrastructure",
                        ru: "Давайте спланируем вашу безопасную IT-инфраструктуру",
                    })}
                </Typography>

                <Typography
                    as="p"
                    variant="description"
                    className="mx-auto mb-unit-lg max-w-2xl"
                >
                    {t("contact.hero.description", {
                        ka: "მიიღეთ პროფესიონალური კონსულტაცია ვიდეოსამეთვალყურეო, ქსელური, დაშვების კონტროლის და სერვერული ინფრასტრუქტურის მიმართულებით.",
                        en: "Get professional guidance for CCTV, networking, access control, and server infrastructure.",
                        ru: "Получите профессиональную консультацию по видеонаблюдению, сетям, контролю доступа и серверной инфраструктуре.",
                    })}
                </Typography>

                <div className="flex justify-center">
                    <ActionLink
                        href="#contact-form"
                        className="rounded-full px-unit-lg py-4"
                    >
                        {t("contact.hero.button", {
                            ka: "კონსულტაციის მოთხოვნა",
                            en: "Request consultation",
                            ru: "Запросить консультацию",
                        })}
                    </ActionLink>
                </div>
            </div>
        </section>
    );
}
