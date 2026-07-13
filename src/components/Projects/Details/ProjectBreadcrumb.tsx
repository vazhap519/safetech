import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectBreadcrumb({
    current,
}: {
    current: string;
}) {
    const { locale, translations } = await getSiteSettings();

    return (
        <nav
            aria-label="Breadcrumb"
            className="font-mono-sm text-mono-sm text-on-surface-variant"
        >
            <ol className="flex flex-wrap items-center gap-2">
                <li>
                    <LocalizedLink
                        className="transition-colors hover:text-primary"
                        href="/"
                    >
                        {translateText(translations, "nav.home", locale, {
                            ka: "მთავარი",
                            en: "Home",
                            ru: "Главная",
                        })}
                    </LocalizedLink>
                </li>
                <li aria-hidden="true">›</li>
                <li>
                    <LocalizedLink
                        className="transition-colors hover:text-primary"
                        href="/projects"
                    >
                        {translateText(translations, "nav.projects", locale, {
                            ka: "პროექტები",
                            en: "Projects",
                            ru: "Проекты",
                        })}
                    </LocalizedLink>
                </li>
                <li aria-hidden="true">›</li>
                <li aria-current="page" className="text-primary-fixed-dim">
                    {current}
                </li>
            </ol>
        </nav>
    );
}
