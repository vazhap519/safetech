import { createMetadata } from "@/lib/seo";
import { normalizeLocale } from "@/lib/locales";

export { default } from "@/app/camera-planner/page";

export async function generateMetadata({ params }: {
    params: Promise<{ locale: string }>;
}) {
    const { locale } = await params;
    const language = normalizeLocale(locale);
    const descriptions = {
        ka: ["კამერების განლაგების პლანერი", "ატვირთეთ გეგმა, დაამატეთ კამერები და შეაფასეთ დაფარვა."],
        en: ["CCTV camera placement planner", "Upload a plan, add cameras and estimate coverage."],
        ru: ["Планировщик размещения камер", "Загрузите план, добавьте камеры и оцените покрытие."],
    };
    return createMetadata({
        title: descriptions[language][0],
        description: descriptions[language][1],
        path: "/camera-planner",
        locale: language,
    });
}
