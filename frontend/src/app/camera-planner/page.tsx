import type { Metadata } from "next";
import Link from "next/link";

import CameraPlanner from "@/components/planner/CameraPlanner";
import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata, localizeHref } from "@/lib/seo";

const wording = {
    ka: ["კამერების განლაგების პლანერი — გეგმა და ბრმა ზონები", "ატვირთეთ ობიექტის გეგმა ან ფოტო, დაამატეთ კამერები, მონიშნეთ კედლები და შეაფასეთ სავარაუდო ბრმა ზონები."],
    en: ["CCTV camera placement planner and blind spots", "Upload a plan or photo, add cameras and walls, and estimate camera coverage and blind spots."],
    ru: ["Планировщик камер видеонаблюдения и слепые зоны", "Загрузите план или фото, добавьте камеры, стены и оцените слепые зоны."],
} as const;

const plannerGuidance = {
    ka: {
        title: "რამდენი კამერა სჭირდება თქვენს ობიექტს?",
        description: "რაოდენობა დამოკიდებულია შესასვლელებზე, პერიმეტრზე, განათებაზე, დაბრკოლებებსა და კამერის ხედვის კუთხეზე. ეს პლანერი მხოლოდ წინასწარ განლაგებაში გეხმარებათ — საბოლოო არჩევანი ადგილზე უნდა გადამოწმდეს.",
        service: "უსაფრთხოების კამერების მონტაჟი და გამართვა",
        projects: "ნახეთ შესრულებული პროექტები",
    },
    en: {
        title: "How many cameras does your property need?",
        description: "The number depends on entrances, perimeter, lighting, obstructions and camera field of view. This planner helps with an initial layout; the final placement needs an on-site check.",
        service: "Security camera installation and setup",
        projects: "Explore completed projects",
    },
    ru: {
        title: "Сколько камер нужно для вашего объекта?",
        description: "Количество зависит от входов, периметра, освещения, препятствий и угла обзора. Планировщик помогает создать предварительную схему; итоговое размещение нужно проверить на объекте.",
        service: "Монтаж и настройка камер видеонаблюдения",
        projects: "Посмотреть выполненные проекты",
    },
} as const;

export async function generateMetadata(): Promise<Metadata> {
    const locale = await getCurrentLocale();
    return createMetadata({
        title: wording[locale][0], description: wording[locale][1],
        path: "/camera-planner", locale,
    });
}

export default async function CameraPlannerPage() {
    const locale = await getCurrentLocale();
    const guidance = plannerGuidance[locale];

    return (
        <>
            <CameraPlanner />
            <section
                aria-labelledby="planner-guidance-heading"
                className="mx-auto max-w-container-max px-5 pb-20 md:px-8 xl:px-14"
            >
                <div className="rounded-3xl border border-white/10 bg-surface-container p-7 sm:p-10">
                    <h2
                        id="planner-guidance-heading"
                        className="text-2xl font-semibold text-on-surface md:text-3xl"
                    >
                        {guidance.title}
                    </h2>
                    <p className="mt-4 max-w-3xl leading-8 text-on-surface-variant">
                        {guidance.description}
                    </p>
                    <div className="mt-7 flex flex-wrap gap-4">
                        <Link
                            className="rounded-xl bg-primary-container px-6 py-4 font-semibold text-on-primary-container"
                            href={localizeHref("/services/security-camera-installation", locale)}
                        >
                            {guidance.service}
                        </Link>
                        <Link
                            className="rounded-xl border border-outline-variant px-6 py-4 font-semibold text-on-surface hover:border-primary/40"
                            href={localizeHref("/projects", locale)}
                        >
                            {guidance.projects}
                        </Link>
                    </div>
                </div>
            </section>
        </>
    );
}
