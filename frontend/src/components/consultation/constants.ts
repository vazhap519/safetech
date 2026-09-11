export const CONSULTATION_MODAL_ID = "consultation-modal";
export const CONSULTATION_OPEN_EVENT = "safetech:consultation-open";
export const CONSULTATION_CLOSE_EVENT = "safetech:consultation-close";

export type ConsultationPrefill = {
    serviceSlug?: string;
    message?: string;
    details?: Array<{
        key: string;
        label: string;
        type: string;
        value: string;
    }>;
};
