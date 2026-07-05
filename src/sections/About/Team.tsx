import TeamMemberCard from "@/components/About/TeamMemberCard";
import type { TeamMember } from "@/lib/team";
import { getBackendTeam } from "@/lib/backend";

function TeamList({ members, duplicate = false }: { members: TeamMember[]; duplicate?: boolean }) {
    return (
        <ul aria-hidden={duplicate || undefined} className="team-marquee-group">
            {members.map((member) => (
                <li key={`${duplicate ? "duplicate-" : ""}${member.firstName}-${member.lastName}`}>
                    <TeamMemberCard member={member} />
                </li>
            ))}
        </ul>
    );
}

export default async function TeamSection() {
    const teamMembers = await getBackendTeam();
    return (
        <section aria-labelledby="team-title" className="overflow-hidden bg-surface-container-lowest py-unit-xl">
            <header className="mx-auto mb-unit-xl max-w-3xl px-margin-desktop text-center">
                <p className="mb-unit-sm font-mono-sm text-mono-sm uppercase tracking-[0.25em] text-secondary">SafeTech Team</p>
                <h2 className="font-headline-xl text-headline-xl text-white" id="team-title">ჩვენი გუნდი</h2>
                <p className="mx-auto mt-unit-md max-w-2xl font-body-md text-body-md leading-relaxed text-on-surface-variant">
                    პროფესიონალები, რომლებიც ქმნიან უსაფრთხო და საიმედო ტექნოლოგიურ ინფრასტრუქტურას.
                </p>
            </header>

            <div aria-label="თანამშრომლების სია" className="team-marquee" role="region">
                <div className="team-marquee-track">
                    <TeamList members={teamMembers} />
                    <TeamList duplicate members={teamMembers} />
                </div>
            </div>
        </section>
    );
}
