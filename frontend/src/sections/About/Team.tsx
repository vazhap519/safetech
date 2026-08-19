import TeamMemberCard from "@/components/About/TeamMemberCard";
import { getBackendTeam } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import type { TeamMember } from "@/lib/team";
import { translateText } from "@/lib/translations";

function TeamList({
    members,
    duplicate = false,
}: {
    members: TeamMember[];
    duplicate?: boolean;
}) {
    return (
        <ul aria-hidden={duplicate || undefined} className="team-marquee-group">
            {members.map((member) => (
                <li
                    key={`${duplicate ? "duplicate-" : ""}${member.id ?? `${member.firstName}-${member.lastName}`}`}
                >
                    <TeamMemberCard member={member} />
                </li>
            ))}
        </ul>
    );
}

export default async function TeamSection() {
    const [teamMembers, { locale, translations }] = await Promise.all([
        getBackendTeam(),
        getSiteSettings(),
    ]);

    if (!teamMembers.length) return null;

    const fallback = {
        ka: {
            eyebrow: "SafeTech გუნდი",
            title: "ადამიანები SafeTech-ის უკან",
            description:
                "გაიცანით გუნდი, რომელიც პასუხისმგებელია ტექნიკურ დაგეგმვაზე, მონტაჟზე, კონფიგურაციასა და მხარდაჭერაზე.",
            regionLabel: "SafeTech-ის გუნდის წევრები",
        },
        en: {
            eyebrow: "SafeTech team",
            title: "The people behind SafeTech",
            description:
                "Meet the team responsible for technical planning, installation, configuration, and support.",
            regionLabel: "SafeTech team members",
        },
        ru: {
            eyebrow: "Команда SafeTech",
            title: "Люди, стоящие за SafeTech",
            description:
                "Познакомьтесь с командой, отвечающей за техническое планирование, монтаж, настройку и поддержку.",
            regionLabel: "Члены команды SafeTech",
        },
    }[locale];

    const eyebrow =
        translateText(translations, "about.team.eyebrow", locale, null) ||
        fallback.eyebrow;
    const title =
        translateText(translations, "about.team.title", locale, null) ||
        fallback.title;
    const description =
        translateText(translations, "about.team.description", locale, null) ||
        fallback.description;
    const regionLabel =
        translateText(translations, "about.team.regionLabel", locale, null) ||
        fallback.regionLabel;

    const useMarquee = teamMembers.length >= 4;

    return (
        <section
            aria-labelledby="team-title"
            className="overflow-hidden bg-surface-container-lowest py-unit-xl"
        >
            <header className="mx-auto mb-unit-xl max-w-3xl px-margin-desktop text-center">
                <p className="mb-unit-sm font-mono-sm text-mono-sm uppercase tracking-[0.25em] text-secondary">
                    {eyebrow}
                </p>
                <h2
                    className="font-headline-xl text-headline-xl text-white"
                    id="team-title"
                >
                    {title}
                </h2>
                <p className="mx-auto mt-unit-md max-w-2xl font-body-md text-body-md leading-relaxed text-on-surface-variant">
                    {description}
                </p>
            </header>

            {useMarquee ? (
                <div
                    aria-label={regionLabel}
                    className="team-marquee"
                    role="region"
                >
                    <div className="team-marquee-track">
                        <TeamList members={teamMembers} />
                        <TeamList duplicate members={teamMembers} />
                    </div>
                </div>
            ) : (
                <div
                    aria-label={regionLabel}
                    className="mx-auto flex max-w-6xl flex-wrap justify-center gap-6 px-margin-desktop"
                    role="region"
                >
                    {teamMembers.map((member) => (
                        <TeamMemberCard
                            key={member.id ?? `${member.firstName}-${member.lastName}`}
                            member={member}
                        />
                    ))}
                </div>
            )}
        </section>
    );
}
