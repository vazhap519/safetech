import type { Metadata } from "next";

import { calculatorsMetadata } from "@/app/calculators/page";
import { normalizeLocale } from "@/lib/locales";

type LocalizedCalculatorsPageProps = {
    params: Promise<{ locale: string }>;
    searchParams?: Promise<{ service?: string }>;
};

export async function generateMetadata({ params }: LocalizedCalculatorsPageProps): Promise<Metadata> {
    const { locale } = await params;
    return calculatorsMetadata(normalizeLocale(locale));
}

export { default } from "@/app/calculators/page";
