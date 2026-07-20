"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";

type ProjectDetailsLinkProps = {
    className?: string;
    slug: string;
};

export default function ProjectDetailsLink({
    className = "inline-flex items-center gap-2 font-bold text-secondary transition-colors hover:text-primary",
    slug,
}: ProjectDetailsLinkProps) {
    const { t } = useLocalization();
    const label = t("common.details", {
        ka: "პროექტის ნახვა",
        en: "View project",
        ru: "Смотреть проект",
    });

    return (
        <LocalizedLink className={className} href={`/projects/${slug}`}>
            {label}
            <Icon aria-hidden="true" name="arrow_forward" />
        </LocalizedLink>
    );
}
