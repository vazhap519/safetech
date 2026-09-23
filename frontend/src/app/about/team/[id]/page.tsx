import type { Metadata } from "next";
import { notFound } from "next/navigation";

import CertificateSlider, {
    type CertificateSliderLabels,
} from "@/components/About/CertificateSlider";
import TeamMemberSocials from "@/components/About/TeamMemberSocials";
import JsonLd from "@/components/seo/JsonLd";
import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getBackendTeamMember } from "@/lib/backend";
import { getLanguageTag, type Locale } from "@/lib/locales";
import {
    absoluteLocalizedUrl,
    absoluteSiteUrl,
    createMetadata,
    withSiteTitle,
} from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { buildBreadcrumbSchema } from "@/lib/structured-data";

type TeamMemberPageProps = {
    params: Promise<{ id: string; locale?: string }>;
};

type PageLabels = {
    team: string;
    back: string;
    eyebrow: string;
    profile: string;
    bioFallback: string;
    contact: string;
    metadataDescription: (name: string, position: string) => string;
    certificates: CertificateSliderLabels;
};

const labelsByLocale: Record<Locale, PageLabels> = {
    ka: {
        team: "გუნდი",
        back: "გუნდში დაბრუნება",
        eyebrow: "SafeTech-ის გუნდი",
        profile: "პროფესიული პროფილი",
        bioFallback:
            "SafeTech-ის სპეციალისტი, რომელიც მონაწილეობს ტექნიკური გადაწყვეტილებების დაგეგმვაში, შესრულებასა და ხარისხის კონტროლში.",
        contact: "კონსულტაციის მოთხოვნა",
        metadataDescription: (name, position) =>
            `${name} — ${position}. გაიცანით SafeTech-ის სპეციალისტის გამოცდილება და პროფესიული სერტიფიკატები.`,
        certificates: {
            eyebrow: "კვალიფიკაცია",
            title: "პროფესიული სერტიფიკატები",
            description:
                "გაეცანით სპეციალისტის კვალიფიკაციისა და პროფესიული განვითარების დამადასტურებელ სერტიფიკატებს.",
            certificate: "სერტიფიკატი",
            previous: "წინა სერტიფიკატი",
            next: "შემდეგი სერტიფიკატი",
            open: "სერტიფიკატის სრულად ნახვა",
            close: "გალერეის დახურვა",
            dialog: "სერტიფიკატების სრულეკრანიანი გალერეა",
        },
    },
    en: {
        team: "Team",
        back: "Back to the team",
        eyebrow: "SafeTech team",
        profile: "Professional profile",
        bioFallback:
            "A SafeTech specialist involved in planning, delivering, and quality-assuring technical solutions.",
        contact: "Request a consultation",
        metadataDescription: (name, position) =>
            `${name} — ${position}. Explore the SafeTech specialist's experience and professional certificates.`,
        certificates: {
            eyebrow: "Qualifications",
            title: "Professional certificates",
            description:
                "Explore certificates that demonstrate the specialist's qualifications and continuing professional development.",
            certificate: "Certificate",
            previous: "Previous certificate",
            next: "Next certificate",
            open: "Open full certificate",
            close: "Close gallery",
            dialog: "Full-screen certificate gallery",
        },
    },
    ru: {
        team: "Команда",
        back: "Вернуться к команде",
        eyebrow: "Команда SafeTech",
        profile: "Профессиональный профиль",
        bioFallback:
            "Специалист SafeTech, участвующий в проектировании, реализации и контроле качества технических решений.",
        contact: "Запросить консультацию",
        metadataDescription: (name, position) =>
            `${name} — ${position}. Опыт и профессиональные сертификаты специалиста SafeTech.`,
        certificates: {
            eyebrow: "Квалификация",
            title: "Профессиональные сертификаты",
            description:
                "Сертификаты, подтверждающие квалификацию и профессиональное развитие специалиста.",
            certificate: "Сертификат",
            previous: "Предыдущий сертификат",
            next: "Следующий сертификат",
            open: "Открыть сертификат полностью",
            close: "Закрыть галерею",
            dialog: "Полноэкранная галерея сертификатов",
        },
    },
};

export async function generateMetadata({
    params,
}: TeamMemberPageProps): Promise<Metadata> {
    const { id } = await params;
    const [{ branding, locale }, member] = await Promise.all([
        getSiteSettings(),
        getBackendTeamMember(id),
    ]);

    if (!member) {
        return {
            title: withSiteTitle(labelsByLocale[locale].team, branding.siteName),
            robots: { index: false, follow: false },
        };
    }

    const labels = labelsByLocale[locale];
    const fullName = `${member.firstName} ${member.lastName}`.trim();

    return createMetadata({
        title: `${fullName} — ${member.position}`,
        description: member.bio || labels.metadataDescription(fullName, member.position),
        path: `/about/team/${member.id}`,
        locale,
        image: member.image || branding.defaultImage || undefined,
        siteName: branding.siteName,
    });
}

export default async function TeamMemberPage({ params }: TeamMemberPageProps) {
    const { id } = await params;
    const [{ locale, branding }, member] = await Promise.all([
        getSiteSettings(),
        getBackendTeamMember(id),
    ]);

    if (!member?.id) notFound();

    const labels = labelsByLocale[locale];
    const fullName = `${member.firstName} ${member.lastName}`.trim();
    const path = `/about/team/${member.id}`;
    const memberUrl = absoluteLocalizedUrl(path, locale);
    const sameAs = Object.values(member.socials).filter(
        (value): value is string =>
            typeof value === "string" && /^https?:\/\//i.test(value),
    );
    const structuredData = [
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "@id": `${memberUrl}#person`,
            name: fullName,
            url: memberUrl,
            image: absoluteSiteUrl(member.image),
            jobTitle: member.position,
            description: member.bio || labels.bioFallback,
            inLanguage: getLanguageTag(locale),
            worksFor: {
                "@type": "Organization",
                name: branding.siteName,
                url: absoluteSiteUrl("/"),
            },
            ...(sameAs.length ? { sameAs } : {}),
        },
        {
            "@context": "https://schema.org",
            ...buildBreadcrumbSchema([
                {
                    name: labels.team,
                    url: absoluteLocalizedUrl("/about", locale),
                },
                { name: fullName, url: memberUrl },
            ]),
        },
    ];

    return (
        <article className="pt-20">
            <JsonLd data={structuredData} />

            <section className="relative overflow-hidden bg-surface-container-low py-unit-xl">
                <div aria-hidden="true" className="absolute inset-0 bg-[radial-gradient(circle_at_75%_25%,rgba(39,119,255,0.2),transparent_42%)]" />
                <div className="relative mx-auto grid max-w-container-max gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(280px,0.8fr)_1.2fr] lg:items-center lg:px-margin-desktop">
                    <div className="relative mx-auto aspect-[4/5] w-full max-w-md overflow-hidden rounded-[32px] border border-outline-variant/15 bg-surface-container-high shadow-[0_30px_90px_rgba(0,0,0,0.32)]">
                        <Image
                            alt={`${fullName} — ${member.position}`}
                            className="object-cover"
                            fill
                            priority
                            sizes="(max-width: 1024px) 90vw, 38vw"
                            src={member.image}
                        />
                        <div aria-hidden="true" className="absolute inset-0 bg-gradient-to-t from-background/65 via-transparent to-transparent" />
                    </div>

                    <div>
                        <LocalizedLink
                            className="inline-flex min-h-11 items-center gap-2 rounded-full border border-outline-variant/15 px-4 py-2 text-sm font-semibold text-on-surface-variant transition hover:border-secondary/50 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href="/about#team-title"
                        >
                            <span aria-hidden="true">←</span>
                            {labels.back}
                        </LocalizedLink>
                        <p className="mt-8 font-mono-sm text-mono-sm uppercase tracking-[0.22em] text-secondary">
                            {labels.eyebrow}
                        </p>
                        <h1 className="mt-3 font-display-lg text-display-lg text-white">
                            {fullName}
                        </h1>
                        <p className="mt-3 font-headline-sm text-headline-sm text-secondary">
                            {member.position}
                        </p>

                        <div className="mt-8 rounded-3xl border border-outline-variant/10 bg-surface-container-lowest/70 p-6 backdrop-blur-md sm:p-8">
                            <h2 className="font-headline-sm text-headline-sm text-white">
                                {labels.profile}
                            </h2>
                            <p className="mt-4 whitespace-pre-line font-body-md text-body-md leading-relaxed text-on-surface-variant">
                                {member.bio || labels.bioFallback}
                            </p>
                            <TeamMemberSocials member={member} />
                        </div>

                        <LocalizedLink
                            className="mt-8 inline-flex min-h-12 items-center justify-center rounded-full bg-primary px-6 py-3 font-semibold text-on-primary transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href="/contact"
                        >
                            {labels.contact}
                        </LocalizedLink>
                    </div>
                </div>
            </section>

            {member.certificates?.length ? (
                <CertificateSlider
                    certificates={member.certificates}
                    labels={labels.certificates}
                    memberName={fullName}
                />
            ) : null}
        </article>
    );
}
