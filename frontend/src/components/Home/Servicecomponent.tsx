"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
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
    const readMoreLabel = t("common.readMore", null);

    return (
        <div className="glass-card flex flex-col gap-unit-md rounded-3xl p-unit-lg">
            <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                <Icon className="!text-[40px]" name={service.icon} />
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
            {readMoreLabel ? (
                <LocalizedLink
                    className="mt-auto inline-flex items-center gap-2 font-label-md text-primary hover:text-secondary"
                    href={`/services/${service.slug}`}
                    prefetch={false}
                >
                    {readMoreLabel} <span aria-hidden="true">-&gt;</span>
                </LocalizedLink>
            ) : null}
        </div>
    );
}
