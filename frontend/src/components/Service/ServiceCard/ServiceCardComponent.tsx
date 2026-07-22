"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";

type Service = {
    slug: string;
    title: string;
    description: string;
    icon: string;
};

type Props = {
    service: Service;
};

export default function ServiceCardComponent({ service }: Props) {
    const { t } = useLocalization();
    const readMoreLabel = t("common.readMore", {
        ka: "დეტალურად",
        en: "View details",
        ru: "Подробнее",
    });

    return (
        <div className="glass-card flex h-full flex-col rounded-lg p-unit-lg">
            <div className="mb-unit-md flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <Icon className="text-[28px] text-primary" name={service.icon} />
            </div>

            {service.title ? (
                <h3 className="mb-unit-sm font-headline-md text-headline-md">
                    {service.title}
                </h3>
            ) : null}

            {service.description ? (
                <p className="mb-unit-lg flex-grow font-body-md text-body-md text-on-surface-variant">
                    {service.description}
                </p>
            ) : null}

            {service.slug && readMoreLabel ? (
                <LocalizedLink
                    className={`${CARD_ACTION_CLASS} group`}
                    href={`/services/${service.slug}`}
                    prefetch={false}
                >
                    {readMoreLabel}
                    <Icon
                        className="text-[18px] transition-transform group-hover:translate-x-1"
                        name="arrow_forward"
                    />
                </LocalizedLink>
            ) : null}
        </div>
    );
}
