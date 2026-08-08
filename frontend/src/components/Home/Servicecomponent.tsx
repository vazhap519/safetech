"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";

type ServiceCard = {
    slug: string;
    title: string;
    description: string;
    icon: string;
};

export default function Servicecomponent({ service }: { service: ServiceCard }) {
    const { t } = useLocalization();
    const readMoreLabel = t("common.readMore", {
        ka: "დეტალურად",
        en: "View details",
        ru: "Подробнее",
    });

    return (
        <div className="glass-card flex h-full min-w-0 flex-col gap-4 rounded-lg p-5 sm:p-6">
            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <Icon className="!text-[30px]" name={service.icon} />
            </div>
            {service.title ? (
                <h3 className="min-w-0 break-words font-headline-md text-xl leading-snug text-white [overflow-wrap:anywhere] sm:text-headline-md">
                    {service.title}
                </h3>
            ) : null}
            {service.description ? (
                <p className="min-w-0 break-words font-body-md leading-relaxed text-on-surface-variant [overflow-wrap:anywhere]">
                    {service.description}
                </p>
            ) : null}
            {service.slug && readMoreLabel ? (
                <LocalizedLink
                    className={`${CARD_ACTION_CLASS} mt-auto max-w-full break-words`}
                    href={`/services/${service.slug}`}
                    prefetch={false}
                >
                    {readMoreLabel} <span aria-hidden="true">-&gt;</span>
                </LocalizedLink>
            ) : null}
        </div>
    );
}
