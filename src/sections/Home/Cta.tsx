"use client";

import Button from "@/components/ui/Button";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";
import PrivacyConsent from "@/components/forms/PrivacyConsent";

export default function Cta() {
    const { status, message, submit } = useLeadForm("home-cta");

    return (
        <section
            className="
                relative
                overflow-hidden
                py-20
                md:py-28
                px-5
                md:px-10
                xl:px-margin-desktop
                max-w-container-max
                mx-auto
            "
        >

            {/* CTA Container */}
            <div
                className="
                    mesh-gradient
                    relative
                    z-10
                    overflow-hidden
                    rounded-[28px]
                    md:rounded-[40px]
                    border
                    border-outline-variant/20
                    px-6
                    py-14
                    sm:px-10
                    md:px-16
                    md:py-20
                    text-center
                "
            >

                {/* Glow */}
                <div
                    className="
                        absolute
                        inset-0
                        bg-primary/5
                        blur-[120px]
                        opacity-70
                    "
                ></div>

                {/* Content */}
                <div
                    className="
                        relative
                        z-20
                        space-y-8
                        max-w-4xl
                        mx-auto
                    "
                >

                    {/* Badge */}
                    <span
                        className="
                            inline-block
                            text-primary
                            font-mono-sm
                            text-mono-sm
                            uppercase
                            tracking-[0.3em]
                        "
                    >
                        უფასო კონსულტაცია
                    </span>

                    <Typography
                        as="h2"
                        variant="cta-title"
                    >
                        დავგეგმოთ თქვენი უსაფრთხო
                        <br className="hidden md:block" />
                        {" "}IT ინფრასტრუქტურა
                    </Typography>

                    {/* Description */}
                    <Typography
                        as="p"
                        variant="section-description"
                        className="
        max-w-2xl
        mx-auto
    "
                    >
                        ჩვენი ექსპერტები მზად არიან
                        დაგეხმარონ საუკეთესო გადაწყვეტილების
                        შერჩევაში.
                    </Typography>

                    {/* Form */}
                    <form
                        onSubmit={submit}
                        className="
                            space-y-4
                            pt-2
                            w-full
                            max-w-2xl
                            mx-auto
                        "
                    >

                        <input aria-hidden="true" autoComplete="off" className="absolute -left-[9999px]" name="website" tabIndex={-1} type="text" />

                        <div className="flex flex-col items-center justify-center gap-4 md:flex-row">
                        <label className="sr-only" htmlFor="home-cta-email">
                            თქვენი ელ-ფოსტა
                        </label>
                        <input
                            id="home-cta-email"
                            type="email"
                            name="email"
                            autoComplete="email"
                            required
                            placeholder="თქვენი ელ-ფოსტა"
                            className="
                                w-full
                                md:flex-1
                                bg-surface-container-highest/50
                                border
                                border-outline-variant/30
                                rounded-xl
                                px-6
                                py-4
                                outline-none
                                font-body-md
                                text-on-surface
                                backdrop-blur-xl
                                transition-all
                                duration-300
                                focus:ring-2
                                focus:ring-primary
                                focus:border-transparent
                            "
                        />

                        <Button
                            variant="cta"
                            size="lg"
                            type="submit"
                            disabled={status === "submitting"}
                        >
                            {status === "submitting" ? "იგზავნება..." : "კონსულტაცია"}
                        </Button>
                        </div>
                        <PrivacyConsent />
                    </form>
                    <p aria-live="polite" className={status === "error" ? "text-error" : "text-success"} role="status">{message}</p>

                    {/* Bottom Text */}
                    <p
                        className="
                            text-on-surface-variant/50
                            text-sm
                            md:text-label-md
                            font-label-md
                            leading-relaxed
                        "
                    >
                        კონსულტაცია უფასოა და არ
                        გაკისრებთ არანაირ ვალდებულებას.
                    </p>
                </div>
            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-250px]
                    left-[-250px]
                    w-[600px]
                    h-[600px]
                    bg-primary/5
                    rounded-full
                    blur-[160px]
                    -z-10
                "
            ></div>
        </section>
    );
}
