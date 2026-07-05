
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import Items from "@/components/Contact/support/Items";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Support() {
    const { branding } = await getSiteSettings();

    return (
        <section
            className="
                py-unit-xl
                relative
                overflow-hidden
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-margin-desktop

                    grid
                    grid-cols-1
                    lg:grid-cols-2

                    gap-unit-lg
                    lg:gap-unit-xl

                    items-center
                "
            >
                {/* Image */}
                <div
                    className="
                        relative
                        order-1
                    "
                >
                    <Image
                        variant="contact-support"
                        src={branding.defaultImage}
                        alt="Security monitoring dashboard"
                        sizes="(max-width: 768px) 100vw, 50vw"

                    />

                    {/* Floating Badge */}
                    <div
                        className="
                            absolute

                            bottom-4
                            right-4

                            md:bottom-6
                            md:right-6

                            glass-panel
                            p-3
                            md:p-4

                            rounded-xl
                            border-secondary/30
                        "
                    >
                        <div
                            className="
                                flex
                                items-center
                                gap-2
                            "
                        >
                            <div
                                className="
                                    w-2
                                    h-2
                                    md:w-3
                                    md:h-3
                                    bg-secondary
                                    rounded-full
                                    animate-pulse
                                "
                            />

                            <span
                                className="
                                    text-xs
                                    md:text-sm
                                    uppercase
                                    tracking-wider
                                    whitespace-nowrap
                                "
                            >
                                ACTIVE MONITORING
                            </span>
                        </div>
                    </div>
                </div>

                {/* Content */}
                <div
                    className="
                        order-2
                    "
                >
                    <Typography
                        as="h2"
                        variant="contact-support-title"
                    >
                        ინფრასტრუქტურული მხარდაჭერა
                    </Typography>

                    <Typography
                        as="p"
                        variant="description"
                        className="
                            mb-unit-md
                        "
                    >
                        ჩვენი გუნდი უზრუნველყოფს 24/7
                        დისტანციურ მონიტორინგს და
                        ტექნიკურ მხარდაჭერას.

                        ნებისმიერი ანომალია სისტემაში
                        ფიქსირდება მომენტალურად ჩვენი
                        ცენტრალიზებული მართვის პანელის
                        მეშვეობით.
                    </Typography>

                    <ul
                        className="
                            space-y-unit-sm
                        "
                    >
                        <Items />
                    </ul>
                </div>
            </div>
        </section>
    );
}
