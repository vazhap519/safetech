import type { SocialNetwork } from "@/components/ui/SocialIcon";

export type TeamMember = {
    id?: number;
    firstName: string;
    lastName: string;
    position: string;
    image: string;
    socials: Partial<Record<SocialNetwork, string>>;
};
