import TrustComponent from "@/components/Home/TrustComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Trust() {
    const { locale, translations } = await getSiteSettings();

    return (
        <section
            className="
                border-y
                border-outline-variant/10
                bg-surface-container-lowest/50
                py-unit-xl
            "
        >
            <div
                className="
                    mx-auto
                    max-w-container-max
                    px-margin-desktop
                "
            >
                <Typography
                    as="h2"
                    variant="footer-title"
                    className="
                        mb-unit-lg
                        text-center
                    "
                >
                    {translateText(translations, "home.trust.title", locale, {
                        ka: "ჩვენი პარტნიორები და კლიენტები",
                        en: "Our Partners and Clients",
                        ru: "Наши партнеры и клиенты",
                    })}
                </Typography>

                <div
                    className="
                        flex
                        flex-wrap
                        items-center
                        justify-center
                        gap-unit-xl
                        opacity-70
                        grayscale
                        transition-all
                        duration-700
                        hover:grayscale-0
                    "
                >
                    <TrustComponent />
                </div>
            </div>
        </section>
    );
}
