import type { Metadata } from "next";
import { notFound } from "next/navigation";

import ProductGallery from "@/components/Shop/ProductGallery";
import ProductDetailSchema from "@/components/seo/ProductDetailSchema";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getBackendProduct } from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { formatProductPrice, plainTextFromHtml } from "@/lib/products";
import { translateText } from "@/lib/translations";

type ProductPageProps = {
    params: Promise<{ slug: string }>;
};

export async function generateMetadata({
    params,
}: ProductPageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding, locale, seo, translations }, product] = await Promise.all([
        getSiteSettings(),
        getBackendProduct(slug),
    ]);
    const siteName = branding.siteName;

    if (!product) {
        return {
            title: withSiteTitle(
                translateText(
                    translations,
                    "meta.product.notFound",
                    locale,
                    {
                        ka: "პროდუქტი ვერ მოიძებნა",
                        en: "Product not found",
                        ru: "Товар не найден",
                    },
                ),
                siteName,
            ),
            robots: { index: false, follow: false },
        };
    }

    return createMetadata({
        title: product.seo?.title || product.name,
        description:
            product.seo?.description ||
            plainTextFromHtml(product.shortDescription || product.description),
        path: `/shop/${product.slug}`,
        locale,
        keywords: product.seo?.keywords?.length
            ? product.seo.keywords
            : seo.defaultKeywords,
        image:
            product.seo?.image ||
            product.image ||
            branding.defaultImage ||
            undefined,
        siteName,
        canonical: product.seo?.canonical,
        ogTitle: product.seo?.ogTitle,
        ogDescription: product.seo?.ogDescription,
        noindex: Boolean(product.seo?.noindex),
        robotsIndex: seo.robotsIndex,
        robotsFollow: seo.robotsFollow,
    });
}

export default async function ProductPage({ params }: ProductPageProps) {
    const { slug } = await params;
    const [locale, settings, product] = await Promise.all([
        getCurrentLocale(),
        getSiteSettings(),
        getBackendProduct(slug),
    ]);

    if (!settings.features.shopEnabled || !product) {
        notFound();
    }

    const contactPriceLabel = translateText(
        settings.translations,
        "shop.price.contact",
        locale,
        {
            ka: "ფასის დასაზუსტებლად დაგვიკავშირდით",
            en: "Contact us for pricing",
            ru: "Свяжитесь для уточнения цены",
        },
    );
    const priceLabel =
        product.contactForPrice || product.price === null || product.price === undefined
            ? contactPriceLabel
            : formatProductPrice(product.price, product.currency || "GEL", locale);
    const breadcrumbs = [
        {
            href: "/",
            label: translateText(settings.translations, "nav.home", locale, {
                ka: "მთავარი",
                en: "Home",
                ru: "Главная",
            }),
        },
        {
            href: "/shop",
            label: translateText(settings.translations, "nav.shop", locale, {
                ka: "მაღაზია",
                en: "Shop",
                ru: "Магазин",
            }),
        },
        {
            href: `/shop/${product.slug}`,
            label: product.name,
        },
    ].filter((item) => item.label);
    const detailsTitle = translateText(
        settings.translations,
        "shop.details.title",
        locale,
        {
            ka: "სრული აღწერა",
            en: "Full details",
            ru: "Полное описание",
        },
    );
    const categoryTitle = translateText(
        settings.translations,
        "shop.category.title",
        locale,
        {
            ka: "კატეგორია",
            en: "Category",
            ru: "Категория",
        },
    );
    const filtersTitle = translateText(
        settings.translations,
        "shop.filters.title",
        locale,
        {
            ka: "ფილტრები",
            en: "Filters",
            ru: "Фильтры",
        },
    );
    const galleryImages = [
        ...(product.image
            ? [
                  {
                      src: product.image,
                      thumb: product.thumb || product.cardImage || product.image,
                      alt: product.imageAlt || product.name,
                  },
              ]
            : []),
        ...(product.gallery ?? []).filter(
            (image) => image.src && image.src !== product.image,
        ),
    ];

    return (
        <article className="bg-background pt-24">
            <ProductDetailSchema product={product} />

            <section className="mx-auto max-w-container-max space-y-8 px-5 pb-12 md:px-8 md:pb-14 xl:px-10 2xl:px-14">
                <nav aria-label="Breadcrumb" className="pt-4">
                    <ol className="flex flex-wrap gap-2 text-sm text-on-surface-variant">
                        {breadcrumbs.map((item, index) => (
                            <li className="flex items-center gap-2" key={item.href}>
                                {index > 0 ? <span>/</span> : null}
                                {index === breadcrumbs.length - 1 ? (
                                    <span className="text-on-surface">{item.label}</span>
                                ) : (
                                    <LocalizedLink
                                        className="transition-colors hover:text-primary"
                                        href={item.href}
                                        prefetch={false}
                                    >
                                        {item.label}
                                    </LocalizedLink>
                                )}
                            </li>
                        ))}
                    </ol>
                </nav>

                <div className="grid gap-8 xl:grid-cols-[1.05fr_0.95fr]">
                    <div>
                        <ProductGallery images={galleryImages} name={product.name} />
                    </div>

                    <div className="space-y-6 rounded-[32px] border border-outline-variant/15 bg-surface p-5 shadow-xl shadow-black/10 sm:p-6 xl:p-7">
                        {product.category?.name ? (
                            <p className="text-xs font-semibold uppercase tracking-[0.24em] text-primary">
                                {product.category.name}
                            </p>
                        ) : null}

                        <div className="space-y-4">
                            <h1 className="text-[clamp(2rem,3vw,3.5rem)] font-semibold leading-[0.96] tracking-tight text-on-surface">
                                {product.name}
                            </h1>
                            <div
                                className="shop-copy text-[clamp(1rem,1.35vw,1.0625rem)] leading-7 text-on-surface-variant sm:leading-8"
                                dangerouslySetInnerHTML={{
                                    __html: product.shortDescription || product.description,
                                }}
                            />
                        </div>

                        <div className="rounded-2xl bg-primary/7 p-5">
                            <p className="text-sm font-medium text-on-surface-variant">
                                {translateText(
                                    settings.translations,
                                    "shop.price.label",
                                    locale,
                                    {
                                        ka: "ფასი",
                                        en: "Price",
                                        ru: "Цена",
                                    },
                                )}
                            </p>
                            <p className="mt-2 text-2xl font-semibold text-secondary">
                                {priceLabel}
                            </p>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            {product.category?.name ? (
                                <div className="space-y-2">
                                    <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-on-surface-variant">
                                        {categoryTitle}
                                    </h2>
                                    <p className="text-base text-on-surface">
                                        {product.category.name}
                                    </p>
                                </div>
                            ) : null}

                            {product.filters?.length ? (
                                <div className="space-y-3">
                                    <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-on-surface-variant">
                                        {filtersTitle}
                                    </h2>
                                    <div className="flex flex-wrap gap-2">
                                        {product.filters.flatMap((filter) =>
                                            filter.options.map((option) => (
                                                <span
                                                    className="rounded-full border border-outline-variant/20 bg-surface-container-low px-3 py-1.5 text-sm text-on-surface"
                                                    key={`${filter.slug}-${option.slug}`}
                                                >
                                                    {filter.name}: {option.name}
                                                </span>
                                            )),
                                        )}
                                    </div>
                                </div>
                            ) : null}
                        </div>
                    </div>
                </div>

                <section className="rounded-[32px] border border-outline-variant/15 bg-surface px-5 py-7 shadow-xl shadow-black/10 sm:px-6 xl:px-7 xl:py-8">
                    <h2 className="text-[clamp(1.5rem,2vw,2rem)] font-semibold text-on-surface">
                        {detailsTitle}
                    </h2>
                    <div
                        className="shop-prose mt-5 max-w-none"
                        dangerouslySetInnerHTML={{
                            __html: product.details || product.description,
                        }}
                    />
                </section>
            </section>
        </article>
    );
}
