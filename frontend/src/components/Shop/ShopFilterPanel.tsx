"use client";

import { startTransition, useMemo, useTransition } from "react";
import { usePathname, useRouter, useSearchParams } from "next/navigation";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import type {
    BackendProductCategory,
    BackendProductFilter,
} from "@/lib/backend";

type ShopFilterPanelProps = {
    categories: BackendProductCategory[];
    filters: BackendProductFilter[];
    activeCategory?: string;
    activeFilters: Record<string, string[]>;
    resultCount: number;
};

export default function ShopFilterPanel({
    categories,
    filters,
    activeCategory = "all",
    activeFilters,
    resultCount,
}: ShopFilterPanelProps) {
    const { t } = useLocalization();
    const pathname = usePathname();
    const router = useRouter();
    const searchParams = useSearchParams();
    const [isPending, beginTransition] = useTransition();
    const allLabel = t("filters.all", {
        ka: "ყველა",
        en: "All",
        ru: "Все",
    });
    const filtersTitle = t("shop.filters.title", {
        ka: "ფილტრები",
        en: "Filters",
        ru: "Фильтры",
    });
    const clearLabel = t("shop.filters.clear", {
        ka: "ფილტრების გასუფთავება",
        en: "Clear filters",
        ru: "Сбросить фильтры",
    });
    const resultsLabel = t("shop.results.count", {
        ka: `${resultCount} პროდუქტი`,
        en: `${resultCount} products`,
        ru: `${resultCount} товаров`,
    });
    const hasActiveFilters = activeCategory !== "all" || Object.keys(activeFilters).length > 0;
    const categoryItems = useMemo(
        () => [{ name: allLabel, slug: "all" }, ...categories],
        [allLabel, categories],
    );

    function replaceSearchParams(mutator: (params: URLSearchParams) => void) {
        const params = new URLSearchParams(searchParams?.toString() || "");
        mutator(params);
        const query = params.toString();
        const nextUrl = query ? `${pathname}?${query}` : pathname;

        beginTransition(() => {
            startTransition(() => {
                router.replace(nextUrl, { scroll: false });
            });
        });
    }

    function setCategory(nextCategory: string) {
        replaceSearchParams((params) => {
            if (!nextCategory || nextCategory === "all") {
                params.delete("category");
                return;
            }

            params.set("category", nextCategory);
        });
    }

    function toggleOption(filterSlug: string, optionSlug: string) {
        replaceSearchParams((params) => {
            const key = `filter_${filterSlug}`;
            const current = (params.get(key) || "")
                .split(",")
                .map((value) => value.trim())
                .filter(Boolean);
            const values = new Set(current);

            if (values.has(optionSlug)) {
                values.delete(optionSlug);
            } else {
                values.add(optionSlug);
            }

            if (values.size === 0) {
                params.delete(key);
                return;
            }

            params.set(key, [...values].join(","));
        });
    }

    function clearAll() {
        replaceSearchParams((params) => {
            params.delete("category");

            [...params.keys()]
                .filter((key) => key.startsWith("filter_"))
                .forEach((key) => params.delete(key));
        });
    }

    return (
        <aside
            className={`space-y-6 rounded-[28px] border border-outline-variant/20 bg-surface/80 p-5 shadow-xl backdrop-blur-xl xl:sticky xl:top-24 xl:p-6 ${
                isPending ? "opacity-70" : ""
            }`}
        >
            <div className="space-y-2">
                <p className="text-xs font-semibold uppercase tracking-[0.24em] text-primary">
                    {filtersTitle}
                </p>
                <p className="text-sm text-on-surface-variant">{resultsLabel}</p>
                {hasActiveFilters ? (
                    <button
                        className="text-sm font-semibold text-secondary transition-colors hover:text-primary"
                        onClick={clearAll}
                        type="button"
                    >
                        {clearLabel}
                    </button>
                ) : null}
            </div>

            <section className="space-y-3">
                <h2 className="text-base font-semibold text-on-surface">
                    {t("shop.categories.title", {
                        ka: "კატეგორიები",
                        en: "Categories",
                        ru: "Категории",
                    })}
                </h2>
                <div className="flex flex-wrap gap-2">
                    {categoryItems.map((category) => {
                        const active = category.slug === (activeCategory || "all");

                        return (
                            <button
                                aria-pressed={active}
                                className={`rounded-full border px-4 py-2 text-sm font-medium transition-colors ${
                                    active
                                        ? "border-primary bg-primary text-on-primary"
                                        : "border-outline-variant/30 bg-surface-container-low text-on-surface hover:border-primary/40 hover:text-primary"
                                }`}
                                key={category.slug}
                                onClick={() => setCategory(category.slug)}
                                type="button"
                            >
                                {category.name}
                            </button>
                        );
                    })}
                </div>
            </section>

            {filters.map((filter) => (
                <section className="space-y-3" key={filter.slug}>
                    <h2 className="text-base font-semibold text-on-surface">
                        {filter.name}
                    </h2>
                    <div className="flex flex-wrap gap-2">
                        {filter.options.map((option) => {
                            const active = (
                                activeFilters[filter.slug] || []
                            ).includes(option.slug);

                            return (
                                <button
                                    aria-pressed={active}
                                    className={`rounded-full border px-4 py-2 text-sm transition-colors ${
                                        active
                                            ? "border-secondary bg-secondary/15 text-secondary"
                                            : "border-outline-variant/30 bg-surface-container-low text-on-surface hover:border-secondary/40 hover:text-secondary"
                                    }`}
                                    key={option.slug}
                                    onClick={() =>
                                        toggleOption(filter.slug, option.slug)
                                    }
                                    type="button"
                                >
                                    {option.name}
                                    {typeof option.count === "number"
                                        ? ` (${option.count})`
                                        : ""}
                                </button>
                            );
                        })}
                    </div>
                </section>
            ))}
        </aside>
    );
}
