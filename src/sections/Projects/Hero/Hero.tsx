import Image from "@/components/ui/Image";

import { getSiteSettings } from "@/lib/site-settings";

export default async function ProjectsHeroSection() {
    const { branding } = await getSiteSettings();

    return (
        <section className="relative flex min-h-[80vh] items-center justify-center overflow-hidden px-margin-desktop pb-unit-xl pt-28">
            <div className="absolute inset-0 z-0">
                <Image
                    alt="SafeTech-ის ინფრასტრუქტურული პროექტები"
                    className="object-cover opacity-60"
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
                    <span className="font-mono-sm text-[10px] leading-relaxed text-secondary uppercase min-[380px]:text-mono-sm">
                        Enterprise Infrastructure Solutions
                    </span>
                </div>
                <h1 className="mb-6 font-display-lg text-display-lg-mobile text-white md:text-display-lg">
                    ინფრასტრუქტურული პროექტები
                </h1>
                <p className="mb-unit-lg font-body-lg text-body-lg leading-relaxed text-on-surface-variant">
                    Enterprise კლასის უსაფრთხოების, ქსელური და სერვერული
                    ინფრასტრუქტურის რეალური დანერგვები.
                </p>
            </div>
            <div
                aria-hidden="true"
                className="ambient-glow absolute bottom-0 left-1/2 z-0 h-[400px] w-[min(800px,140vw)] -translate-x-1/2"
            />
        </section>
    );
}
