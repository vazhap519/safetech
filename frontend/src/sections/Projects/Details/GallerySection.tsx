import Image from "@/components/ui/Image";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getYouTubeEmbedUrl } from "@/lib/youtube";

export default function GallerySection({
    project,
}: {
    project: ProjectDetail;
}) {
    const embedUrl = getYouTubeEmbedUrl(project.videoUrl);
    const gallery = project.gallery;
    const [primary, ...secondary] = gallery;
    const galleryImages = embedUrl ? gallery : secondary;

    if (!embedUrl && !primary) {
        return null;
    }

    return (
        <section
            aria-label={project.title || project.name}
            className="mx-auto max-w-container-max px-4 py-10 sm:px-6 sm:py-unit-xl lg:px-margin-desktop"
        >
            <div className="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-12">
                <div className="relative aspect-video w-full overflow-hidden rounded-2xl border border-outline-variant/10 bg-black/20 shadow-xl lg:col-span-8 lg:aspect-[16/10]">
                    {embedUrl ? (
                        <iframe
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowFullScreen
                            className="absolute inset-0 h-full w-full"
                            loading="lazy"
                            referrerPolicy="strict-origin-when-cross-origin"
                            src={embedUrl}
                            title={project.title || project.name}
                        />
                    ) : primary ? (
                        <Image
                            alt={primary.alt}
                            className="object-cover"
                            fill
                            sizes="(max-width: 1024px) 100vw, 66vw"
                            src={primary.src}
                            unoptimized={primary.src.endsWith(".svg")}
                        />
                    ) : null}
                </div>

                {galleryImages.length ? (
                    <div className="grid grid-cols-1 gap-4 min-[520px]:grid-cols-2 sm:gap-6 lg:col-span-4 lg:grid-cols-1">
                        {galleryImages.map((image, index) => (
                            <figure
                                className="group relative aspect-[4/3] overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-low shadow-sm"
                                key={`${image.src}-${image.alt}`}
                            >
                                <Image
                                    alt={image.alt}
                                    className="object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                    fill
                                    sizes="(max-width: 519px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                    src={image.src}
                                    unoptimized={image.src.endsWith(".svg")}
                                />
                                <span
                                    aria-hidden="true"
                                    className="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/15 via-transparent to-transparent"
                                />
                                <span className="sr-only">
                                    {image.alt || `${project.title || project.name} ${index + 1}`}
                                </span>
                            </figure>
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
