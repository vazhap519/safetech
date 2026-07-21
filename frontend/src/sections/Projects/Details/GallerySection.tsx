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
    const sideGallery = embedUrl ? gallery : secondary;

    if (!embedUrl && !primary) {
        return null;
    }

    return (
        <section
            aria-label={project.title || project.name}
            className="mx-auto max-w-container-max px-margin-desktop py-unit-xl"
        >
            <div className="grid grid-cols-1 gap-gutter lg:grid-cols-12">
                <div className="relative min-h-72 overflow-hidden rounded-2xl border border-outline-variant/10 shadow-xl sm:min-h-[500px] lg:col-span-8">
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
                {sideGallery.length ? (
                    <div className="grid gap-gutter min-[480px]:grid-cols-2 lg:col-span-4 lg:grid-cols-1">
                        {sideGallery.map((image) => (
                            <div
                                className="relative min-h-56 overflow-hidden rounded-2xl border border-outline-variant/10"
                                key={`${image.src}-${image.alt}`}
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
                ) : null}
            </div>
        </section>
    );
}
