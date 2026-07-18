import ActionLink from "@/components/ui/ActionLink";
import Icon from "@/components/ui/Icon";
import { getBackendContent } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FaqSeqAction() {
    const [content, { locale, translations }] = await Promise.all([
        getBackendContent(),
        getSiteSettings(),
    ]);
    const faqs = (content.faqs ?? []).filter(
        (item) => item.question || item.answer,
    );

    if (!faqs.length) return null;

    const title = translateText(translations, "services.faq.title", locale, null);
    const description = translateText(
        translations,
        "services.faq.description",
        locale,
        null,
    );
    const contactLabel = translateText(
        translations,
        "services.faq.contact",
        locale,
        null,
    );

    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl">
            <div className="grid items-start gap-unit-xl md:grid-cols-2">
                {title || description || contactLabel ? (
                    <div>
                        {title ? (
                            <h2 className="mb-unit-md font-headline-lg text-headline-lg text-on-surface">
                                {title}
                            </h2>
                        ) : null}
                        {description ? (
                            <p className="mb-unit-lg font-body-md text-body-md text-on-surface-variant">
                                {description}
                            </p>
                        ) : null}
                        {contactLabel ? (
                            <ActionLink href="/contact">{contactLabel}</ActionLink>
                        ) : null}
                    </div>
                ) : null}
                <div className="space-y-unit-md">
                    {faqs.map((item, index) => (
                        <details
                            className="glass-card group overflow-hidden rounded-xl"
                            key={`${item.question}-${index}`}
                            open={index === 0}
                        >
                            <summary className="flex cursor-pointer list-none items-center justify-between p-unit-md">
                                <span className="font-headline-md text-[18px]">
                                    {item.question}
                                </span>
                                <Icon
                                    className="transition-transform group-open:rotate-180"
                                    name="expand_more"
                                />
                            </summary>
                            {item.answer ? (
                                <div className="border-t border-outline-variant/10 px-unit-md pb-unit-md pt-4 text-on-surface-variant">
                                    {item.answer}
                                </div>
                            ) : null}
                        </details>
                    ))}
                </div>
            </div>
        </section>
    );
}
