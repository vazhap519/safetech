"use client";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Button from "@/components/ui/Button";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function Cta() {
    const { status, message, submit } = useLeadForm("home-cta");
    const { t } = useLocalization();

    return (
        <section
            className="
                relative
                mx-auto
                max-w-container-max
                overflow-hidden
                px-5
                py-20
                md:px-10
                md:py-28
                xl:px-margin-desktop
            "
        >
            <div
                className="
                    mesh-gradient
                    relative
                    z-10
                    overflow-hidden
                    rounded-[28px]
                    border
                    border-outline-variant/20
                    px-6
                    py-14
                    text-center
                    sm:px-10
                    md:rounded-[40px]
                    md:px-16
                    md:py-20
                "
            >
                <div
                    className="
                        absolute
                        inset-0
                        bg-primary/5
                        blur-[120px]
                        opacity-70
                    "
                />

                <div
                    className="
                        relative
                        z-20
                        mx-auto
                        max-w-4xl
                        space-y-8
                    "
                >
                    <span
                        className="
                            inline-block
                            font-mono-sm
                            text-mono-sm
                            uppercase
                            tracking-[0.3em]
                            text-primary
                        "
                    >
                        {t("home.cta.eyebrow", {
                            ka: "უფასო კონსულტაცია",
                            en: "Free consultation",
                            ru: "Бесплатная консультация",
                        })}
                    </span>

                    <Typography as="h2" variant="cta-title">
                        {t("home.cta.title", {
                            ka: "დავგეგმოთ თქვენი უსაფრთხო IT ინფრასტრუქტურა",
                            en: "Let's plan your secure IT infrastructure",
                            ru: "Давайте спланируем вашу безопасную IT-инфраструктуру",
                        })}
                    </Typography>

                    <Typography
                        as="p"
                        variant="section-description"
                        className="mx-auto max-w-2xl"
                    >
                        {t("home.cta.description", {
                            ka: "ჩვენი ექსპერტები მზად არიან დაგეხმარონ საუკეთესო გადაწყვეტილების შერჩევაში.",
                            en: "Our experts are ready to help you choose the right solution.",
                            ru: "Наши эксперты готовы помочь вам выбрать оптимальное решение.",
                        })}
                    </Typography>

                    <form
                        className="mx-auto w-full max-w-2xl space-y-4 pt-2"
                        onSubmit={submit}
                    >
                        <input
                            aria-hidden="true"
                            autoComplete="off"
                            className="sr-only"
                            name="website"
                            tabIndex={-1}
                            type="text"
                        />

                        <div className="flex flex-col items-center justify-center gap-4 md:flex-row">
                            <label className="sr-only" htmlFor="home-cta-email">
                                {t("home.cta.emailLabel", {
                                    ka: "თქვენი ელ-ფოსტა",
                                    en: "Your email",
                                    ru: "Ваш email",
                                })}
                            </label>
                            <input
                                id="home-cta-email"
                                autoComplete="email"
                                className="
                                    w-full
                                    rounded-xl
                                    border
                                    border-outline-variant/30
                                    bg-surface-container-highest/50
                                    px-6
                                    py-4
                                    font-body-md
                                    text-on-surface
                                    outline-none
                                    backdrop-blur-xl
                                    transition-all
                                    duration-300
                                    focus:border-transparent
                                    focus:ring-2
                                    focus:ring-primary
                                    md:flex-1
                                "
                                name="email"
                                placeholder={t("home.cta.emailPlaceholder", {
                                    ka: "თქვენი ელ-ფოსტა",
                                    en: "Your email",
                                    ru: "Ваш email",
                                })}
                                required
                                type="email"
                            />

                            <Button
                                variant="cta"
                                size="lg"
                                type="submit"
                                disabled={status === "submitting"}
                            >
                                {status === "submitting"
                                    ? t("forms.submitting", {
                                          ka: "იგზავნება...",
                                          en: "Sending...",
                                          ru: "Отправка...",
                                      })
                                    : t("home.cta.submit", {
                                          ka: "კონსულტაცია",
                                          en: "Consultation",
                                          ru: "Консультация",
                                      })}
                            </Button>
                        </div>
                        <PrivacyConsent />
                    </form>
                    <p
                        aria-live="polite"
                        className={status === "error" ? "text-error" : "text-success"}
                        role="status"
                    >
                        {message}
                    </p>

                    <p
                        className="
                            text-sm
                            font-label-md
                            leading-relaxed
                            text-on-surface-variant/50
                            md:text-label-md
                        "
                    >
                        {t("home.cta.note", {
                            ka: "კონსულტაცია უფასოა და არ გაკისრებთ არანაირ ვალდებულებას.",
                            en: "The consultation is free and does not obligate you in any way.",
                            ru: "Консультация бесплатна и не накладывает на вас никаких обязательств.",
                        })}
                    </p>
                </div>
            </div>

            <div
                className="
                    absolute
                    bottom-[-250px]
                    left-[-250px]
                    -z-10
                    h-[600px]
                    w-[600px]
                    rounded-full
                    bg-primary/5
                    blur-[160px]
                "
            />
        </section>
    );
}
