import { notFound } from "next/navigation";

import ShopFilterPanel from "@/components/Shop/ShopFilterPanel";
import ShopProductCard from "@/components/Shop/ShopProductCard";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import {
    getBackendProductCategories,
    getBackendProductFilters,
    getBackendProducts,
} from "@/lib/backend";
import { extractProductFilters, firstSearchParamValue } from "@/lib/products";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

type ShopPageContentProps = {
    searchParams?:
        | Promise<Record<string, string | string[] | undefined>>
        | Record<string, string | string[] | undefined>;
    showPageSchema?: boolean;
};

export default async function ShopPageContent({
    searchParams,
    showPageSchema = true,
}: ShopPageContentProps) {
    const params = (await searchParams) ?? {};
    const settings = await getSiteSettings();

    if (!settings.features.shopEnabled) {
        notFound();
    }

    const activeCategory = firstSearchParamValue(params.category) || "all";
    const activeFilters = extractProductFilters(params);
    const [categories, filters, products] = await Promise.all([
        getBackendProductCategories(),
        getBackendProductFilters(activeCategory),
        getBackendProducts({
            category: activeCategory,
            filters: activeFilters,
        }),
    ]);

    if (!products.length && !categories.length && !filters.length) {
        notFound();
    }

    const t = createTranslator(settings.translations, settings.locale);
    const eyebrow = t("shop.hero.eyebrow", {
        ka: "დინამიკური კატალოგი",
        en: "Dynamic Catalog",
        ru: "Динамический каталог",
    });
    const title = t("shop.hero.title", {
        ka: "მაღაზია",
        en: "Shop",
        ru: "Магазин",
    });
    const description = t("shop.hero.description", {
        ka: "აირჩიეთ პროდუქტები კატეგორიებითა და ფილტრებით. ფასები, აღწერები და SEO სრულად იმართება ადმინ პანელიდან.",
        en: "Browse products by category and dynamic filters. Pricing, content, and SEO are managed entirely from the admin panel.",
        ru: "Просматривайте товары по категориям и динамическим фильтрам. Цены, контент и SEO полностью управляются из админ-панели.",
    });
    const browseLabel = t("shop.card.cta", {
        ka: "დეტალების ნახვა",
        en: "View details",
        ru: "Подробнее",
    });
    const contactPriceLabel = t("shop.price.contact", {
        ka: "ფასის დასაზუსტებლად დაგვიკავშირდით",
        en: "Contact us for pricing",
        ru: "Свяжитесь для уточнения цены",
    });
    const emptyTitle = t("shop.empty.title", {
        ka: "შედეგები ვერ მოიძებნა",
        en: "No products found",
        ru: "Товары не найдены",
    });
    const emptyDescription = t("shop.empty.description", {
        ka: "შეცვალეთ ფილტრები ან კატეგორია, რომ სხვა პროდუქტებიც გამოჩნდეს.",
        en: "Adjust filters or category selection to see more products.",
        ru: "Измените фильтры или категорию, чтобы увидеть больше товаров.",
    });

    return (
        <>
            {showPageSchema ? <CmsPageSchema pageKey="shop" /> : null}

            <section className="relative overflow-hidden border-b border-outline-variant/10 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.18),transparent_40%),linear-gradient(180deg,#08111f_0%,#0f172a_100%)] pt-[clamp(6.5rem,10vw,8rem)] text-white">
                <div className="mx-auto max-w-container-max px-5 pb-12 md:px-8 md:pb-14 xl:px-10 2xl:px-14">
                    <div className="max-w-3xl space-y-5">
                        <p className="text-xs font-semibold uppercase tracking-[0.28em] text-secondary">
                            {eyebrow}
                        </p>
                        <h1 className="max-w-4xl text-[clamp(2.25rem,5vw,4.75rem)] font-semibold leading-[0.95] tracking-tight">
                            {title}
                        </h1>
                        <p className="max-w-2xl text-[clamp(1rem,1.6vw,1.125rem)] leading-7 text-white/78 sm:leading-8">
                            {description}
                        </p>
                    </div>
                </div>
            </section>

            <section className="bg-background py-8 md:py-12 xl:py-14">
                <div className="mx-auto grid max-w-container-max gap-8 px-5 md:px-8 xl:grid-cols-[minmax(0,320px)_minmax(0,1fr)] xl:items-start xl:px-10 2xl:px-14">
                    <ShopFilterPanel
                        activeCategory={activeCategory}
                        activeFilters={activeFilters}
                        categories={categories}
                        filters={filters}
                        resultCount={products.length}
                    />

                    <div className="space-y-6">
                        {products.length ? (
                            <div className="grid auto-rows-fr gap-5 md:grid-cols-2 xl:gap-6 2xl:grid-cols-3">
                                {products.map((product, index) => (
                                    <ShopProductCard
                                        contactPriceLabel={contactPriceLabel}
                                        ctaLabel={browseLabel}
                                        key={product.slug}
                                        locale={settings.locale}
                                        priority={index < 2}
                                        product={product}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-[28px] border border-dashed border-outline-variant/30 bg-surface-container-low px-8 py-14 text-center">
                                <h2 className="text-2xl font-semibold text-on-surface">
                                    {emptyTitle}
                                </h2>
                                <p className="mt-3 text-base leading-7 text-on-surface-variant">
                                    {emptyDescription}
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </section>
        </>
    );
}
