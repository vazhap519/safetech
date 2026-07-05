
import Typography from "@/components/ui/Typography";
import ActionLink from "@/components/ui/ActionLink";

export default function FinalSection() {
    return (
        <section
            className="
                py-16
                sm:py-20
                md:py-24
                lg:py-unit-xl

                bg-gradient-to-b
                from-surface-container-low
                to-background
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-4
                    sm:px-6
                    lg:px-margin-desktop

                    flex
                    flex-col
                    items-center
                    justify-center
                "
            >
                <Typography
                    as="h2"
                    variant="contact-final-cta"
                    className="
                        max-w-5xl
                        mx-auto
                        text-center
                        text-balance
                    "
                >
                    SafeTech — თანამედროვე
                    <br className="hidden md:block" />
                    {" "}
                    ინფრასტრუქტურის პარტნიორი
                </Typography>

                <div
                    className="
                        mt-6
                        md:mt-8

                        w-full
                        flex
                        justify-center
                    "
                >
                    <ActionLink
                        href="#contact-form"
                        className="
                            w-full
                            max-w-xs

                            sm:w-auto
                            sm:max-w-none
                        "
                    >
                        კონსულტაციის მოთხოვნა
                    </ActionLink>
                </div>
            </div>
        </section>
    );
}
