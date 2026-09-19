import type { Metadata } from "next";
import CameraPlanner from "@/components/planner/CameraPlanner";
import { createMetadata } from "@/lib/seo";
import { getCurrentLocale } from "@/lib/locale-server";

const wording = {
    ka: ["კამერების განლაგების პლანერი — გეგმა და ბრმა ზონები", "ატვირთეთ ობიექტის გეგმა ან ფოტო, დაამატეთ კამერები, მონიშნეთ კედლები და შეაფასეთ სავარაუდო ბრმა ზონები."],
    en: ["CCTV camera placement planner and blind spots", "Upload a plan or photo, add cameras and walls, and estimate camera coverage and blind spots."],
    ru: ["Планировщик камер видеонаблюдения и слепые зоны", "Загрузите план или фото, добавьте камеры, стены и оцените слепые зоны."],
} as const;

export async function generateMetadata(): Promise<Metadata> {
    const locale = await getCurrentLocale();
    return createMetadata({
        title: wording[locale][0], description: wording[locale][1],
        path: "/camera-planner", locale,
    });
}

export default function CameraPlannerPage() {
    return <CameraPlanner />;
}
