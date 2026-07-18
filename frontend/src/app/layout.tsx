import type { Metadata } from "next";

import "./globals.css";

import FloatingWhatsAppSlot from "@/components/Contact/FloatingWhatsAppSlot";
import Footer from "@/components/Footer/Footer";
import Navbar from "@/components/Navbar/Navbar";
import ConsultationProvider from "@/components/consultation/ConsultationProvider";
import LocalizationProvider from "@/components/providers/LocalizationProvider";
import { GoogleTagManager } from "@next/third-parties/google";
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
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

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
    const { branding, locale, translations } = await getSiteSettings();
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
        verification: process.env.GOOGLE_SITE_VERIFICATION
            ? { google: process.env.GOOGLE_SITE_VERIFICATION }
            : undefined,
    };
}

export default async function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    const { contact, branding, locale, socialLinks, translations } =
        await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const gtmId = process.env.NEXT_PUBLIC_GTM_ID?.trim();
    const publicApiOrigin = (() => {
        try {
            return new URL(process.env.NEXT_PUBLIC_API_URL || "").origin;
        } catch {
            return "";
        }
    })();

    const contactPoint =
        contact.phone || contact.email
            ? [
                  {
                      "@type": "ContactPoint",
                      contactType: "customer support",
                      ...(contact.phone ? { telephone: contact.phone } : {}),
                      ...(contact.email ? { email: contact.email } : {}),
                      areaServed: "GE",
                      availableLanguage: ["ka", "en", "ru"],
                  },
              ]
            : undefined;

    const organizationSchema: Record<string, unknown> = {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": `${absoluteSiteUrl("/")}#organization`,
        name: siteName,
        url: absoluteSiteUrl("/"),
        image: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        areaServed: "Georgia",
        ...(contact.phone ? { telephone: contact.phone } : {}),
        ...(contact.email ? { email: contact.email } : {}),
        ...(contact.address
            ? {
                  address: {
                      "@type": "PostalAddress",
                      streetAddress: contact.address,
                      addressCountry: "GE",
                      addressLocality: "Tbilisi",
                  },
              }
            : {}),
        ...(contactPoint ? { contactPoint } : {}),
        ...(socialLinks.length
            ? { sameAs: socialLinks.map((item) => item.href) }
            : {}),
    };

    return (
        <html
            lang={getLanguageTag(locale)}
            className="dark scroll-smooth"
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
                {gtmId ? <GoogleTagManager gtmId={gtmId} /> : null}
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
                    <ConsultationProvider>
                        <div
                            className="
                                relative
                                flex
                                min-h-screen
                                flex-col
                            "
                        >
                            <Navbar />

                            <main className="flex-1">{children}</main>

                            <Footer />
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
