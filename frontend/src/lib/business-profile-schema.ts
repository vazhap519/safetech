import "server-only";

import { cache } from "react";
import { getBackendContent } from "@/lib/backend";

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

function pickString(value: unknown): string {
    return typeof value === "string" ? value.trim() : "";
}

export type BusinessProfileSchemaDetails = {
    city: string;
    country: string;
    postalCode: string;
    description: string;
};

export const getBusinessProfileSchemaDetails = cache(async (): Promise<BusinessProfileSchemaDetails> => {
    const content = await getBackendContent();
    const settings = isRecord(content.settings) ? content.settings : {};
    const seo = isRecord(settings.seo) ? settings.seo : {};

    return {
        city: pickString(seo.city),
        country: pickString(seo.country).toUpperCase() || "GE",
        postalCode: pickString(seo.postal_code),
        description: pickString(seo.site_description),
    };
});
