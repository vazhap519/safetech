import type { TeamMember } from "@/lib/team";
import SocialIcon, { type SocialNetwork } from "@/components/ui/SocialIcon";

const labels = {
    linkedin: "LinkedIn",
    facebook: "Facebook",
    instagram: "Instagram",
    tiktok: "TikTok",
    email: "ელფოსტა",
};

export default function TeamMemberSocials({ member }: { member: TeamMember }) {
    const fullName = `${member.firstName} ${member.lastName}`;

    return (
        <div aria-label={`${fullName} — სოციალური ქსელები`} className="mt-unit-md flex items-center gap-2">
            {(Object.entries(member.socials) as [keyof typeof labels, string][]).map(([network, href]) => {
                const isExternal = href.startsWith("http");

                return (
                    <a
                        aria-label={`${fullName} — ${labels[network]}`}
                        className="grid size-10 place-items-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition-colors hover:border-secondary/60 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        href={href}
                        key={network}
                        rel={isExternal ? "noopener noreferrer" : undefined}
                        target={isExternal ? "_blank" : undefined}
                        title={labels[network]}
                    >
                        <SocialIcon network={network as SocialNetwork} />
                    </a>
                );
            })}
        </div>
    );
}
