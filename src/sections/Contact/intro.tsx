
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Intro() {
    const { branding } = await getSiteSettings();

    return (
        <section className="py-unit-xl bg-background">
            <div
                className="
                    container
                    max-w-container-max
                    mx-auto
                    px-margin-desktop
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-unit-lg
                    lg:gap-unit-xl
                    items-center
                "
            >
                {/* Content */}
                <div
                    className="
                        order-2
                        md:order-1
                    "
                >
                    <Typography
                        as="h2"
                        variant="contact-intro-title"
                        className="mb-unit-md"
                    >
                        ციფრული ფორტიფიკაცია
                    </Typography>

                    <div
                        className="
                            space-y-unit-md
                            text-on-surface-variant
                            text-base
                            md:text-lg
                            leading-relaxed
                        "
                    >
                        <p>
                            SafeTech-ის ექსპერტთა ჯგუფი
                            უზრუნველყოფს კორპორატიული დონის
                            ინფრასტრუქტურის დაპროექტებას,
                            რომელიც პასუხობს უსაფრთხოების
                            თანამედროვე გამოწვევებს.
                        </p>

                        <p>
                            ჩვენი მიდგომა ეფუძნება სისტემურ
                            ანალიზს და მასშტაბირებად
                            არქიტექტურას, რაც გარანტიას იძლევა
                            თქვენი მონაცემების და ფიზიკური
                            აქტივების მაქსიმალურ დაცვაზე.
                        </p>
                    </div>

                    {/* Status */}
                    <div
                        className="
                            mt-unit-lg
                            flex
                            flex-col
                            sm:flex-row
                            gap-unit-md
                        "
                    >
                        <div
                            className="
                                flex
                                items-center
                                gap-unit-xs
                            "
                        >
                            <span
                                className="
                                    w-2
                                    h-2
                                    rounded-full
                                    bg-secondary
                                    animate-pulse
                                    shadow-[0_0_8px_#4cd7f6]
                                "
                            />

                            <span
                                className="
                                    text-xs
                                    md:text-sm
                                    uppercase
                                    tracking-widest
                                    text-secondary
                                "
                            >
                                Live Monitoring
                            </span>
                        </div>

                        <div
                            className="
                                flex
                                items-center
                                gap-unit-xs
                            "
                        >
                            <span
                                className="
                                    w-2
                                    h-2
                                    rounded-full
                                    bg-primary
                                    animate-pulse
                                    shadow-[0_0_8px_#b4c5ff]
                                "
                            />

                            <span
                                className="
                                    text-xs
                                    md:text-sm
                                    uppercase
                                    tracking-widest
                                    text-primary
                                "
                            >
                                Enterprise Secure
                            </span>
                        </div>
                    </div>
                </div>

                {/* Image */}
                <div
                    className="
                        order-1
                        md:order-2
                    "
                >
                    <div
                        className="
                            glass-panel
                            p-2
                            rounded-xl
                            overflow-hidden
                            group
                        "
                    >
                        <Image
                            variant="contact-intro"
                            src={branding.defaultImage}
                            alt="Server racks visualization"
                            width={610}
                            height={410}
                            sizes="(max-width: 768px) 100vw, 50vw"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
