export const projectCategories = [
    { value: "all", label: "ყველა" },
    { value: "offices", label: "ოფისები" },
    { value: "hotels", label: "სასტუმროები" },
    { value: "warehouses", label: "საწყობები" },
    { value: "factories", label: "საწარმოები" },
] as const;

export type ProjectCategory = (typeof projectCategories)[number]["value"];

export type ProjectMetric = {
    value: string;
    label: string;
};

export type FeaturedProject = {
    title: string;
    category: string;
    image: string;
    imageAlt: string;
    specs: Array<{ label: string; value: string }>;
};

export type Project = {
    slug?: string;
    title: string;
    description: string;
    category: Exclude<ProjectCategory, "all">;
    icon: string;
    accent: "primary" | "secondary";
    technology: string;
};

export type ProjectStandard = {
    title: string;
    description: string;
    icon: string;
    accent: "primary" | "secondary";
};

export const projectMetrics: ProjectMetric[] = [
    { value: "500+", label: "კამერა" },
    { value: "120+", label: "მოწყობილობა" },
    { value: "50+", label: "სერვერი" },
    { value: "20+", label: "კორპორატიული პროექტი" },
];

export const featuredProjects: FeaturedProject[] = [
    {
        title: "ბანკის უსაფრთხოების განახლება",
        category: "უსაფრთხოების ინფრასტრუქტურა",
        image: "/brand-preview.svg",
        imageAlt: "ბანკის უსაფრთხოების მონიტორინგის ინფრასტრუქტურა",
        specs: [
            { label: "კამერები", value: "128 ერთეული" },
            { label: "ოპტიკა", value: "10G Backbone" },
            { label: "საცავი", value: "256TB RAID" },
        ],
    },
    {
        title: "მონაცემთა ცენტრის ტოპოლოგია",
        category: "ქსელური არქიტექტურა",
        image: "/brand-preview.svg",
        imageAlt: "მონაცემთა ცენტრის ქსელური ტოპოლოგია",
        specs: [
            { label: "წვდომის წერტილი", value: "48 კვანძი" },
            { label: "მაგისტრალი", value: "40G QSFP+" },
            { label: "VLAN", value: "12 სეგმენტი" },
        ],
    },
    {
        title: "სახელმწიფო ჰაბი",
        category: "სტრატეგიული უსაფრთხოება",
        image: "/brand-preview.svg",
        imageAlt: "მართვისა და მონიტორინგის უსაფრთხო ცენტრი",
        specs: [
            { label: "ავტორიზაცია", value: "Face ID" },
            { label: "AI ტექნოლოგია", value: "Surveillance" },
            { label: "მუშაობა", value: "24/7" },
        ],
    },
];

export const projects: Project[] = [
    {
        title: "ლუქს სასტუმროს Wi‑Fi",
        description:
            "მაღალი სიმკვრივის უსადენო დაფარვა 200-ზე მეტი ნომრისთვის, უწყვეტი როუმინგით.",
        category: "hotels",
        icon: "wifi",
        accent: "primary",
        technology: "Ubiquiti",
    },
    {
        slug: "global-logistics-hub",
        title: "ლოგისტიკური ჰაბის CCTV",
        description:
            "5 000 მ² საწყობის ტერიტორიის ცენტრალიზებული ვიდეომონიტორინგი და ანალიტიკა.",
        category: "warehouses",
        icon: "videocam",
        accent: "secondary",
        technology: "Synology",
    },
    {
        title: "ინდუსტრიული სერვერული კლასტერი",
        description:
            "წარმოებისათვის შექმნილი, შეფერხებებისადმი მდგრადი სერვერული ინფრასტრუქტურა.",
        category: "factories",
        icon: "dns",
        accent: "primary",
        technology: "Cisco",
    },
    {
        title: "ჭკვიანი ოფისის დაშვება",
        description:
            "თანამშრომლებისა და სტუმრების ერთიანი დაშვების კონტროლი და სამუშაო დროის აღრიცხვა.",
        category: "offices",
        icon: "badge",
        accent: "secondary",
        technology: "Hikvision",
    },
];

export const projectStandards: ProjectStandard[] = [
    {
        title: "სტრუქტურირებული კაბელირება",
        description:
            "მარკირება, საკაბელო არხების მართვა და ერთიანი საინჟინრო სტანდარტი ყველა კვანძში.",
        icon: "settings_input_component",
        accent: "primary",
    },
    {
        title: "VLAN არქიტექტურა",
        description:
            "ქსელის ლოგიკური სეგმენტაცია, წვდომის კონტროლი და კრიტიკული რესურსების იზოლაცია.",
        icon: "hub",
        accent: "secondary",
    },
    {
        title: "დისტანციური მონიტორინგი",
        description:
            "სისტემის მდგომარეობის უწყვეტი კონტროლი და პროაქტიული რეაგირება ნებისმიერი წერტილიდან.",
        icon: "visibility",
        accent: "primary",
    },
];
