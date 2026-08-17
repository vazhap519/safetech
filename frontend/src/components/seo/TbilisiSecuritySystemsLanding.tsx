import Link from "next/link";

import { localizePath, type Locale } from "@/lib/locales";

type Copy = {
    eyebrow: string;
    title: string;
    intro: string;
    primaryCta: string;
    secondaryCta: string;
    servicesTitle: string;
    servicesIntro: string;
    services: { title: string; text: string; href: string }[];
    whyTitle: string;
    why: string[];
    coverageTitle: string;
    coverage: string;
    processTitle: string;
    process: { title: string; text: string }[];
    finalTitle: string;
    finalText: string;
    call: string;
};

const COPY: Record<Locale, Copy> = {
    ka: {
        eyebrow: "SafeTech • თბილისი",
        title: "უსაფრთხოების კამერებისა და უსაფრთხოების სისტემების მონტაჟი თბილისში",
        intro: "ვგეგმავთ, ვაწვდით და ვამონტაჟებთ ვიდეოსამეთვალყურეობას, დაშვების კონტროლს, სიგნალიზაციას, ქსელურ ინფრასტრუქტურას და სხვა სუსტი დენების სისტემებს სახლებისთვის, ოფისებისთვის, მაღაზიებისთვის, საწყობებისა და კომერციული ობიექტებისთვის თბილისში.",
        primaryCta: "მოითხოვე შეთავაზება",
        secondaryCta: "ნახე შესრულებული პროექტები",
        servicesTitle: "რას ვაკეთებთ თბილისში",
        servicesIntro: "ობიექტის ტიპისა და ბიუჯეტის მიხედვით ვარჩევთ შესაბამის მოწყობილობას, კაბელირებას, ჩამწერს, ქსელურ ინფრასტრუქტურას და მონტაჟის სქემას.",
        services: [
            { title: "უსაფრთხოების კამერები", text: "IP/PoE და ანალოგური კამერები, NVR/DVR, დისტანციური ნახვა, ჩაწერის არქივი და სწორად შერჩეული ხედვის კუთხეები.", href: "/services" },
            { title: "დაშვების კონტროლი და დომოფონი", text: "ვიდეოდომოფონი, ელექტრო და მაგნიტური საკეტები, ბარათი, კოდი, ღილაკი და კარის კონტროლი.", href: "/services" },
            { title: "ქსელები და Wi‑Fi", text: "CAT6 კაბელირება, ქსელური როზეტები, PoE, სვიჩები, როუტერები, Wi‑Fi დაფარვა, rack და სტრუქტურირებული ქსელი.", href: "/services" },
            { title: "სიგნალიზაცია და IT ინფრასტრუქტურა", text: "სიგნალიზაცია, სერვერები, სამუშაო სადგურები, UPS, სარეზერვო კავშირი და ტექნიკური მხარდაჭერა.", href: "/services" },
        ],
        whyTitle: "რატომ SafeTech",
        why: [
            "ობიექტის წინასწარი შეფასება და ტექნიკურად გამართული დაგეგმვა",
            "კაბელის, PoE დატვირთვის, არქივის მოცულობისა და ქსელის სწორი გათვლა",
            "მონტაჟის შემდეგ კონფიგურაცია, ტესტირება და მომხმარებლის სწავლება",
            "შემდგომი ტექნიკური მხარდაჭერა და სისტემის გაფართოების შესაძლებლობა",
        ],
        coverageTitle: "მომსახურების ზონა",
        coverage: "ვემსახურებით თბილისის ყველა ძირითად რაიონს და საჭიროების შემთხვევაში ვმუშაობთ საქართველოს სხვა ქალაქებშიც. კომერციული ობიექტებისთვის შესაძლებელია ეტაპობრივი სამუშაო: პროექტირება, კაბელირება, მოწყობილობების მონტაჟი და საბოლოო კონფიგურაცია.",
        processTitle: "როგორ ვმუშაობთ",
        process: [
            { title: "1. მოთხოვნის შეფასება", text: "ვარკვევთ ობიექტის ტიპს, კამერების/წერტილების რაოდენობას, ჩაწერის პერიოდს და დამატებით მოთხოვნებს." },
            { title: "2. ტექნიკური გადაწყვეტა", text: "ვადგენთ მოწყობილობების, კაბელის, ქსელის, კვებისა და მონტაჟის ოპტიმალურ სქემას." },
            { title: "3. მონტაჟი და ტესტირება", text: "ვამონტაჟებთ, ვაკონფიგურირებთ, ვამოწმებთ ყველა წერტილს და ვაბარებთ გამართულ სისტემას." },
        ],
        finalTitle: "გჭირდებათ უსაფრთხოების სისტემის მონტაჟი თბილისში?",
        finalText: "მოგვწერეთ ობიექტის ტიპი, მისამართის რაიონი და დაახლოებით რამდენი კამერა ან ქსელური წერტილი გჭირდებათ — დაგეხმარებით სწორი კონფიგურაციის შერჩევაში.",
        call: "დარეკვა: 571 43 01 69",
    },
    en: {
        eyebrow: "SafeTech • Tbilisi",
        title: "CCTV and Security System Installation in Tbilisi",
        intro: "We design, supply, and install CCTV, access control, alarm systems, structured cabling, Wi‑Fi, and low-voltage infrastructure for homes, offices, shops, warehouses, and commercial properties in Tbilisi.",
        primaryCta: "Request a quote",
        secondaryCta: "View completed projects",
        servicesTitle: "Security and IT services in Tbilisi",
        servicesIntro: "We select the right equipment, cabling, recorder, network design, and installation approach for the property type, retention requirements, and budget.",
        services: [
            { title: "CCTV systems", text: "IP/PoE and analog cameras, NVR/DVR, remote viewing, recording retention planning, and correct camera placement.", href: "/services" },
            { title: "Access control and intercom", text: "Video intercoms, electric and magnetic locks, cards, keypads, exit buttons, and door control.", href: "/services" },
            { title: "Networks and Wi‑Fi", text: "CAT6 cabling, data outlets, PoE, switches, routers, Wi‑Fi coverage, racks, and structured networks.", href: "/services" },
            { title: "Alarm and IT infrastructure", text: "Alarm systems, servers, workstations, UPS, backup connectivity, and technical support.", href: "/services" },
        ],
        whyTitle: "Why SafeTech",
        why: [
            "Site assessment and technically correct planning",
            "Correct sizing of cabling, PoE load, storage retention, and network capacity",
            "Configuration, testing, and user handover after installation",
            "Ongoing support and room for future system expansion",
        ],
        coverageTitle: "Service area",
        coverage: "We serve all major districts of Tbilisi and can also work in other cities across Georgia. Commercial projects can be delivered in phases: design, cabling, equipment installation, and final configuration.",
        processTitle: "How we work",
        process: [
            { title: "1. Requirement review", text: "We define the property type, number of cameras or data points, recording period, and any special requirements." },
            { title: "2. Technical solution", text: "We prepare the optimal equipment, cabling, networking, power, and installation design." },
            { title: "3. Installation and testing", text: "We install, configure, test every point, and hand over a working system." },
        ],
        finalTitle: "Need a security system installed in Tbilisi?",
        finalText: "Send us the property type, district, and approximate number of cameras or network points you need, and we will help you choose the right configuration.",
        call: "Call: 571 43 01 69",
    },
    ru: {
        eyebrow: "SafeTech • Тбилиси",
        title: "Монтаж видеонаблюдения и систем безопасности в Тбилиси",
        intro: "Проектируем, поставляем и устанавливаем видеонаблюдение, контроль доступа, сигнализацию, структурированные сети, Wi‑Fi и слаботочные системы для домов, офисов, магазинов, складов и коммерческих объектов в Тбилиси.",
        primaryCta: "Получить расчет",
        secondaryCta: "Посмотреть проекты",
        servicesTitle: "Что мы устанавливаем в Тбилиси",
        servicesIntro: "Подбираем оборудование, кабель, регистратор, сеть и схему монтажа с учетом типа объекта, срока хранения архива и бюджета.",
        services: [
            { title: "Видеонаблюдение", text: "IP/PoE и аналоговые камеры, NVR/DVR, удаленный просмотр, расчет архива и правильное размещение камер.", href: "/services" },
            { title: "Контроль доступа и домофон", text: "Видеодомофоны, электромеханические и магнитные замки, карты, код, кнопки выхода и управление дверью.", href: "/services" },
            { title: "Сети и Wi‑Fi", text: "CAT6, сетевые розетки, PoE, коммутаторы, роутеры, Wi‑Fi покрытие, стойки и структурированные сети.", href: "/services" },
            { title: "Сигнализация и IT", text: "Сигнализация, серверы, рабочие станции, UPS, резервный интернет и техническая поддержка.", href: "/services" },
        ],
        whyTitle: "Почему SafeTech",
        why: [
            "Предварительная оценка объекта и технически корректное планирование",
            "Правильный расчет кабеля, PoE, объема архива и пропускной способности сети",
            "Настройка, тестирование и обучение пользователя после монтажа",
            "Техническая поддержка и возможность дальнейшего расширения системы",
        ],
        coverageTitle: "Зона обслуживания",
        coverage: "Работаем во всех основных районах Тбилиси и при необходимости выезжаем в другие города Грузии. Коммерческие объекты можем выполнять поэтапно: проектирование, кабельные работы, монтаж оборудования и финальная настройка.",
        processTitle: "Как проходит работа",
        process: [
            { title: "1. Уточнение задачи", text: "Определяем тип объекта, количество камер или сетевых точек, срок хранения записи и дополнительные требования." },
            { title: "2. Техническое решение", text: "Подбираем оптимальную схему оборудования, кабеля, сети, питания и монтажа." },
            { title: "3. Монтаж и проверка", text: "Устанавливаем, настраиваем, тестируем каждую точку и сдаем полностью рабочую систему." },
        ],
        finalTitle: "Нужен монтаж системы безопасности в Тбилиси?",
        finalText: "Напишите тип объекта, район и примерное количество камер или сетевых точек — поможем подобрать подходящую конфигурацию.",
        call: "Позвонить: 571 43 01 69",
    },
};

export default function TbilisiSecuritySystemsLanding({ locale }: { locale: Locale }) {
    const copy = COPY[locale];
    const localized = (href: string) => localizePath(href, locale);

    return (
        <main className="bg-surface text-on-surface">
            <section className="border-b border-outline/20 bg-surface-container-low">
                <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-secondary">{copy.eyebrow}</p>
                    <h1 className="mt-4 max-w-5xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">{copy.title}</h1>
                    <p className="mt-6 max-w-3xl text-lg leading-8 text-on-surface-variant">{copy.intro}</p>
                    <div className="mt-8 flex flex-wrap gap-3">
                        <Link className="rounded-full bg-primary px-6 py-3 font-semibold text-on-primary transition-opacity hover:opacity-90" href={localized("/contact")}>{copy.primaryCta}</Link>
                        <Link className="rounded-full border border-outline/40 px-6 py-3 font-semibold transition-colors hover:bg-surface-container" href={localized("/projects")}>{copy.secondaryCta}</Link>
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 className="text-3xl font-bold tracking-tight">{copy.servicesTitle}</h2>
                <p className="mt-4 max-w-3xl text-on-surface-variant">{copy.servicesIntro}</p>
                <div className="mt-8 grid gap-4 md:grid-cols-2">
                    {copy.services.map((service) => (
                        <Link key={service.title} href={localized(service.href)} className="rounded-3xl border border-outline/20 bg-surface-container-low p-6 transition-transform hover:-translate-y-0.5">
                            <h3 className="text-xl font-semibold">{service.title}</h3>
                            <p className="mt-3 leading-7 text-on-surface-variant">{service.text}</p>
                        </Link>
                    ))}
                </div>
            </section>

            <section className="bg-surface-container-low">
                <div className="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div>
                        <h2 className="text-3xl font-bold tracking-tight">{copy.whyTitle}</h2>
                        <ul className="mt-6 space-y-3 text-on-surface-variant">
                            {copy.why.map((item) => <li key={item} className="rounded-2xl bg-surface px-5 py-4">{item}</li>)}
                        </ul>
                    </div>
                    <div>
                        <h2 className="text-3xl font-bold tracking-tight">{copy.coverageTitle}</h2>
                        <p className="mt-6 text-lg leading-8 text-on-surface-variant">{copy.coverage}</p>
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 className="text-3xl font-bold tracking-tight">{copy.processTitle}</h2>
                <div className="mt-8 grid gap-4 lg:grid-cols-3">
                    {copy.process.map((step) => (
                        <div key={step.title} className="rounded-3xl border border-outline/20 p-6">
                            <h3 className="text-lg font-semibold">{step.title}</h3>
                            <p className="mt-3 leading-7 text-on-surface-variant">{step.text}</p>
                        </div>
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
                <div className="rounded-[2rem] bg-primary p-8 text-on-primary sm:p-10">
                    <h2 className="text-3xl font-bold tracking-tight">{copy.finalTitle}</h2>
                    <p className="mt-4 max-w-3xl text-on-primary/85">{copy.finalText}</p>
                    <div className="mt-7 flex flex-wrap gap-3">
                        <a className="rounded-full bg-surface px-6 py-3 font-semibold text-on-surface" href="tel:+995571430169">{copy.call}</a>
                        <Link className="rounded-full border border-on-primary/40 px-6 py-3 font-semibold" href={localized("/contact")}>{copy.primaryCta}</Link>
                    </div>
                </div>
            </section>
        </main>
    );
}
