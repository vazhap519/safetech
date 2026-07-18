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
                    key={`${duplicate ? "duplicate-" : ""}${member.firstName}-${member.lastName}`}
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
    const eyebrow = translateText(
        translations,
        "about.team.eyebrow",
        locale,
        null,
    );
    const title = translateText(translations, "about.team.title", locale, null);
    const description = translateText(
        translations,
        "about.team.description",
        locale,
        null,
    );
    const regionLabel = translateText(
        translations,
        "about.team.regionLabel",
        locale,
        null,
    );

    if (!teamMembers.length) return null;

    return (
        <section
            aria-labelledby={title ? "team-title" : undefined}
            className="overflow-hidden bg-surface-container-lowest py-unit-xl"
        >
            {eyebrow || title || description ? (
                <header className="mx-auto mb-unit-xl max-w-3xl px-margin-desktop text-center">
                    {eyebrow ? (
                        <p className="mb-unit-sm font-mono-sm text-mono-sm uppercase tracking-[0.25em] text-secondary">
                            {eyebrow}
                        </p>
                    ) : null}
                    {title ? (
                        <h2
                            className="font-headline-xl text-headline-xl text-white"
                            id="team-title"
                        >
                            {title}
                        </h2>
                    ) : null}
                    {description ? (
                        <p className="mx-auto mt-unit-md max-w-2xl font-body-md text-body-md leading-relaxed text-on-surface-variant">
                            {description}
                        </p>
                    ) : null}
                </header>
            ) : null}

            <div
                aria-label={regionLabel || undefined}
                className="team-marquee"
                role="region"
            >
                <div className="team-marquee-track">
                    <TeamList members={teamMembers} />
                    <TeamList duplicate members={teamMembers} />
                </div>
            </div>
        </section>
    );
}
