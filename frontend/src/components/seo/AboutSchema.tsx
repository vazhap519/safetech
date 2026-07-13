import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function AboutSchema() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const schema = {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        name: t("meta.about.title", {
            ka: "ჩვენ შესახებ | SafeTech გუნდი და გამოცდილება",
            en: "About SafeTech | Team and Experience",
            ru: "О SafeTech | Команда и опыт",
        }),
        url: absoluteLocalizedUrl("/about", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity: {
            "@type": "Organization",
            name: branding.siteName,
            url: absoluteLocalizedUrl("/", locale),
            description: t("meta.about.description", {
                ka: "გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა IT ინფრასტრუქტურისა და უსაფრთხოების პროექტებში.",
                en: "Meet the SafeTech team, expertise, and approach to IT infrastructure and security projects.",
                ru: "Познакомьтесь с командой SafeTech, опытом и подходом к проектам IT-инфраструктуры и безопасности.",
            }),
            logo: absoluteSiteUrl(
                branding.logo || branding.footerLogo || branding.defaultImage,
            ),
        },
    };

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
