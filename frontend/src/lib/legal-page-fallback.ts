import type { BackendPage } from "@/lib/backend";
import type { Locale } from "@/lib/locales";

type LegalSlug = "privacy" | "terms";

const fallbackCopy: Record<
    LegalSlug,
    Record<Locale, { title: string; excerpt: string; content: string }>
> = {
    privacy: {
        ka: {
            title: "კონფიდენციალურობის პოლიტიკა",
            excerpt: "კონფიდენციალურობის სრული ტექსტი დროებით მიუწვდომელია.",
            content:
                "SafeTech-ის მიერ მართული კონფიდენციალურობის პოლიტიკის სრული ტექსტი დროებით ვერ ჩაიტვირთა. გთხოვთ, მოგვიანებით სცადოთ ხელახლა ან დაგვიკავშირდეთ კონფიდენციალურობასთან დაკავშირებული საკითხებისთვის.",
        },
        en: {
            title: "Privacy Policy",
            excerpt: "The full privacy policy is temporarily unavailable.",
            content:
                "SafeTech's managed privacy policy could not be loaded temporarily. Please try again later or contact us with any privacy-related questions.",
        },
        ru: {
            title: "Политика конфиденциальности",
            excerpt: "Полный текст политики конфиденциальности временно недоступен.",
            content:
                "Управляемая SafeTech политика конфиденциальности временно не загрузилась. Пожалуйста, повторите попытку позже или свяжитесь с нами по вопросам конфиденциальности.",
        },
    },
    terms: {
        ka: {
            title: "მომსახურების პირობები",
            excerpt: "მომსახურების პირობების სრული ტექსტი დროებით მიუწვდომელია.",
            content:
                "SafeTech-ის მიერ მართული მომსახურების პირობების სრული ტექსტი დროებით ვერ ჩაიტვირთა. გთხოვთ, მოგვიანებით სცადოთ ხელახლა ან დაგვიკავშირდეთ პირობებთან დაკავშირებული საკითხებისთვის.",
        },
        en: {
            title: "Terms of Service",
            excerpt: "The full terms of service are temporarily unavailable.",
            content:
                "SafeTech's managed terms of service could not be loaded temporarily. Please try again later or contact us with any questions about the terms.",
        },
        ru: {
            title: "Условия обслуживания",
            excerpt: "Полный текст условий обслуживания временно недоступен.",
            content:
                "Управляемые SafeTech условия обслуживания временно не загрузились. Пожалуйста, повторите попытку позже или свяжитесь с нами по вопросам условий.",
        },
    },
};

export function getLegalPageFallback(slug: LegalSlug, locale: Locale): BackendPage {
    const copy = fallbackCopy[slug][locale] ?? fallbackCopy[slug].ka;

    return {
        id: 0,
        slug,
        title: copy.title,
        excerpt: copy.excerpt,
        content: copy.content,
        seo: {
            title: copy.title,
            description: copy.excerpt,
            noindex: true,
        },
    };
}
