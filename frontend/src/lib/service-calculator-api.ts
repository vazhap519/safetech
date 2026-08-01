import "server-only";

import { buildServerApiUrl } from "@/lib/backend-api";
import { getCurrentLocale } from "@/lib/locale-server";
import type { CalculatorProfile } from "@/lib/service-calculator";

type CalculatorProfilesResponse = {
    data?: CalculatorProfile[];
};

export async function getServiceCalculatorProfiles(): Promise<CalculatorProfile[]> {
    const locale = await getCurrentLocale();
    const url = new URL(buildServerApiUrl("/service-calculator/profiles"));
    url.searchParams.set("locale", locale);

    try {
        const response = await fetch(url, {
            next: {
                revalidate: 60,
                tags: ["cms", "services", "service-calculator"],
            },
        });

        if (!response.ok) {
            return [];
        }

        const payload = (await response.json()) as
            | CalculatorProfile[]
            | CalculatorProfilesResponse;
        const profiles = Array.isArray(payload) ? payload : payload.data;

        return Array.isArray(profiles)
            ? profiles.filter((profile) => Boolean(profile?.slug && profile?.name))
            : [];
    } catch {
        return [];
    }
}
