import type { ComponentProps, ReactNode } from "react";
import clsx from "clsx";

import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";

type TypographyVariant = ComponentProps<typeof Typography>["variant"];

export type CmsCtaAction = {
    label?: ReactNode;
    href?: string;
    variant?: "primary" | "secondary" | "glass" | "outline";
    className?: string;
    consultation?: boolean;
    ariaLabel?: string;
};

type RenderableCmsCtaAction = CmsCtaAction &
    ({ consultation: true } | { href: string });

type CmsCtaSectionProps = {
    title?: string | null;
    description?: string | null;
    actions?: CmsCtaAction[];
    titleId?: string;
    titleVariant?: TypographyVariant;
    descriptionVariant?: TypographyVariant;
    sectionClassName?: string;
    panelClassName?: string;
    contentClassName?: string;
    titleClassName?: string;
    descriptionClassName?: string;
    actionsClassName?: string;
    sectionBackground?: ReactNode;
    panelBackground?: ReactNode;
    afterPanel?: ReactNode;
};

const actionVariantClassNames = {
    primary:
        "rounded-xl bg-primary-container px-6 py-3 font-medium text-on-primary-container shadow-lg shadow-blue-500/20 hover:brightness-110 motion-safe:hover:-translate-y-1",
    secondary:
        "rounded-xl border border-outline-variant bg-transparent px-6 py-3 font-medium text-on-surface hover:bg-surface-container",
    glass: "glass-card rounded-xl px-6 py-3 font-medium text-on-surface hover:bg-white/10 motion-safe:hover:-translate-y-1",
    outline:
        "rounded-2xl border border-outline-variant bg-surface-variant/20 px-8 py-5 font-label-md text-on-surface transition-colors hover:bg-surface-variant/40",
};

function isRenderable(value: ReactNode) {
    if (value === null || value === undefined || value === false) return false;
    if (typeof value === "string") return value.trim().length > 0;

    return true;
}

function getRenderableActions(
    actions: CmsCtaAction[] = [],
): RenderableCmsCtaAction[] {
    return actions.filter(
        (action): action is RenderableCmsCtaAction =>
            isRenderable(action.label) &&
            Boolean(action.consultation || action.href),
    );
}

export default function CmsCtaSection({
    title,
    description,
    actions = [],
    titleId,
    titleVariant,
    descriptionVariant,
    sectionClassName,
    panelClassName,
    contentClassName,
    titleClassName,
    descriptionClassName,
    actionsClassName,
    sectionBackground,
    panelBackground,
    afterPanel,
}: CmsCtaSectionProps) {
    const visibleActions = getRenderableActions(actions);

    if (!title && !description && !visibleActions.length) return null;

    return (
        <section
            aria-labelledby={title && titleId ? titleId : undefined}
            className={sectionClassName}
        >
            {sectionBackground}
            <div className={panelClassName}>
                {panelBackground}
                <div className={contentClassName}>
                    {title ? (
                        titleVariant ? (
                            <Typography
                                as="h2"
                                className={titleClassName}
                                id={titleId}
                                variant={titleVariant}
                            >
                                {title}
                            </Typography>
                        ) : (
                            <h2 className={titleClassName} id={titleId}>
                                {title}
                            </h2>
                        )
                    ) : null}

                    {description ? (
                        descriptionVariant ? (
                            <Typography
                                as="p"
                                className={descriptionClassName}
                                variant={descriptionVariant}
                            >
                                {description}
                            </Typography>
                        ) : (
                            <p className={descriptionClassName}>{description}</p>
                        )
                    ) : null}

                    {visibleActions.length ? (
                        <div className={actionsClassName}>
                            {visibleActions.map((action, index) => {
                                const className = clsx(
                                    "inline-flex max-w-full items-center justify-center text-center transition-all duration-300 active:scale-95",
                                    actionVariantClassNames[
                                        action.variant ?? "primary"
                                    ],
                                    action.className,
                                );

                                if (action.consultation) {
                                    return (
                                        <ConsultationTrigger
                                            key={index}
                                            aria-label={action.ariaLabel}
                                            className={className}
                                        >
                                            {action.label}
                                        </ConsultationTrigger>
                                    );
                                }

                                return (
                                    <LocalizedLink
                                        key={index}
                                        aria-label={action.ariaLabel}
                                        className={className}
                                        href={action.href}
                                    >
                                        {action.label}
                                    </LocalizedLink>
                                );
                            })}
                        </div>
                    ) : null}
                </div>
            </div>
            {afterPanel}
        </section>
    );
}
