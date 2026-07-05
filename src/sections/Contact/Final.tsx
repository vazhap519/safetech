import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function FinalSection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <section
            className="
                bg-gradient-to-b
                from-surface-container-low
                to-background
                py-16
                sm:py-20
                md:py-24
                lg:py-unit-xl
            "
        >
            <div className="mx-auto flex max-w-container-max flex-col items-center justify-center px-4 sm:px-6 lg:px-margin-desktop">
                <Typography
                    as="h2"
                    variant="contact-final-cta"
                    className="mx-auto max-w-5xl text-balance text-center"
                >
                    {t("contact.final.title", {
                        ka: "SafeTech - თანამედროვე ინფრასტრუქტურის პარტნიორი",
                        en: "SafeTech - your partner for modern infrastructure",
                        ru: "SafeTech - партнер в современной инфраструктуре",
                    })}
                </Typography>

                <div className="mt-6 flex w-full justify-center md:mt-8">
                    <ActionLink
                        href="#contact-form"
                        className="w-full max-w-xs sm:w-auto sm:max-w-none"
                    >
                        {t("contact.final.button", {
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
