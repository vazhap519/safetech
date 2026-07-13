import type { LinkProps } from "next/link";
import type { ReactNode } from "react";
import clsx from "clsx";

import LocalizedLink from "@/components/ui/LocalizedLink";

type ActionLinkProps = LinkProps & {
    children: ReactNode;
    variant?: "primary" | "secondary" | "glass";
    className?: string;
};

export default function ActionLink({
    children,
    variant = "primary",
    className,
    ...props
}: ActionLinkProps) {
    return (
        <LocalizedLink
            className={clsx(
                "inline-flex max-w-full items-center justify-center rounded-xl px-6 py-3 text-center font-medium transition-all duration-300 active:scale-95",
                variant === "primary" &&
                    "bg-primary-container text-on-primary-container shadow-lg shadow-blue-500/20 hover:brightness-110 motion-safe:hover:-translate-y-1",
                variant === "secondary" &&
                    "border border-outline-variant bg-transparent text-on-surface hover:bg-surface-container",
                variant === "glass" &&
                    "glass-card text-on-surface hover:bg-white/10 motion-safe:hover:-translate-y-1",
                className,
            )}
            {...props}
        >
            {children}
        </LocalizedLink>
    );
}
