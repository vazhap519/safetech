import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import Icon from "@/components/ui/Icon";
import Link from "next/link";
import { localizeHref } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ProjectDetailsLinkProps = {
    className?: string;
    slug: string;
};

export default async function ProjectDetailsLink({
    className = "",
    slug,
}: ProjectDetailsLinkProps) {
    const { locale, translations } = await getSiteSettings();
    const label = translateText(translations, "common.details", locale, {
        ka: "პროექტის ნახვა",
        en: "View project",
        ru: "Смотреть проект",
    });

    return (
        <Link
            className={`${CARD_ACTION_CLASS} ${className}`.trim()}
            href={localizeHref(`/projects/${slug}`, locale)}
            prefetch={false}
        >
            {label}
            <Icon aria-hidden="true" name="arrow_forward" />
        </Link>
    );
}
