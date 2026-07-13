import LocalizedLink from "@/components/ui/LocalizedLink";
import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceBreadcrumb({
    current,
}: {
    current: string;
}) {
    const { locale, translations } = await getSiteSettings();

    return (
        <nav
            aria-label="Breadcrumb"
            className="mb-unit-lg text-label-md text-on-surface-variant"
        >
            <ol className="flex flex-wrap items-center gap-unit-sm">
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
                <li aria-hidden="true">
                    <Icon className="text-[14px]" name="chevron_right" />
                </li>
                <li>
                    <LocalizedLink
                        className="transition-colors hover:text-primary"
                        href="/services"
                    >
                        {translateText(translations, "nav.services", locale, {
                            ka: "სერვისები",
                            en: "Services",
                            ru: "Услуги",
                        })}
                    </LocalizedLink>
                </li>
                <li aria-hidden="true">
                    <Icon className="text-[14px]" name="chevron_right" />
                </li>
                <li aria-current="page" className="text-primary">
                    {current}
                </li>
            </ol>
        </nav>
    );
}
