import TeamMemberSocials from "@/components/About/TeamMemberSocials";
import Image from "@/components/ui/Image";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { TeamMember } from "@/lib/team";

export default function TeamMemberCard({
    member,
    profileLabel,
}: {
    member: TeamMember;
    profileLabel: string;
}) {
    const fullName = `${member.firstName} ${member.lastName}`;
    const profileHref = member.id ? `/about/team/${member.id}` : null;

    return (
        <article className="glass-card group w-[clamp(16rem,24vw,20rem)] shrink-0 overflow-hidden rounded-3xl">
            {profileHref ? (
                <LocalizedLink
                    aria-label={`${profileLabel}: ${fullName}`}
                    className="relative block aspect-[4/5] overflow-hidden bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-secondary"
                    href={profileHref}
                >
                    <Image
                        alt={`${fullName} — ${member.position}`}
                        className="object-cover transition-transform duration-500 motion-safe:group-hover:scale-105"
                        fill
                        sizes="(max-width: 640px) 256px, 320px"
                        src={member.image}
                    />
                    <div aria-hidden="true" className="absolute inset-0 bg-gradient-to-t from-background/80 via-transparent to-transparent" />
                </LocalizedLink>
            ) : (
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
            )}
            <div className="flex min-h-[10.5rem] flex-col p-unit-md sm:p-unit-lg">
                <h3 className="font-headline-md text-headline-md text-white">
                    {profileHref ? (
                        <LocalizedLink
                            className="transition-colors hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href={profileHref}
                        >
                            {fullName}
                        </LocalizedLink>
                    ) : (
                        fullName
                    )}
                </h3>
                <p className="mt-2 font-label-md text-label-md leading-relaxed text-secondary">{member.position}</p>
                <div className="mt-auto">
                    <TeamMemberSocials member={member} />
                    {profileHref ? (
                        <LocalizedLink
                            className="mt-4 inline-flex min-h-11 items-center gap-2 rounded-full border border-secondary/30 px-4 py-2 text-sm font-semibold text-secondary transition hover:border-secondary hover:bg-secondary/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href={profileHref}
                        >
                            {profileLabel}
                            <Icon className="size-4" name="arrow-right" />
                        </LocalizedLink>
                    ) : null}
                </div>
            </div>
        </article>
    );
}
