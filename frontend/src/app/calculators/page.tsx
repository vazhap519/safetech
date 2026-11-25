import type { Metadata } from "next";
import Link from "next/link";

import ServiceCalculatorSlot from "@/components/calculator/ServiceCalculatorSlot";
import { getCurrentLocale } from "@/lib/locale-server";
import type { Locale } from "@/lib/locales";
import { firstSearchParam } from "@/lib/pagination";
import { createMetadata, localizeHref } from "@/lib/seo";

const copy = {
    ka: {
        title: "კალკულატორები და სისტემების კონფიგურატორი",
        description: "ერთ სივრცეში გამოთვალეთ მომსახურება, შეარჩიეთ თავსებადი მოწყობილობები, დაგეგმეთ კამერები და ვიდეოარქივი.",
        heading: "კალკულატორების ცენტრი",
        intro: "აირჩიეთ სერვისი და მიუთითეთ ობიექტის პარამეტრები. სისტემა გაჩვენებთ მხოლოდ თავსებად მოწყობილობებს, მიახლოებით რაოდენობებსა და ღირებულებას.",
        service: "სერვისის ღირებულება და მოწყობილობები",
        serviceText: "7 ძირითადი სერვისის კონფიგურაცია, მასალები, სამუშაო და საორიენტაციო ფასი.",
        planner: "კამერების განლაგების პლანერი",
        plannerText: "გეგმა, ხედვის კუთხეები, კამერების ჯგუფები, NVR, bandwidth და HDD.",
        open: "გახსნა",
    },
    en: {
        title: "Calculators and system configurator",
        description: "Estimate services, select compatible equipment, plan cameras and calculate video storage in one place.",
        heading: "Calculator center",
        intro: "Choose a service and enter the property parameters. Only compatible equipment, estimated quantities and pricing will be shown.",
        service: "Service price and equipment",
        serviceText: "Configure the 7 core services, materials, labor and indicative pricing.",
        planner: "Camera placement planner",
        plannerText: "Plan coverage, camera groups, NVR, bandwidth and HDD storage.",
        open: "Open",
    },
    ru: {
        title: "Калькуляторы и конфигуратор систем",
        description: "Расчёт услуг, совместимого оборудования, камер и видеоархива в одном разделе.",
        heading: "Центр калькуляторов",
        intro: "Выберите услугу и параметры объекта. Система покажет только совместимое оборудование, примерное количество и стоимость.",
        service: "Стоимость услуги и оборудование",
        serviceText: "Настройка 7 основных услуг, материалов, работ и ориентировочной цены.",
        planner: "Планировщик размещения камер",
        plannerText: "Зоны обзора, группы камер, NVR, пропускная способность и HDD.",
        open: "Открыть",
    },
} as const;

type CalculatorsPageProps = {
    searchParams?: Promise<{ service?: string }>;
};

export async function generateMetadata(): Promise<Metadata> {
    const locale = await getCurrentLocale();
    return calculatorsMetadata(locale);
}

export function calculatorsMetadata(locale: Locale): Metadata {
    return createMetadata({
        title: copy[locale].title,
        description: copy[locale].description,
        path: "/calculators",
        locale,
    });
}

export default async function CalculatorsPage({ searchParams }: CalculatorsPageProps) {
    const locale = await getCurrentLocale();
    const selectedService = firstSearchParam((await searchParams)?.service);
    const text = copy[locale];

    return (
        <main className="mx-auto max-w-container-max px-5 py-12 md:px-8 md:py-16 xl:px-14">
            <header className="max-w-4xl">
                <p className="text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                    {text.title}
                </p>
                <h1 className="mt-4 text-4xl font-bold text-on-surface md:text-6xl">
                    {text.heading}
                </h1>
                <p className="mt-5 text-lg leading-8 text-on-surface-variant">
                    {text.intro}
                </p>
            </header>

            <nav aria-label={text.heading} className="mt-10 grid gap-4 md:grid-cols-2">
                <a className="rounded-2xl border border-primary/20 bg-primary/10 p-6" href="#service-calculator">
                    <h2 className="text-xl font-semibold text-on-surface">{text.service}</h2>
                    <p className="mt-2 leading-7 text-on-surface-variant">{text.serviceText}</p>
                    <span className="mt-5 inline-flex font-semibold text-primary">{text.open} ↓</span>
                </a>
                <Link className="rounded-2xl border border-outline-variant/30 bg-surface-container p-6" href={localizeHref("/camera-planner", locale)}>
                    <h2 className="text-xl font-semibold text-on-surface">{text.planner}</h2>
                    <p className="mt-2 leading-7 text-on-surface-variant">{text.plannerText}</p>
                    <span className="mt-5 inline-flex font-semibold text-primary">{text.open} →</span>
                </Link>
            </nav>

            <ServiceCalculatorSlot initialService={selectedService} />
        </main>
    );
}
