import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceTypographyComponent() {
    const { locale, translations } = await getSiteSettings();

    return (
        <div className="mb-unit-xl flex flex-col justify-between gap-unit-md md:flex-row md:items-end">
            <div>
                <Typography as="h2" variant="section-title">
                    {translateText(translations, "services.catalog.title", locale, {
                        ka: "სერვისების კატალოგი",
                        en: "Service Catalog",
                        ru: "Каталог услуг",
                    })}
                </Typography>
                <Typography as="p" variant="section-description">
                    {translateText(
                        translations,
                        "services.catalog.description",
                        locale,
                        {
                            ka: "შეარჩიეთ თქვენს მოთხოვნებზე მორგებული გადაწყვეტა",
                            en: "Choose the solution that fits your requirements",
                            ru: "Выберите решение, подходящее под ваши задачи",
                        },
                    )}
                </Typography>
            </div>
            <div className="font-mono-sm text-mono-sm text-on-surface-variant">
                {translateText(translations, "services.catalog.page", locale, {
                    ka: "გვერდი 01 / 04",
                    en: "Page 01 / 04",
                    ru: "Страница 01 / 04",
                })}
            </div>
        </div>
    );
}
