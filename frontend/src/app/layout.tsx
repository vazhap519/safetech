import type { Metadata } from "next";
import { Noto_Sans_Georgian } from "next/font/google";

import "./globals.css";

import FloatingWhatsAppSlot from "@/components/Contact/FloatingWhatsAppSlot";
import Footer from "@/components/Footer/Footer";
import Navbar from "@/components/Navbar/Navbar";
import ConsultationProvider from "@/components/consultation/ConsultationProvider";
import LocalizationProvider from "@/components/providers/LocalizationProvider";
import MarketingPixels from "@/components/analytics/MarketingPixels";
import {
    getLanguageTag,
    getOgLocale,
    supportedLocales,
} from "@/lib/locales";
import {
    absoluteLocalizedUrl,
    absoluteSiteUrl,
    buildLanguageAlternates,
    SITE_NAME,
    SITE_URL,
} from "@/lib/seo";
import { buildOrganizationEntity } from "@/lib/organization-schema";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

const georgianFont = Noto_Sans_Georgian({
    display: "swap",
    preload: false,
    subsets: ["georgian"],
    variable: "--font-georgian",
    weight: "variable",
});

function withDynamicSiteTitle(title: string, siteName: string) {
    const cleanTitle = title.trim();
    const cleanSiteName = siteName.trim();

    if (!cleanTitle) return cleanSiteName;
    if (!cleanSiteName) return cleanTitle;

    return cleanTitle.includes(cleanSiteName)
        ? cleanTitle
        : `${cleanTitle} | ${cleanSiteName}`;
}

export async function generateMetadata(): Promise<Metadata> {
    const { branding, integrations, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const t = createTranslator(translations, locale);
    const canonical = absoluteLocalizedUrl("/", locale);
    const title = withDynamicSiteTitle(
        t("meta.default.title", {
            ka: "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები",
            en: "IT Infrastructure and Security Systems",
            ru: "IT-инфраструктура и системы безопасности",
        }),
        siteName,
    );
    const siteDescription = t("meta.default.description", {
        ka: "SafeTech უზრუნველყოფს ვიდეომეთვალყურეობის, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალურ გადაწყვეტილებებს ბიზნესისთვის.",
        en: "Professional CCTV, access control, networking, and server infrastructure solutions for businesses.",
        ru: "Профессиональные решения для видеонаблюдения, контроля доступа, сетевой и серверной инфраструктуры для бизнеса.",
    });
    const alternateLocales = supportedLocales
        .filter((item) => item !== locale)
        .map(getOgLocale);

    return {
        metadataBase: new URL(SITE_URL),
        applicationName: siteName,
        authors: [{ name: siteName, url: SITE_URL }],
        creator: siteName,
        publisher: siteName,
        title,
        description: siteDescription,
        keywords: [
            "CCTV",
            "IT Infrastructure",
            "Networking",
            "Access Control",
            "Security Systems",
            "Tbilisi",
        ],
        openGraph: {
            type: "website",
            locale: getOgLocale(locale),
            alternateLocale: alternateLocales,
            url: canonical,
            siteName,
            title,
            description: siteDescription,
            images: [
                {
                    url: absoluteSiteUrl(branding.defaultImage),
                    alt: siteName,
                },
            ],
        },
        twitter: {
            card: "summary_large_image",
            title,
            description: siteDescription,
            images: [absoluteSiteUrl(branding.defaultImage)],
        },
        robots: {
            index: true,
            follow: true,
            googleBot: {
                index: true,
                follow: true,
                "max-image-preview": "large",
                "max-snippet": -1,
                "max-video-preview": -1,
            },
        },
        alternates: {
            canonical,
            languages: {
                ...buildLanguageAlternates("/"),
                "x-default": absoluteSiteUrl("/"),
            },
        },
        icons: {
            icon: branding.favicon,
            shortcut: branding.favicon,
            apple: branding.favicon,
        },
        verification: {
            google:
                integrations.googleSiteVerification ||
                process.env.GOOGLE_SITE_VERIFICATION ||
                undefined,
            other: {
                ...(integrations.bingSiteVerification
                    ? { "msvalidate.01": integrations.bingSiteVerification }
                    : {}),
                ...(integrations.yandexSiteVerification
                    ? { "yandex-verification": integrations.yandexSiteVerification }
                    : {}),
            },
        },
    };
}

export default async function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    const { contact, branding, integrations, locale, socialLinks, translations } =
        await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const t = createTranslator(translations, locale);
    const skipLabel = t("accessibility.skipToContent", {
        ka: "ძირითად კონტენტზე გადასვლა",
        en: "Skip to main content",
        ru: "Перейти к основному содержанию",
    });
    const googleTagManagerId =
        integrations.googleTagManagerId || process.env.NEXT_PUBLIC_GTM_ID?.trim();
    const googleAnalyticsId =
        integrations.googleAnalyticsId || process.env.NEXT_PUBLIC_GA_ID?.trim();
    const metaPixelId =
        integrations.metaPixelId || process.env.NEXT_PUBLIC_META_PIXEL_ID?.trim();
    const marketingEnabled =
        integrations.marketingEnabled ||
        Boolean(googleTagManagerId || googleAnalyticsId || metaPixelId);
    const publicApiOrigin = (() => {
        try {
            return new URL(process.env.NEXT_PUBLIC_API_URL || "").origin;
        } catch {
            return "";
        }
    })();

    const organizationSchema: Record<string, unknown> = {
        "@context": "https://schema.org",
        "@id": `${absoluteSiteUrl("/")}#organization`,
        ...buildOrganizationEntity({
            branding,
            contact,
            contactType: "customer support",
            siteName,
            socialUrls: socialLinks.map((item) => item.href),
            url: absoluteSiteUrl("/"),
        }),
    };

    return (
        <html
            lang={getLanguageTag(locale)}
            className={`${georgianFont.variable} dark scroll-smooth`}
            suppressHydrationWarning
        >
            <head>
                {publicApiOrigin ? (
                    <>
                        <link rel="dns-prefetch" href={publicApiOrigin} />
                        <link
                            rel="preconnect"
                            href={publicApiOrigin}
                            crossOrigin="anonymous"
                        />
                    </>
                ) : null}
            </head>
            <body
                className="
                    bg-background
                    text-on-background
                    font-body-md
                    antialiased
                    overflow-x-hidden
                    min-h-screen
                "
            >
                <script
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{
                        __html: JSON.stringify(organizationSchema).replace(
                            /</g,
                            "\\u003c",
                        ),
                    }}
                />

                <LocalizationProvider
                    locale={locale}
                    translations={translations}
                >
                    <MarketingPixels
                        enabled={marketingEnabled}
                        googleAnalyticsId={googleAnalyticsId}
                        googleTagManagerId={googleTagManagerId}
                        metaPixelId={metaPixelId}
                    />
                    <ConsultationProvider>
                        <div
                            className="
                                relative
                                flex
                                min-h-screen
                                flex-col
                            "
                        >
                            <a
                                href="#main-content"
                                className="sr-only z-[100] rounded-lg bg-primary-container px-4 py-3 text-on-primary-container focus:not-sr-only focus:fixed focus:left-4 focus:top-4"
                            >
                                {skipLabel}
                            </a>
                            <Navbar />

                            <main id="main-content" className="flex-1" tabIndex={-1}>
                                {children}
                            </main>

                            <Footer marketingEnabled={marketingEnabled} />
                        </div>
                        <FloatingWhatsAppSlot
                            phone={contact.whatsapp}
                            message={contact.whatsappMessage}
                        />
                    </ConsultationProvider>
                </LocalizationProvider>
            </body>
        </html>
    );
}
