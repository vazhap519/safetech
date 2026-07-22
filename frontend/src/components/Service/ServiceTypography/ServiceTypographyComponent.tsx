import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceTypographyComponent() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "services.catalog.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "services.catalog.description",
        locale,
        null,
    );
    const pageLabel = translateText(
        translations,
        "services.catalog.page",
        locale,
        null,
    );

    if (!title && !description && !pageLabel) return null;

    return (
        <div className="mb-unit-xl flex flex-col justify-between gap-unit-md md:flex-row md:items-end">
            <div>
                {title ? (
                    <Typography as="h2" variant="section-title">
                        {title}
                    </Typography>
                ) : null}
                {description ? (
                    <Typography as="p" variant="section-description">
                        {description}
                    </Typography>
                ) : null}
            </div>
            {pageLabel ? (
                <div className="font-mono-sm text-mono-sm text-on-surface-variant">
                    {pageLabel}
                </div>
            ) : null}
        </div>
    );
}
