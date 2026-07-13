export type TeamMember = {
    id?: number;
    firstName: string;
    lastName: string;
    position: string;
    image: string;
    socials: {
        linkedin?: string;
        facebook?: string;
        instagram?: string;
        tiktok?: string;
        email?: string;
    };
};
