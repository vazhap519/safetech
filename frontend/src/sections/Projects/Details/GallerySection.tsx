import Image from "@/components/ui/Image";

import type { ProjectDetail } from "@/lib/projectDetails";

export default function GallerySection({
    project,
}: {
    project: ProjectDetail;
}) {
    const gallery = project.gallery.length
        ? project.gallery
        : project.image
          ? [{ src: project.image, alt: project.imageAlt || project.title }]
          : [];
    const [primary, ...secondary] = gallery;

    if (!primary) {
        return null;
    }

    return (
        <section
            aria-label={project.title}
            className="mx-auto max-w-container-max px-margin-desktop py-unit-xl"
        >
            <div className="grid grid-cols-1 gap-gutter lg:grid-cols-12">
                <div className="relative min-h-72 overflow-hidden rounded-2xl border border-outline-variant/10 shadow-xl sm:min-h-[500px] lg:col-span-8">
                    <Image
                        alt={primary.alt}
                        className="object-cover"
                        fill
                        sizes="(max-width: 1024px) 100vw, 66vw"
                        src={primary.src}
                        unoptimized={primary.src.endsWith(".svg")}
                    />
                </div>
                <div className="grid gap-gutter min-[480px]:grid-cols-2 lg:col-span-4 lg:grid-cols-1">
                    {secondary.map((image) => (
                        <div
                            className="relative min-h-56 overflow-hidden rounded-2xl border border-outline-variant/10"
                            key={image.alt}
                        >
                            <Image
                                alt={image.alt}
                                className="object-cover"
                                fill
                                sizes="(max-width: 1024px) 50vw, 33vw"
                                src={image.src}
                                unoptimized={image.src.endsWith(".svg")}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
