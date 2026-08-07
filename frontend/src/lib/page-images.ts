import "server-only";

import { cache } from "react";

import { getBackendContent, maybeBackendAsset } from "@/lib/backend";

type PageImages = {
    homeHero: string | null;
    homeInfrastructure: string | null;
    servicesHero: string | null;
    projectsHero: string | null;
    aboutStory: string | null;
    contactIntro: string | null;
    contactSupport: string | null;
};

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

function mediaUrl(branding: Record<string, unknown>, key: string): string | null {
    const value = branding[key];

    return typeof value === "string" && value.trim()
        ? maybeBackendAsset(value.trim())
        : null;
}

export const getPageImages = cache(async (): Promise<PageImages> => {
    const content = await getBackendContent();
    const settings = isRecord(content.settings) ? content.settings : {};
    const branding = isRecord(settings.branding) ? settings.branding : {};

    return {
        homeHero: mediaUrl(branding, "home_hero"),
        homeInfrastructure: mediaUrl(branding, "home_infrastructure"),
        servicesHero: mediaUrl(branding, "services_hero"),
        projectsHero: mediaUrl(branding, "projects_hero"),
        aboutStory: mediaUrl(branding, "about_story"),
        contactIntro: mediaUrl(branding, "contact_intro"),
        contactSupport: mediaUrl(branding, "contact_support"),
    };
});
