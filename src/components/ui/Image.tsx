"use client";

import { useState, type SyntheticEvent } from "react";

import NextImage, { type ImageProps as NextImageProps } from "next/image";

import clsx from "clsx";

type ImageProps = {
    variant?:
        | "home-hero"
        | "home-trust"
        | "home-infrastructure"
        | "home-project"
        | "contact-intro"
        | "avatar"
        | "full"
        | "contact-support"
        | "Service-showCase";

    fallbackSrc?: string;

    className?: string;
} & NextImageProps;

function getSourceKey(src: NextImageProps["src"]) {
    if (typeof src === "string") {
        return src;
    }

    if ("src" in src) {
        return src.src;
    }

    return src.default.src;
}

export default function Image({
    variant = "full",
    fallbackSrc,
    className,
    alt,
    ...props
}: ImageProps) {
    const resolvedFallback =
        fallbackSrc || (variant === "avatar" ? "/team-avatar.svg" : "/brand-preview.svg");
    const sourceKey = getSourceKey(props.src);
    const [failedSourceKey, setFailedSourceKey] = useState<string | null>(null);
    const resolvedSrc = failedSourceKey === sourceKey ? resolvedFallback : props.src;
    const isSvg =
        typeof resolvedSrc === "string" && /\.svg($|\?)/i.test(resolvedSrc);
    const dimensions = props.fill
        ? {}
        : {
              width: props.width ?? 512,
              height: props.height ?? 512,
          };

    function handleError(
        event: SyntheticEvent<HTMLImageElement, Event>,
    ) {
        props.onError?.(event);

        if (sourceKey !== resolvedFallback) {
            setFailedSourceKey(sourceKey);
        }
    }

    return (
        <NextImage
            alt={alt}
            className={clsx(
                /*
                |--------------------------------------------------------------------------
                | BASE
                |--------------------------------------------------------------------------
                */

                `
                object-cover
                select-none
                `,

                /*
                |--------------------------------------------------------------------------
                | HOME HERO IMAGE
                |--------------------------------------------------------------------------
                */

                variant === "home-hero" &&
                    `
                    rounded-[28px]
                    glass-card
                    shadow-2xl
                    object-cover
                    `,

                /*
                |--------------------------------------------------------------------------
                | HOME TRUST IMAGE
                |--------------------------------------------------------------------------
                */

                variant === "home-trust" &&
                    `
                    relative
                    z-10
                    w-full
                    rounded-[28px]
                    glass-card
                    shadow-2xl
                    object-cover
                    max-h-[720px]
                    `,

                /*
                |--------------------------------------------------------------------------
                | HOME INFRASTRUCTURE IMAGE
                |--------------------------------------------------------------------------
                */

                variant === "home-infrastructure" &&
                    `
                    relative
                    z-10
                    w-full
                    rounded-[28px]
                    glass-card
                    shadow-2xl
                    object-cover
                    max-h-[720px]
                    `,

                /*
                |--------------------------------------------------------------------------
                | HOME PROJECT IMAGE
                |--------------------------------------------------------------------------
                */

                variant === "home-project" &&
                    `
                    w-full
                    h-full
                    object-cover
                    transition-transform
                    duration-700
                    group-hover:scale-110
                    `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT INTRO IMAGE
                |--------------------------------------------------------------------------
                */

                variant === "contact-intro" &&
                    `
                    w-full
                    aspect-video
                    object-cover
                    rounded-lg
                    transition-transform
                    duration-700
                    group-hover:scale-105
                    `,

                /*
                |--------------------------------------------------------------------------
                | AVATAR
                |--------------------------------------------------------------------------
                */

                variant === "avatar" &&
                    `
                    rounded-full
                    object-cover
                    `,

                /*
                |--------------------------------------------------------------------------
                | FULL
                |--------------------------------------------------------------------------
                */

                variant === "full" &&
                    `
                    w-full
                    h-full
                    `,

                variant === "contact-support" &&
                    `
                    w-full
                    h-full
                    object-cover
                    rounded-2xl
                    shadow-2xl
                    border
                    border-outline-variant/20
                    `,

                variant === "Service-showCase" &&
                    `
                    w-full
                    h-64
                    object-cover
                    transition-transform
                    duration-500
                    group-hover:scale-105
                    `,

                className,
            )}
            {...dimensions}
            {...props}
            onError={handleError}
            src={resolvedSrc}
            unoptimized={props.unoptimized ?? isSvg}
        />
    );
}
