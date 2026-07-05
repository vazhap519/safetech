export type ProjectDetailCard = {
    icon: string;
    title: string;
    description: string;
    featured?: boolean;
};

export type ProjectDetail = {
    slug: string;
    name: string;
    title: string;
    description: string;
    seoDescription: string;
    image: string;
    imageAlt: string;
    meta: Array<{ label: string; value: string }>;
    scope: Array<{ value: string; label: string }>;
    specs: Array<{ value: string; label: string }>;
    challenges: ProjectDetailCard[];
    solutions: ProjectDetailCard[];
    process: Array<{ title: string; description: string }>;
    gallery: Array<{ src: string; alt: string }>;
    results: Array<{ value: string; title: string; description: string; accent: "primary" | "secondary" }>;
    testimonial: { quote: string; author: string; role: string };
    related: Array<{ slug: string; title: string; category: string; image: string; imageAlt: string }>;
};

export const projectDetails: ProjectDetail[] = [
    {
        slug: "global-logistics-hub",
        name: "გლობალური ლოგისტიკური ჰაბი",
        title: "გლობალური ლოგისტიკური ჰაბის ინფრასტრუქტურა",
        description: "ერთიან პლატფორმაში გაერთიანებული ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურა.",
        seoDescription: "SafeTech-ის მიერ განხორციელებული გლობალური ლოგისტიკური ჰაბის CCTV, ქსელური, დაშვების კონტროლისა და სერვერული ინფრასტრუქტურის პროექტი თბილისში.",
        image: "/brand-preview.svg",
        imageAlt: "გლობალური ლოგისტიკური ჰაბის ტექნოლოგიური ინფრასტრუქტურა",
        meta: [
            { label: "ინდუსტრია", value: "ლოგისტიკა" },
            { label: "ლოკაცია", value: "თბილისი" },
            { label: "წელი", value: "2023" },
        ],
        scope: [
            { value: "50 000 მ²", label: "ობიექტის ფართობი" },
            { value: "6 თვე", label: "ხანგრძლივობა" },
        ],
        specs: [
            { value: "150+", label: "კამერა" },
            { value: "80+", label: "Access Point" },
            { value: "12", label: "Server Node" },
            { value: "99.9%", label: "Uptime" },
            { value: "100%", label: "დაფარვა" },
        ],
        challenges: [
            { icon: "warning", title: "მოძველებული ინფრასტრუქტურა", description: "არსებული ქსელი ვერ აკმაყოფილებდა თანამედროვე მონაცემთა გადაცემის მოთხოვნებს და იწვევდა სისტემურ შეფერხებებს." },
            { icon: "security", title: "უსაფრთხოების ხარვეზები", description: "ვიდეოსამეთვალყურეო სისტემა არ ფარავდა კრიტიკულ ზონებს, ხოლო კონტროლი ფრაგმენტული იყო." },
            { icon: "analytics", title: "ოპერაციული შეფერხებები", description: "ცენტრალიზებული მონიტორინგის არარსებობა ართულებდა პრობლემების დროულ აღმოჩენასა და რეაგირებას." },
        ],
        solutions: [
            { icon: "videocam", title: "CCTV Deployment", description: "IP კამერებით სრული დაფარვა და AI ვიდეოანალიტიკა." },
            { icon: "vpn_key", title: "Access Control", description: "ბიომეტრიული დაშვების კონტროლის ერთიანი სისტემა." },
            { icon: "settings_input_component", title: "Network Upgrade", description: "10Gbps ოპტიკური Backbone ინფრასტრუქტურა ყველა საკვანძო ზონაში.", featured: true },
            { icon: "storage", title: "NAS Infrastructure", description: "რეზერვირებული მონაცემთა საცავი და შენახვის პოლიტიკა." },
            { icon: "monitoring", title: "AI Analytics Platform", description: "ინციდენტების რეალურ დროში აღმოჩენა და პრევენცია." },
            { icon: "dynamic_feed", title: "System Redundancy", description: "კრიტიკული კომპონენტების მაღალი ხელმისაწვდომობა." },
        ],
        process: [
            { title: "ობიექტის შესწავლა", description: "ლოკაციის სრული აუდიტი და რისკების შეფასება." },
            { title: "პროექტირება", description: "ოპტიკური და უსადენო ქსელის ტოპოლოგიის დაგეგმვა." },
            { title: "შესყიდვები", description: "საჭირო ტექნიკის კომპლექტაცია და ლოგისტიკა." },
            { title: "მონტაჟი", description: "ფიზიკური ინფრასტრუქტურის გამართვა და კაბელირება." },
            { title: "კონფიგურაცია", description: "სისტემების პროგრამული გამართვა და ტესტირება." },
            { title: "ჩაბარება", description: "ექსპლუატაციაში ჩაბარება, დოკუმენტაცია და პერსონალის ტრენინგი." },
        ],
        gallery: [
            { src: "/brand-preview.svg", alt: "ქსელის ოპერაციების ცენტრი" },
            { src: "/brand-preview.svg", alt: "სერვერული კვანძი" },
            { src: "/brand-preview.svg", alt: "სტრუქტურირებული კაბელირება" },
        ],
        results: [
            { value: "99.99%", title: "სისტემური Uptime", description: "გარანტირებული უწყვეტობა კრიტიკულ პერიოდებში.", accent: "secondary" },
            { value: "60%", title: "სწრაფი რეაგირება", description: "ინციდენტების იდენტიფიცირების დროის შემცირება.", accent: "primary" },
            { value: "40%", title: "ნაკლები ინციდენტი", description: "უსაფრთხოების რისკების პრევენციული შემცირება.", accent: "secondary" },
        ],
        testimonial: {
            quote: "SafeTech-ის მიერ დანერგილი სისტემა არა მხოლოდ უსაფრთხოების გარანტიაა, არამედ ჩვენი ყოველდღიური ოპერაციული ეფექტურობის საფუძველიც. პროექტის მართვის ხარისხმა და ტექნიკურმა შესრულებამ მოლოდინს გადააჭარბა.",
            author: "ალექსანდრე დანელია",
            role: "CTO, Global Logistics Hub",
        },
        related: [
            { slug: "warehouse-hub", title: "Warehouse Hub A+", category: "ქსელური ინფრასტრუქტურა", image: "/brand-preview.svg", imageAlt: "თანამედროვე საწყობის ინფრასტრუქტურა" },
            { slug: "enterprise-center", title: "Enterprise Center", category: "დაშვების კონტროლის სისტემები", image: "/brand-preview.svg", imageAlt: "ბიზნეს ცენტრის უსაფრთხოების სისტემა" },
            { slug: "industrial-zone", title: "Industrial Zone Park", category: "ვიდეოსამეთვალყურეო სისტემა", image: "/brand-preview.svg", imageAlt: "ინდუსტრიული პარკის ვიდეოსამეთვალყურეო სისტემა" },
        ],
    },
];

export function getProjectDetail(slug: string) {
    return projectDetails.find((project) => project.slug === slug);
}
