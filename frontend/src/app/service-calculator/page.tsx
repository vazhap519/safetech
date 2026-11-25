import type { Metadata } from "next";
import { permanentRedirect } from "next/navigation";

import { getCurrentLocale } from "@/lib/locale-server";
import { firstSearchParam } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import { resolveCanonicalServiceSlug } from "@/lib/canonical-services";

export const metadata: Metadata = {
    robots: {
        index: false,
        follow: true,
    },
};

type LegacyServiceCalculatorPageProps = {
    searchParams?: Promise<{ service?: string }>;
};

export default async function LegacyServiceCalculatorPage({
    searchParams,
}: LegacyServiceCalculatorPageProps) {
    const resolvedSearchParams = await searchParams;
    const selectedService = firstSearchParam(resolvedSearchParams?.service);
    const canonicalService = resolveCanonicalServiceSlug(selectedService);
    const locale = await getCurrentLocale();
    const target = canonicalService
        ? `/calculators?service=${encodeURIComponent(canonicalService)}#service-calculator`
        : "/calculators#service-calculator";

    permanentRedirect(localizeHref(target, locale));
}
