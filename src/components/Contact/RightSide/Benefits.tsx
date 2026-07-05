import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function Benefits() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <div className="glass-panel rounded-xl p-unit-md transition-transform duration-300 hover:scale-105">
            <div className="flex items-start gap-unit-md">
                <span
                    className="material-symbols-outlined text-4xl text-secondary"
                    data-icon="architecture"
                >
                    architecture
                </span>
                <div>
                    <Typography as="h3" variant="contact-title">
                        {t("contact.side.title", {
                            ka: "პროფესიონალური დაგეგმვა",
                            en: "Professional planning",
                            ru: "Профессиональное планирование",
                        })}
                    </Typography>
                    <Typography
                        as="p"
                        variant="section-description"
                        className="mx-auto max-w-2xl"
                    >
                        {t("contact.side.description", {
                            ka: "თითოეული პროექტი მუშავდება ინდივიდუალურად, ობიექტის სპეციფიკის გათვალისწინებით.",
                            en: "Every project is designed individually with the specific site requirements in mind.",
                            ru: "Каждый проект разрабатывается индивидуально с учетом специфики объекта.",
                        })}
                    </Typography>
                </div>
            </div>
        </div>
    );
}
