import type { ReactNode } from "react";

import SafeTechAssistant from "@/components/ai/SafeTechAssistant";
import ConsultationModal from "@/components/consultation/ConsultationModal";
import { buildServerApiUrl } from "@/lib/backend-api";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ServiceOption = {
    slug: string;
    label: string;
};

type PublicSettingsResponse = {
    ai_assistant?: {
        enabled?: boolean;
    };
};

async function getServiceOptions(locale: string): Promise<ServiceOption[]> {
    try {
        const response = await fetch(
            buildServerApiUrl(
                `/services/options?locale=${encodeURIComponent(locale)}`,
            ),
            {
                next: { revalidate: 300, tags: ["cms"] },
                signal: AbortSignal.timeout(3000),
            },
        );

        if (!response.ok) return [];

        const payload = (await response.json()) as { data?: ServiceOption[] };

        return (payload.data ?? []).filter(
            (service) => service.slug && service.label,
        );
    } catch {
        return [];
    }
}

async function getAiAssistantEnabled(): Promise<boolean> {
    if (process.env.NEXT_PUBLIC_AI_ASSISTANT_ENABLED !== "true") {
        return false;
    }

    try {
        const response = await fetch(buildServerApiUrl("/settings"), {
            next: { revalidate: 300, tags: ["cms"] },
            signal: AbortSignal.timeout(3000),
        });

        if (!response.ok) return false;

        const payload = (await response.json()) as PublicSettingsResponse;

        return payload.ai_assistant?.enabled === true;
    } catch {
        return false;
    }
}

export default async function ConsultationProvider({
    children,
}: {
    children: ReactNode;
}) {
    const { locale, translations } = await getSiteSettings();
    const [serviceOptions, assistantEnabled] = await Promise.all([
        getServiceOptions(locale),
        getAiAssistantEnabled(),
    ]);
    const eyebrow = translateText(
        translations,
        "consultation.modal.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "consultation.modal.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "consultation.modal.description",
        locale,
        null,
    );
    const closeLabel = translateText(
        translations,
        "consultation.modal.close",
        locale,
        null,
    );

    return (
        <>
            {children}
            <ConsultationModal
                closeLabel={closeLabel}
                description={description}
                eyebrow={eyebrow}
                serviceOptions={serviceOptions}
                title={title}
            />
            {assistantEnabled ? <SafeTechAssistant /> : null}
        </>
    );
}
