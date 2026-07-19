import JsonLd from "@/components/seo/JsonLd";
import { categorySchemas } from "@/lib/categorySeo";

type Category = {
    name?: string;
    intro_text?: string;
    faq?: Array<Record<string, unknown>>;
    schema?: Record<string, unknown> | Array<Record<string, unknown>>;
};

export default function CategorySeoContent({ category }: { category: Category }) {
    return (
        <>
            <JsonLd data={categorySchemas({ category })} />
            {category.intro_text ? (
                <section className="border-t border-outline-variant/20 bg-surface py-12">
                    <div className="mx-auto max-w-3xl px-4">
                        {category.name ? (
                            <h2 className="mb-4 text-xl font-semibold text-on-surface md:text-2xl">
                                {category.name}
                            </h2>
                        ) : null}
                        <div
                            className="prose max-w-none text-on-surface-variant prose-a:text-secondary"
                            dangerouslySetInnerHTML={{ __html: category.intro_text }}
                        />
                    </div>
                </section>
            ) : null}
        </>
    );
}
