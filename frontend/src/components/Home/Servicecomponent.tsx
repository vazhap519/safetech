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
        <div className="glass-card flex h-full flex-col gap-4 rounded-lg p-6">
            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <Icon className="!text-[30px]" name={service.icon} />
            </div>
            {service.title ? (
                <h3 className="font-headline-md text-headline-md text-white">
                    {service.title}
                </h3>
            ) : null}
            {service.description ? (
                <p className="font-body-md text-on-surface-variant">
                    {service.description}
                </p>
            ) : null}
            {service.slug && readMoreLabel ? (
                <LocalizedLink
                    className={`${CARD_ACTION_CLASS} mt-auto`}
                    href={`/services/${service.slug}`}
                    prefetch={false}
                >
                    {readMoreLabel} <span aria-hidden="true">-&gt;</span>
                </LocalizedLink>
            ) : null}
        </div>
    );
}
