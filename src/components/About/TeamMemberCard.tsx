import TeamMemberSocials from "@/components/About/TeamMemberSocials";
import Image from "@/components/ui/Image";
import type { TeamMember } from "@/lib/team";

export default function TeamMemberCard({ member }: { member: TeamMember }) {
    const fullName = `${member.firstName} ${member.lastName}`;

    return (
        <article className="glass-card group w-[clamp(16rem,24vw,20rem)] shrink-0 overflow-hidden rounded-3xl">
            <div className="relative aspect-[4/5] overflow-hidden bg-surface-container-high">
                <Image
                    alt={`${fullName} — ${member.position}`}
                    className="object-cover transition-transform duration-500 motion-safe:group-hover:scale-105"
                    fill
                    sizes="(max-width: 640px) 256px, 320px"
                    src={member.image}
                />
                <div aria-hidden="true" className="absolute inset-0 bg-gradient-to-t from-background/80 via-transparent to-transparent" />
            </div>
            <div className="flex min-h-[10.5rem] flex-col p-unit-md sm:p-unit-lg">
                <h3 className="font-headline-md text-headline-md text-white">{fullName}</h3>
                <p className="mt-2 font-label-md text-label-md leading-relaxed text-secondary">{member.position}</p>
                <div className="mt-auto">
                    <TeamMemberSocials member={member} />
                </div>
            </div>
        </article>
    );
}
