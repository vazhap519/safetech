import JsonLd from "@/components/seo/JsonLd";
import { categorySchemas } from "@/lib/categorySeo";
import type { Locale } from "@/lib/locales";

type Category = {
    name?: string;
    intro_text?: string;
    faq?: Array<Record<string, unknown>>;
    schema?: Record<string, unknown> | Array<Record<string, unknown>>;
};

export default async function CategorySeoContent({
    category,
    locale,
    path,
}: {
    category: Category;
    locale: Locale;
    path: string;
}) {
    return (
        <>
            <JsonLd data={categorySchemas({ category, path, locale })} />
            {category.name || category.intro_text ? (
                <section className="relative overflow-hidden border-b border-outline-variant/20 bg-surface px-5 pb-12 pt-28 md:px-8 md:pb-16 md:pt-32 xl:px-14">
                    <div className="mx-auto max-w-container-max">
                        <div className="max-w-4xl">
                            {category.name ? (
                                <h1 className="text-[36px] font-semibold leading-[1.12] text-on-surface sm:text-[44px] md:text-[54px]">
                                    {category.name}
                                </h1>
                            ) : null}
                            {category.intro_text ? (
                                <div
                                    className="prose mt-6 max-w-3xl text-base leading-8 text-on-surface-variant prose-a:text-secondary md:text-lg"
                                    dangerouslySetInnerHTML={{ __html: category.intro_text }}
                                />
                            ) : null}
                        </div>
                    </div>
                </section>
            ) : null}
        </>
    );
}
