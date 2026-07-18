import FeaturedCardComponent from "@/components/Service/Featured/Cards/FeaturedCardComponent";
import FeatureCardTypographyComponent from "@/components/Service/Featured/Typography/FeatureCardTypographyComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText } from "@/lib/translations";

const featuredKeys = [
    "services.featured.title",
    "services.featured.card.title",
    "services.featured.card.description",
    "services.featured.card.imageAlt",
];

export default async function FeaturedSection() {
    const { locale, translations } = await getSiteSettings();

    if (!hasTranslatedText(translations, featuredKeys, locale)) {
        return null;
    }

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <FeatureCardTypographyComponent />
                <div className="grid gap-unit-lg md:grid-cols-3">
                    <FeaturedCardComponent />
                </div>
            </div>
        </section>
    );
}
