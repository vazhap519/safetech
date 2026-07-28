import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { BackendProduct } from "@/lib/backend";
import type { Locale } from "@/lib/locales";
import { formatProductPrice } from "@/lib/products";

export default function ShopProductCard({
    contactPriceLabel,
    ctaLabel,
    locale,
    priority = false,
    product,
}: {
    contactPriceLabel: string;
    ctaLabel: string;
    locale: Locale;
    priority?: boolean;
    product: BackendProduct;
}) {
    const priceLabel =
        product.contactForPrice || product.price === null || product.price === undefined
            ? contactPriceLabel
            : formatProductPrice(product.price, product.currency || "GEL", locale);
    const previewImage = product.cardImage || product.thumb || product.image;
    const galleryCount = (product.gallery?.length || 0) + (product.image ? 1 : 0);

    return (
        <article className="shop-card group flex h-full flex-col overflow-hidden rounded-[28px] border border-outline-variant/20 bg-surface shadow-xl shadow-black/10 transition-transform hover:-translate-y-1">
            <LocalizedLink className="block" href={`/shop/${product.slug}`} prefetch={false}>
                <div className="relative aspect-[5/4] overflow-hidden bg-surface-container-low">
                    {previewImage ? (
                        <Image
                            alt={product.imageAlt || product.name}
                            className="transition-transform duration-500 group-hover:scale-105"
                            fill
                            priority={priority}
                            sizes="(max-width: 640px) 100vw, (max-width: 1280px) 50vw, 360px"
                            src={previewImage}
                        />
                    ) : (
                        <div className="grid h-full place-items-center text-sm text-on-surface-variant">
                            {product.name}
                        </div>
                    )}
                    {galleryCount > 1 ? (
                        <div className="absolute right-3 top-3 rounded-full bg-black/55 px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                            +{galleryCount - 1}
                        </div>
                    ) : null}
                </div>
            </LocalizedLink>

            <div className="flex flex-1 flex-col space-y-4 p-5 sm:p-6">
                {product.category?.name ? (
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-primary">
                        {product.category.name}
                    </p>
                ) : null}

                <div className="space-y-3">
                    <h2 className="text-[clamp(1.25rem,1rem+1vw,1.75rem)] font-semibold leading-tight text-on-surface">
                        <LocalizedLink href={`/shop/${product.slug}`} prefetch={false}>
                            {product.name}
                        </LocalizedLink>
                    </h2>
                    <div
                        className="line-clamp-3 text-sm leading-6 text-on-surface-variant"
                        dangerouslySetInnerHTML={{
                            __html: product.shortDescription || product.description,
                        }}
                    />
                </div>

                <div className="mt-auto flex flex-col gap-3 border-t border-outline-variant/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p className="text-sm font-semibold text-secondary sm:text-base">
                        {priceLabel}
                    </p>
                    <LocalizedLink
                        className="inline-flex min-h-11 items-center justify-center rounded-full bg-primary px-5 py-2 text-sm font-semibold text-on-primary transition-colors hover:brightness-110"
                        href={`/shop/${product.slug}`}
                        prefetch={false}
                    >
                        {ctaLabel}
                    </LocalizedLink>
                </div>
            </div>
        </article>
    );
}
