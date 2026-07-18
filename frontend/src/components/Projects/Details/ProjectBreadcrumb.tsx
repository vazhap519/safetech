import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectBreadcrumb({
    current,
}: {
    current: string;
}) {
    const { locale, translations } = await getSiteSettings();
    const homeLabel = translateText(translations, "nav.home", locale, null);
    const projectsLabel = translateText(
        translations,
        "nav.projects",
        locale,
        null,
    );
    const items = [
        homeLabel ? { href: "/", label: homeLabel } : null,
        projectsLabel ? { href: "/projects", label: projectsLabel } : null,
    ].filter((item): item is { href: string; label: string } =>
        Boolean(item),
    );

    if (!items.length && !current) return null;

    return (
        <nav
            aria-label="Breadcrumb"
            className="font-mono-sm text-mono-sm text-on-surface-variant"
        >
            <ol className="flex flex-wrap items-center gap-2">
                {items.map((item, index) => (
                    <li className="contents" key={item.href}>
                        {index > 0 ? <span aria-hidden="true">/</span> : null}
                        <LocalizedLink
                            className="transition-colors hover:text-primary"
                            href={item.href}
                        >
                            {item.label}
                        </LocalizedLink>
                    </li>
                ))}
                {current ? (
                    <li className="contents">
                        {items.length ? <span aria-hidden="true">/</span> : null}
                        <span
                            aria-current="page"
                            className="text-primary-fixed-dim"
                        >
                            {current}
                        </span>
                    </li>
                ) : null}
            </ol>
        </nav>
    );
}
