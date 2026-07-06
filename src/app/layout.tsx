import type { Metadata } from "next";

import "./globals.css";

import FloatingWhatsApp from "@/components/Contact/FloatingWhatsApp";
import Footer from "@/components/Footer/Footer";
import Navbar from "@/components/Navbar/Navbar";
import ConsultationProvider from "@/components/consultation/ConsultationProvider";
import LocalizationProvider from "@/components/providers/LocalizationProvider";
import { getOgLocale } from "@/lib/locales";
import { absoluteSiteUrl, SITE_NAME, SITE_URL } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
import { GoogleTagManager } from "@next/third-parties/google";
function withDynamicSiteTitle(title: string, siteName: string) {
    return title.includes(siteName) ? title : `${title} | ${siteName}`;
}

export async function generateMetadata(): Promise<Metadata> {
    const { branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const t = createTranslator(translations, locale);
    const title = withDynamicSiteTitle(
        t("meta.default.title", {
            ka: "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები",
            en: "IT Infrastructure and Security Systems",
            ru: "IT-инфраструктура и системы безопасности",
        }),
        siteName,
    );
    const siteDescription = t("meta.default.description", {
        ka: "ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალური გადაწყვეტილებები ბიზნესისთვის.",
        en: "Professional CCTV, access control, networking, and server infrastructure solutions for businesses.",
        ru: "Профессиональные решения для видеонаблюдения, контроля доступа, сетевой и серверной инфраструктуры для бизнеса.",
    });

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
            "თბილისი",
        ],
        openGraph: {
            type: "website",
            locale: getOgLocale(locale),
            url: absoluteSiteUrl("/"),
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
            canonical: absoluteSiteUrl("/"),
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
    const { contact, branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const publicApiOrigin = (() => {
        try {
            return new URL(
                process.env.NEXT_PUBLIC_API_URL || "",
            ).origin;
        } catch {
            return "";
        }
    })();
    const organizationSchema = {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": `${absoluteSiteUrl("/")}#organization`,
        name: siteName,
        url: absoluteSiteUrl("/"),
        image: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        telephone: contact.phone,
        email: contact.email,
        address: {
            "@type": "PostalAddress",
            streetAddress: contact.address,
            addressCountry: "GE",
            addressLocality: "Tbilisi",
        },
        contactPoint: [
            {
                "@type": "ContactPoint",
                contactType: "customer support",
                telephone: contact.phone,
                email: contact.email,
                areaServed: "GE",
                availableLanguage: ["ka", "en", "ru"],
            },
        ],
        areaServed: "Georgia",
    };

    return (
        <html
            lang={locale}
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
                className={`
                    bg-background
                    text-on-background
                    font-body-md
                    antialiased
                    overflow-x-hidden
                    min-h-screen
                `}
            >
                <GoogleTagManager gtmId="GTM-PHSJ3MHV" />
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
                        <FloatingWhatsApp
                            phone={contact.whatsapp}
                            message={contact.whatsappMessage}
                        />
                    </ConsultationProvider>
                </LocalizationProvider>
            </body>
        </html>
    );
}
