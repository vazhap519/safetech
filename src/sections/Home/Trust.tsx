import Typography from "@/components/ui/Typography";
import TrustComponent from "@/components/Home/TrustComponent";

export default function Trust() {
    return (
        <section
            className="
                py-unit-xl
                border-y
                border-outline-variant/10
                bg-surface-container-lowest/50
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-margin-desktop
                "
            >

                {/* Title */}
                <Typography
                    as="h2"
                    variant="footer-title"
                    className="
                        text-center
                        mb-unit-lg
                    "
                >
                    ჩვენი პარტნიორები და კლიენტები
                </Typography>

                {/* Logos */}
                <div
                    className="
                        flex
                        flex-wrap
                        justify-center
                        items-center
                        gap-unit-xl
                        opacity-70
                        grayscale
                        hover:grayscale-0
                        transition-all
                        duration-700
                    "
                >
                    <TrustComponent />
                </div>

            </div>
        </section>
    );
}