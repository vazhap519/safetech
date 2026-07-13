import Image from "@/components/ui/Image";

import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectsHeroSection() {
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <section className="relative flex min-h-[80vh] items-center justify-center overflow-hidden px-margin-desktop pb-unit-xl pt-28">
            <div className="absolute inset-0 z-0">
                <Image
                    alt={translateText(
                        translations,
                        "projects.hero.imageAlt",
                        locale,
                        {
                            ka: "SafeTech-ის ინფრასტრუქტურული პროექტები",
                            en: "SafeTech infrastructure projects",
                            ru: "Инфраструктурные проекты SafeTech",
                        },
                    )}
                    className="object-cover opacity-10 md:opacity-60"
                    fill
                    priority
                    sizes="100vw"
                    src={branding.defaultImage}
                    unoptimized={branding.defaultImage.endsWith(".svg")}
                />
                <div className="hero-gradient absolute inset-0 z-10" />
            </div>
            <div className="relative z-20 max-w-4xl text-center">
                <div className="mb-6 inline-flex max-w-full items-center gap-2 rounded-full border border-primary/30 bg-primary/5 px-3 py-1.5 sm:px-4">
                    <span
                        aria-hidden="true"
                        className="h-2 w-2 shrink-0 rounded-full bg-secondary shadow-[0_0_10px_#4cd7f6]"
                    />
                    <span className="font-mono-sm text-[10px] uppercase leading-relaxed text-secondary min-[380px]:text-mono-sm">
                        {translateText(
                            translations,
                            "projects.hero.eyebrow",
                            locale,
                            {
                                ka: "ინფრასტრუქტურული გადაწყვეტილებები",
                                en: "Enterprise Infrastructure Solutions",
                                ru: "Инфраструктурные решения",
                            },
                        )}
                    </span>
                </div>
                <h1 className="mb-6 max-w-full px-1 font-display-lg text-[26px] leading-tight text-white sm:text-[36px] md:text-display-lg">
                    {translateText(translations, "projects.hero.title", locale, {
                        ka: "ინფრასტრუქტურული პროექტები",
                        en: "Infrastructure Projects",
                        ru: "Инфраструктурные проекты",
                    })}
                </h1>
                <p className="mb-unit-lg font-body-lg text-body-lg leading-relaxed text-on-surface-variant">
                    {translateText(
                        translations,
                        "projects.hero.description",
                        locale,
                        {
                            ka: "კორპორატიული კლასის უსაფრთხოების, ქსელური და სერვერული ინფრასტრუქტურის რეალური დანერგვები.",
                            en: "Real-world deployments of enterprise-grade security, networking, and server infrastructure.",
                            ru: "Реальные внедрения корпоративной безопасности, сетевой и серверной инфраструктуры.",
                        },
                    )}
                </p>
            </div>
            <div
                aria-hidden="true"
                className="ambient-glow absolute bottom-0 left-1/2 z-0 h-[400px] w-[min(800px,140vw)] -translate-x-1/2"
            />
        </section>
    );
}
