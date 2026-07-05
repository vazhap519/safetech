export type TeamMember = {
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

const defaultSocials = {
    linkedin: "https://www.linkedin.com/",
    facebook: "https://www.facebook.com/",
    email: "mailto:info@safetech.ge",
};

const defaultTeamImage = "/team-avatar.svg";

export const teamMembers: TeamMember[] = [
    {
        firstName: "გიორგი",
        lastName: "მაისურაძე",
        position: "აღმასრულებელი დირექტორი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "ნინო",
        lastName: "ბერიძე",
        position: "ტექნიკური დირექტორი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "ლევან",
        lastName: "კაპანაძე",
        position: "ქსელის მთავარი არქიტექტორი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "ანა",
        lastName: "გელაშვილი",
        position: "პროექტების მენეჯერი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "დავით",
        lastName: "ჩხეიძე",
        position: "უსაფრთხოების სისტემების ინჟინერი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "მარიამ",
        lastName: "ლომიძე",
        position: "სერვერული ინფრასტრუქტურის სპეციალისტი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "ირაკლი",
        lastName: "ნადირაძე",
        position: "მონტაჟის გუნდის ხელმძღვანელი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
    {
        firstName: "თამარ",
        lastName: "დვალიშვილი",
        position: "ტექნიკური მხარდაჭერის ხელმძღვანელი",
        image: defaultTeamImage,
        socials: defaultSocials,
    },
];
