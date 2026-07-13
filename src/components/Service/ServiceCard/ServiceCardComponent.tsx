"use client";

import LocalizedLink from "@/components/ui/LocalizedLink";

import Icon from "@/components/ui/Icon";
import { useLocalization } from "@/components/providers/LocalizationProvider";

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

    return (
        <div className="glass-card flex h-full flex-col rounded-2xl p-unit-lg">
            <div className="mb-unit-md flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                <Icon className="text-[28px] text-primary" name={service.icon} />
            </div>

            <h3 className="mb-unit-sm font-headline-md text-headline-md">
                {service.title}
            </h3>

            <p className="mb-unit-lg flex-grow font-body-md text-body-md text-on-surface-variant">
                {service.description}
            </p>

            <LocalizedLink
                className="group inline-flex items-center gap-2 font-label-md text-label-md text-primary"
                href={`/services/${service.slug}`}
            >
                {t("common.readMore", {
                    ka: "დეტალურად",
                    en: "Read more",
                    ru: "Подробнее",
                })}
                <Icon
                    className="text-[18px] transition-transform group-hover:translate-x-1"
                    name="arrow_forward"
                />
            </LocalizedLink>
        </div>
    );
}
