import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectsHeroSection() {
    const { branding, locale, translations } = await getSiteSettings();

    const eyebrow = translateText(
        translations,
        "projects.hero.eyebrow",
        locale,
        { ka: "განხორციელებული სამუშაოები", en: "Delivered work", ru: "Реализованные работы" },
    );
    const title = translateText(translations, "projects.hero.title", locale, {
        ka: "IT და უსაფრთხოების პროექტები",
        en: "IT and security projects",
        ru: "Проекты IT и безопасности",
    });
    const description = translateText(
        translations,
        "projects.hero.description",
        locale,
        {
            ka: "ნახეთ როგორ ვგეგმავთ და ვნერგავთ ქსელურ, სერვერულ და უსაფრთხოების ინფრასტრუქტურას.",
            en: "See how we design and deploy network, server, and security infrastructure.",
            ru: "Посмотрите, как мы проектируем и внедряем сетевую, серверную инфраструктуру и системы безопасности.",
        },
    );
    const imageAlt = translateText(
        translations,
        "projects.hero.imageAlt",
        locale,
        {
            ka: "SafeTech-ის განხორციელებული ინფრასტრუქტურის პროექტი",
            en: "Infrastructure project delivered by SafeTech",
            ru: "Инфраструктурный проект, реализованный SafeTech",
        },
    );

    return (
        <section className="relative flex min-h-[70svh] items-center justify-center overflow-hidden px-5 pb-14 pt-28 md:px-8 md:pb-16 xl:px-14">
            <div className="absolute inset-0 z-0">
                <Image
                    alt={imageAlt || title}
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
                {eyebrow ? (
                    <div className="mb-6 inline-flex max-w-full items-center gap-2 rounded-full border border-primary/30 bg-primary/5 px-3 py-1.5 sm:px-4">
                        <span
                            aria-hidden="true"
                            className="h-2 w-2 shrink-0 rounded-full bg-secondary shadow-[0_0_10px_#4cd7f6]"
                        />
                        <span className="font-mono-sm text-[10px] uppercase leading-relaxed text-secondary min-[380px]:text-mono-sm">
                            {eyebrow}
                        </span>
                    </div>
                ) : null}
                {title ? (
                    <h1 className="mb-5 max-w-full px-1 font-display-lg text-[30px] font-semibold leading-[1.18] text-white sm:text-[36px] md:text-[46px] lg:text-[52px]">
                        {title}
                    </h1>
                ) : null}
                {description ? (
                    <p className="mb-unit-lg font-body-lg text-body-lg leading-relaxed text-on-surface-variant">
                        {description}
                    </p>
                ) : null}
            </div>
            <div
                aria-hidden="true"
                className="ambient-glow absolute bottom-0 left-1/2 z-0 h-[400px] w-[min(800px,140vw)] -translate-x-1/2"
            />
        </section>
    );
}
