import type { SocialNetwork } from "@/components/ui/SocialIcon";

export type TeamMemberCertificate = {
    id: number;
    src: string;
    thumbnail: string;
    alt: string;
};

export type TeamMember = {
    id?: number;
    firstName: string;
    lastName: string;
    position: string;
    bio?: string | null;
    image: string;
    certificates?: TeamMemberCertificate[];
    socials: Partial<Record<SocialNetwork, string>>;
};
