import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceBreadcrumb({
    current,
}: {
    current: string;
}) {
    const { locale, translations } = await getSiteSettings();
    const homeLabel = translateText(translations, "nav.home", locale, null);
    const servicesLabel = translateText(
        translations,
        "nav.services",
        locale,
        null,
    );

    if (!homeLabel && !servicesLabel && !current) return null;

    return (
        <nav className="mb-unit-lg text-label-md text-on-surface-variant">
            <ol className="flex flex-wrap items-center gap-unit-sm">
                {homeLabel ? (
                    <li>
                        <LocalizedLink
                            className="transition-colors hover:text-primary"
                            href="/"
                        >
                            {homeLabel}
                        </LocalizedLink>
                    </li>
                ) : null}
                {homeLabel && servicesLabel ? (
                    <li aria-hidden="true">
                        <Icon className="text-[14px]" name="chevron_right" />
                    </li>
                ) : null}
                {servicesLabel ? (
                    <li>
                        <LocalizedLink
                            className="transition-colors hover:text-primary"
                            href="/services"
                        >
                            {servicesLabel}
                        </LocalizedLink>
                    </li>
                ) : null}
                {(homeLabel || servicesLabel) && current ? (
                    <li aria-hidden="true">
                        <Icon className="text-[14px]" name="chevron_right" />
                    </li>
                ) : null}
                {current ? (
                    <li aria-current="page" className="text-primary">
                        {current}
                    </li>
                ) : null}
            </ol>
        </nav>
    );
}
