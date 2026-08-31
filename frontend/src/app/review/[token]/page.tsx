import type { Metadata } from "next";

import ReviewRequestForm from "@/components/reviews/ReviewRequestForm";
import { getSiteSettings } from "@/lib/site-settings";

export const metadata: Metadata = {
    title: "Client feedback | SafeTech",
    robots: {
        index: false,
        follow: false,
    },
};

export default async function ReviewRequestPage({
    params,
}: {
    params: Promise<{ token: string }>;
}) {
    const { token } = await params;
    const { integrations } = await getSiteSettings();

    return (
        <div className="mx-auto min-h-[calc(100svh-8rem)] max-w-container-max px-5 pb-16 pt-28 sm:px-6 md:pb-20 lg:px-14 lg:pt-32">
            <ReviewRequestForm
                googleReviewUrl={integrations.googleReviewUrl}
                token={token}
            />
        </div>
    );
}
