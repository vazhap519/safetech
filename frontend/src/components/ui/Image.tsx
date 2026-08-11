import NextImage, { type ImageProps as NextImageProps } from "next/image";
import clsx from "clsx";

type ImageProps = {
    variant?:
        | "plain"
        | "home-hero"
        | "home-trust"
        | "home-infrastructure"
        | "home-project"
        | "contact-intro"
        | "avatar"
        | "full"
        | "contact-support";
    fallbackSrc?: string;
    className?: string;
} & NextImageProps;

function getSourceKey(src: NextImageProps["src"]) {
    if (typeof src === "string") return src;
    if ("src" in src) return src.src;

    return src.default.src;
}

export default function Image({
    variant = "plain",
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
                "select-none",
                variant === "home-hero" &&
                    "rounded-[28px] glass-card shadow-2xl",
                variant === "home-trust" &&
                    "relative z-10 rounded-[28px] glass-card shadow-2xl",
                variant === "home-infrastructure" &&
                    "relative z-10 rounded-[28px] glass-card shadow-2xl",
                variant === "home-project" &&
                    "transition-transform duration-700 group-hover:scale-110",
                variant === "contact-intro" &&
                    "rounded-lg transition-transform duration-700 group-hover:scale-105",
                variant === "avatar" && "rounded-full object-cover",
                variant === "full" && "object-cover",
                variant === "contact-support" &&
                    "rounded-2xl border border-outline-variant/20 shadow-2xl",
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
