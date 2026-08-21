import type { TeamMember } from "@/lib/team";
import SocialIcon, {
    type SocialNetwork,
} from "@/components/ui/SocialIcon";

const labels: Record<SocialNetwork, string> = {
    linkedin: "LinkedIn",
    facebook: "Facebook",
    instagram: "Instagram",
    tiktok: "TikTok",
    x: "X",
    youtube: "YouTube",
    telegram: "Telegram",
    whatsapp: "WhatsApp",
    viber: "Viber",
    pinterest: "Pinterest",
    email: "Email",
};

function isSocialNetwork(network: string): network is SocialNetwork {
    return Object.prototype.hasOwnProperty.call(labels, network);
}

function normalizeHref(network: SocialNetwork, href: string) {
    const value = href.trim();

    if (!value) return "";
    if (network === "email") {
        return value.startsWith("mailto:") ? value : `mailto:${value}`;
    }
    if (network === "whatsapp") {
        if (value.startsWith("http")) return value;
        const digits = value.replace(/[^\d]/g, "");
        return digits ? `https://wa.me/${digits}` : "";
    }
    if (network === "viber") {
        if (value.startsWith("http") || value.startsWith("viber:")) {
            return value;
        }
        const digits = value.replace(/[^\d]/g, "");
        return digits ? `viber://chat?number=%2B${digits}` : "";
    }

    return value.startsWith("http://") || value.startsWith("https://")
        ? value
        : `https://${value}`;
}

export default function TeamMemberSocials({ member }: { member: TeamMember }) {
    const fullName = `${member.firstName} ${member.lastName}`;
    const links = Object.entries(member.socials)
        .filter(
            (entry): entry is [string, string] =>
                typeof entry[1] === "string" && Boolean(entry[1].trim()),
        )
        .flatMap(([network, href]) => {
            if (!isSocialNetwork(network)) return [];

            const normalizedHref = normalizeHref(network, href);

            return normalizedHref
                ? [{ network, href: normalizedHref }]
                : [];
        });

    if (!links.length) return null;

    return (
        <div aria-label={`${fullName} — სოციალური ქსელები`} className="mt-unit-md flex items-center gap-2">
            {links.map(({ network, href }) => {
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
                        <SocialIcon network={network} />
                    </a>
                );
            })}
        </div>
    );
}
