import WhoComponent from "@/components/About/WhoComponent";
import WhyComponent from "@/components/About/WhyComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateIndexedSection } from "@/lib/translations";

const sectionConfig = {
    who: {
        icons: ["security", "lan", "dns"],
        itemGrid: "md:grid-cols-3",
        ItemComponent: WhoComponent,
    },
    why: {
        icons: ["architecture", "verified", "support_agent", "hub"],
        itemGrid: "sm:grid-cols-2 lg:grid-cols-4",
        ItemComponent: WhyComponent,
    },
} as const;

export default async function AboutFeatureSection({
    variant,
}: {
    variant: keyof typeof sectionConfig;
}) {
    const { locale, translations } = await getSiteSettings();
    const config = sectionConfig[variant];
    const { title, description, items } = translateIndexedSection(
        translations,
        locale,
        `about.${variant}`,
        config.icons,
    );
    const ItemComponent = config.ItemComponent;

    if (!title && !description && !items.length) return null;

    return (
        <section className="py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title || description ? (
                    <div className="mb-unit-xl text-center">
                        {title ? (
                            <Typography as="h2" variant="section-title">
                                {title}
                            </Typography>
                        ) : null}
                        {description ? (
                            <Typography as="p" variant="section-description">
                                {description}
                            </Typography>
                        ) : null}
                    </div>
                ) : null}

                {items.length ? (
                    <div className={`grid grid-cols-1 gap-gutter ${config.itemGrid}`}>
                        {items.map(({ key, ...item }) => (
                            <ItemComponent key={key} {...item} />
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
