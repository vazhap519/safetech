import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Hero() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "contact.hero.title", locale, {
        ka: "დაგვიკავშირდით",
        en: "Contact SafeTech",
        ru: "Свяжитесь с SafeTech",
    });
    const description = translateText(
        translations,
        "contact.hero.description",
        locale,
        {
            ka: "მოგვიყევით თქვენი ობიექტის, სისტემის ან IT მხარდაჭერის საჭიროების შესახებ.",
            en: "Tell us about your site, system, or IT support needs.",
            ru: "Расскажите о вашем объекте, системе или потребностях в IT-поддержке.",
        },
    );
    const buttonLabel = translateText(
        translations,
        "contact.hero.button",
        locale,
        {
            ka: "მოთხოვნის გაგზავნა",
            en: "Send an enquiry",
            ru: "Отправить запрос",
        },
    );

    if (!title && !description && !buttonLabel) return null;

    return (
        <section className="hero-mesh topology-grid relative flex min-h-[70vh] items-center justify-center overflow-hidden px-4 pt-24 md:min-h-[75vh] md:pt-28">
            <div className="pointer-events-none absolute inset-0">
                <div className="animated-glow absolute left-1/4 top-1/4 h-48 w-48 rounded-full bg-primary/10 blur-[80px] md:h-96 md:w-96 md:blur-[120px]" />
                <div
                    className="animated-glow absolute bottom-1/4 right-1/4 h-32 w-32 rounded-full bg-secondary/10 blur-[60px] md:h-64 md:w-64 md:blur-[100px]"
                    style={{ animationDelay: "-2s" }}
                />
            </div>

            <div className="relative z-10 mx-auto max-w-container-max px-margin-desktop text-center">
                {title ? (
                    <Typography
                        as="h1"
                        className="mx-auto max-w-5xl"
                        variant="contact-title"
                    >
                        {title}
                    </Typography>
                ) : null}

                {description ? (
                    <Typography
                        as="p"
                        className="mx-auto mb-unit-lg max-w-2xl"
                        variant="description"
                    >
                        {description}
                    </Typography>
                ) : null}

                {buttonLabel ? (
                    <div className="flex justify-center">
                        <ActionLink
                            className="rounded-full px-unit-lg py-4"
                            href="#contact-form"
                        >
                            {buttonLabel}
                        </ActionLink>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
