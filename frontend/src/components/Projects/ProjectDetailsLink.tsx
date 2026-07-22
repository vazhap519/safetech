"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";

type ProjectDetailsLinkProps = {
    className?: string;
    slug: string;
};

export default function ProjectDetailsLink({
    className = "",
    slug,
}: ProjectDetailsLinkProps) {
    const { t } = useLocalization();
    const label = t("common.details", {
        ka: "პროექტის ნახვა",
        en: "View project",
        ru: "Смотреть проект",
    });

    return (
        <LocalizedLink
            className={`${CARD_ACTION_CLASS} ${className}`.trim()}
            href={`/projects/${slug}`}
            prefetch={false}
        >
            {label}
            <Icon aria-hidden="true" name="arrow_forward" />
        </LocalizedLink>
    );
}
