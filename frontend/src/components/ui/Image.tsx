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
    if (typeof src === "string") return src;
    if ("src" in src) return src.src;

    return src.default.src;
}

export default function Image({
    variant = "full",
    fallbackSrc,
    className,
    alt,
    src,
    priority,
    preload,
    fetchPriority,
    ...props
}: ImageProps) {
    const resolvedFallback =
        fallbackSrc ||
        (variant === "avatar" ? "/team-avatar.svg" : "/brand-preview.svg");
    const sourceKey = getSourceKey(src);
    const resolvedSrc = sourceKey.trim() ? src : resolvedFallback;
    const isSvg =
        typeof resolvedSrc === "string" && /\.svg($|\?)/i.test(resolvedSrc);
    const dimensions = props.fill
        ? {}
        : {
              width: props.width ?? 512,
              height: props.height ?? 512,
          };
    const shouldPreload = preload ?? priority ?? false;

    return (
        <NextImage
            alt={alt}
            className={clsx(
                "object-cover select-none",
                variant === "home-hero" &&
                    "rounded-[28px] glass-card shadow-2xl !object-contain",
                variant === "home-trust" &&
                    "relative z-10 w-full rounded-[28px] glass-card shadow-2xl object-cover max-h-[720px]",
                variant === "home-infrastructure" &&
                    "relative z-10 w-full rounded-[28px] glass-card shadow-2xl object-cover max-h-[720px]",
                variant === "home-project" &&
                    "w-full h-full object-cover transition-transform duration-700 group-hover:scale-110",
                variant === "contact-intro" &&
                    "w-full aspect-video object-cover rounded-lg transition-transform duration-700 group-hover:scale-105",
                variant === "avatar" && "rounded-full object-cover",
                variant === "full" && "w-full h-full",
                variant === "contact-support" &&
                    "w-full h-full object-cover rounded-2xl shadow-2xl border border-outline-variant/20",
                variant === "Service-showCase" &&
                    "w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105",
                className,
            )}
            fetchPriority={
                fetchPriority ?? (shouldPreload ? "high" : undefined)
            }
            preload={shouldPreload}
            {...dimensions}
            {...props}
            src={resolvedSrc}
            unoptimized={props.unoptimized ?? isSvg}
        />
    );
}
