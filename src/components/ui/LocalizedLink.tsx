"use client";

import Link, { type LinkProps } from "next/link";
import type { AnchorHTMLAttributes, ReactNode } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { localizeHref } from "@/lib/seo";

type LocalizedLinkProps = LinkProps &
    Omit<AnchorHTMLAttributes<HTMLAnchorElement>, "href"> & {
        children: ReactNode;
    };

export default function LocalizedLink({
    href,
    children,
    ...props
}: LocalizedLinkProps) {
    const { locale } = useLocalization();

    return (
        <Link href={localizeHref(href, locale)} {...props}>
            {children}
        </Link>
    );
}
