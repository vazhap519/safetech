import TeamMemberCard from "@/components/About/TeamMemberCard";
import { getBackendTeam } from "@/lib/backend";
import type { TeamMember } from "@/lib/team";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

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
    const t = createTranslator(translations, locale);

    return (
        <section
            aria-labelledby="team-title"
            className="overflow-hidden bg-surface-container-lowest py-unit-xl"
        >
            <header className="mx-auto mb-unit-xl max-w-3xl px-margin-desktop text-center">
                <p className="mb-unit-sm font-mono-sm text-mono-sm uppercase tracking-[0.25em] text-secondary">
                    {t("about.team.eyebrow", {
                        ka: "SafeTech გუნდი",
                        en: "SafeTech Team",
                        ru: "Команда SafeTech",
                    })}
                </p>
                <h2
                    className="font-headline-xl text-headline-xl text-white"
                    id="team-title"
                >
                    {t("about.team.title", {
                        ka: "ჩვენი გუნდი",
                        en: "Our team",
                        ru: "Наша команда",
                    })}
                </h2>
                <p className="mx-auto mt-unit-md max-w-2xl font-body-md text-body-md leading-relaxed text-on-surface-variant">
                    {t("about.team.description", {
                        ka: "პროფესიონალები, რომლებიც ქმნიან უსაფრთხო და საიმედო ტექნოლოგიურ ინფრასტრუქტურას.",
                        en: "Professionals building secure and dependable technology infrastructure.",
                        ru: "Профессионалы, создающие безопасную и надежную технологическую инфраструктуру.",
                    })}
                </p>
            </header>

            <div
                aria-label={t("about.team.regionLabel", {
                    ka: "თანამშრომლების სია",
                    en: "Team members list",
                    ru: "Список сотрудников",
                })}
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
