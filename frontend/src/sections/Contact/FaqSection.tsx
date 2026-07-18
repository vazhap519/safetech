import Faq from "@/components/Contact/Faq/Faq";
import Typography from "@/components/ui/Typography";
import { getBackendContent } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FaqSection() {
    const [content, { locale, translations }] = await Promise.all([
        getBackendContent(),
        getSiteSettings(),
    ]);
    const faqs = (content.faqs ?? [])
        .filter((faq) => !faq.context || faq.context === "contact")
        .filter((faq) => faq.question || faq.answer);
    const title = translateText(translations, "contact.faq.title", locale, null);

    if (!title && !faqs.length) return null;

    return (
        <section className="bg-surface-container-low py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title ? (
                    <Typography
                        as="h2"
                        className="mx-auto max-w-4xl"
                        variant="contact-faq"
                    >
                        {title}
                    </Typography>
                ) : null}

                {faqs.length ? (
                    <div className="mx-auto max-w-3xl space-y-4 md:space-y-unit-md">
                        {faqs.map((faq, index) => (
                            <Faq
                                answer={faq.answer}
                                defaultOpen={index === 0}
                                key={`${faq.question}-${index}`}
                                question={faq.question}
                            />
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
