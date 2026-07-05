import type { Metadata } from "next";

import "./globals.css";

import FloatingWhatsApp from "@/components/Contact/FloatingWhatsApp";
import Footer from "@/components/Footer/Footer";
import Navbar from "@/components/Navbar/Navbar";
import GoogleAnalytics from "@/components/analytics/GoogleAnalytics";
import ConsultationProvider from "@/components/consultation/ConsultationProvider";
import { absoluteSiteUrl, SITE_NAME, SITE_URL } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

const siteDescription =
    "ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალური გადაწყვეტილებები ბიზნესისთვის.";

function withDynamicSiteTitle(title: string, siteName: string) {
    return title.includes(siteName) ? title : `${title} | ${siteName}`;
}

export async function generateMetadata(): Promise<Metadata> {
    const { branding } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const title = withDynamicSiteTitle(
        "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები",
        siteName,
    );

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
            locale: "ka_GE",
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
    const { contact, branding } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
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
                availableLanguage: ["ka", "en"],
            },
        ],
        areaServed: "Georgia",
    };

    return (
        <html
            lang="ka"
            className="dark scroll-smooth"
            suppressHydrationWarning
        >
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
                <GoogleAnalytics />

                <script
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{
                        __html: JSON.stringify(organizationSchema).replace(
                            /</g,
                            "\\u003c",
                        ),
                    }}
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
                        <Navbar />

                        <main className="flex-1">{children}</main>

                        <Footer />
                    </div>
                    <FloatingWhatsApp
                        phone={contact.whatsapp}
                        message={contact.whatsappMessage}
                    />
                </ConsultationProvider>
            </body>
        </html>
    );
}
