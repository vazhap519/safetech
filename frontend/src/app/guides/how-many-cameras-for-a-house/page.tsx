import type { Metadata } from "next";
import Link from "next/link";

import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata, localizeHref } from "@/lib/seo";

const guide = {
    ka: {
        title: "რამდენი უსაფრთხოების კამერა სჭირდება კერძო სახლს?",
        description: "კამერების რაოდენობა მხოლოდ სახლის ფართობით არ ითვლება. ის დამოკიდებულია შესასვლელებზე, ეზოს გეომეტრიაზე, განათებაზე და იმაზე, გსურთ ზოგადი ხედვა თუ ადამიანის ამოცნობა.",
        intro: "ეს გზამკვლევი დაგეხმარებათ წინასწარ განსაზღვროთ, რა ადგილები უნდა გააკონტროლოთ და რა ინფორმაცია გაუგზავნოთ მონტაჟის სპეციალისტს. კამერების საბოლოო რაოდენობა და კუთხეები ობიექტის შემოწმების შემდეგ განისაზღვრება.",
        exampleTitle: "მაგალითი: ეზოიანი სახლის კამერების საწყისი გეგმა",
        exampleIntro: "წარმოიდგინეთ სახლი ერთი ჭიშკრით, მთავარი კარით, ავტოსადგომითა და უკანა ეზოთი. ქვემოთ ჩამოთვლილია საკონტროლო ამოცანები და არა სავალდებულო კამერების რაოდენობა.",
        zones: [
            ["ჭიშკარი და შესასვლელი", "დააკვირდით, სად შემოდის ადამიანი ან მანქანა. ფართო კადრი და სახის მკაფიო დეტალი ერთი და იგივე ამოცანა არ არის."],
            ["მთავარი კარი", "შეარჩიეთ ხედვა, რომელშიც სტუმრის სახე არ იკარგება ძლიერი უკანა განათების ან ღამის განათების გამო."],
            ["ავტოსადგომი და სავალი ნაწილი", "დააზუსტეთ, გჭირდებათ ავტომობილის უბრალოდ დანახვა თუ სანომრე ნიშნის ამოცნობა; მეორე ამოცანას შეიძლება სხვა კუთხე და ოპტიკა დასჭირდეს."],
            ["უკანა ეზო და გვერდითი ბილიკები", "შეამოწმეთ, ხომ არ ფარავს კედელი, ხე, ღობე ან სახურავის კუთხე მნიშვნელოვან ზონას."],
        ],
        countTitle: "2, 4 თუ 6 კამერა?",
        countText: "საწყისი საორიენტაციო ვარიანტი შეიძლება იყოს 2–4 კამერა მცირე და მარტივი ობიექტისთვის ან 4–8 კამერა უფრო რთული ეზოიანი სახლისთვის. ეს დიაპაზონები არ არის უნივერსალური ნორმა: ზოგჯერ ერთი სწორად შერჩეული ხედვა რამდენიმე ამოცანას ფარავს, ზოგჯერ კი ერთი შესასვლელისთვის ორი განსხვავებული კადრია საჭირო. დაგეგმვა დაიწყეთ ზონებით და მხოლოდ შემდეგ დაითვალეთ კამერები.",
        checklistTitle: "მონტაჟამდე აუცილებლად შეამოწმეთ",
        checklist: [
            ["ხედვის მიზანი", "გჭირდებათ მოძრაობის დაფიქსირება, ადამიანის ამოცნობა თუ კონკრეტული მცირე დეტალის გარჩევა? ფართო ხედვა ყოველთვის არ ნიშნავს საკმარის დეტალიზაციას."],
            ["ღამის პირობები", "გაითვალისწინეთ რეალური განათება, კამერის მოდელი და გარე განათების მიმართულება. Full Color გამოსახულების ხარისხი გარემო პირობებზეა დამოკიდებული."],
            ["ქსელი და კვება", "IP კამერებისთვის დაგეგმეთ კაბელების მარშრუტი, PoE სიმძლავრე, NVR-ის მდებარეობა და UPS-ის საჭიროება."],
            ["ვიდეოარქივი", "HDD-ის მოცულობა გამოთვალეთ კამერების რაოდენობით, რეალური ბიტრეიტით, კოდეკითა და საჭირო შენახვის დღეებით — მხოლოდ „4MP“ საკმარისი მონაცემი არ არის."],
            ["ინტერნეტი და კონფიდენციალურობა", "ლოკალური NVR ჩვეულებრივ აგრძელებს ჩაწერას ინტერნეტის გათიშვისას, თუ კამერებსა და ჩამწერს კვება აქვთ. კამერები მიმართეთ თქვენი ობიექტის საჭირო ზონებზე და გაითვალისწინეთ სხვა ადამიანების კონფიდენციალურობა."],
        ],
        actionTitle: "დაგეგმეთ თქვენი სახლის კამერები",
        actionText: "ატვირთეთ სახლის გეგმა ან ფოტო, მონიშნეთ კარები, კამერები და კედლები. პლანერი აჩვენებს მიახლოებით 2D დაფარვას; მასშტაბი, სიმაღლე, რეალური ბრმა ზონები და აღჭურვილობა ადგილზე უნდა შემოწმდეს.",
        planner: "გახსენით კამერების პლანერი",
        service: "უსაფრთხოების კამერების მონტაჟი",
        local: "კამერების მონტაჟი თბილისში",
        projects: "რეალური პროექტები",
        faqTitle: "ხშირად დასმული კითხვები",
        faq: [
            ["ყველა კამერა აუცილებლად 4MP უნდა იყოს?", "არა. რეზოლუცია მხოლოდ ერთი პარამეტრია. მნიშვნელოვანია ხედვის კუთხე, მანძილი, ოპტიკა, განათება და საჭირო დეტალის ზომა კადრში."],
            ["კამერები ინტერნეტის გარეშე იმუშავებს?", "ლოკალური ჩანაწერი შესაძლებელია ინტერნეტის გარეშეც თავსებადი NVR/DVR სისტემით და კვებით. ტელეფონიდან დისტანციურ ნახვას კი კავშირი დასჭირდება."],
            ["შეიძლება პლანერის ნახაზით პირდაპირ მონტაჟი?", "პლანერი წინასწარი შეფასებისთვისაა. მანძილი, სამონტაჟო სიმაღლე, კედლები, განათება, ქსელი და კვება ადგილზე უნდა გადამოწმდეს."],
        ],
    },
    en: {
        title: "How many security cameras does a house need?",
        description: "Camera count is not determined by floor area alone. It depends on entrances, yard geometry, lighting and whether you need general coverage or a recognizable view of a visitor.",
        intro: "Use this guide to identify the areas you want to monitor and prepare a useful brief for an installer. Confirm the final camera count and viewing angles against the actual property.",
        exampleTitle: "Example: an initial plan for a house with a yard",
        exampleIntro: "Imagine one gate, a front door, a parking area and a rear yard. These are coverage tasks, not a mandatory number of cameras.",
        zones: [
            ["Gate and entrance", "Decide where people or vehicles enter. A wide overview and a detailed face image are different objectives."],
            ["Front door", "Position the view so a visitor's face is not lost in backlighting or poor night lighting."],
            ["Parking and driveway", "Clarify whether you need to see a vehicle or read its plate. Plate recognition may require a separate viewing angle and lens."],
            ["Rear yard and side passages", "Check whether a wall, tree, fence or roof corner blocks an important area."],
        ],
        countTitle: "Two, four or six cameras?",
        countText: "A rough starting range might be 2–4 cameras for a simple small property or 4–8 for a more complex house and yard. These are not universal rules: one suitable view can cover more than one task, while a single entry may require two different views. Identify coverage zones first; count cameras second.",
        checklistTitle: "Check these points before installation",
        checklist: [
            ["Purpose of each view", "Do you need movement awareness, recognizable faces or small details? Wide coverage does not automatically deliver identification-quality detail."],
            ["Night conditions", "Consider the actual lighting, camera model and direction of outdoor lights. Full Color performance depends on the environment."],
            ["Network and power", "Plan cable runs, PoE power budget, NVR location and whether backup power is required."],
            ["Recording retention", "Size the hard drive from camera count, actual bitrate, codec and target retention days. Resolution alone is not enough."],
            ["Internet and privacy", "A local recorder can normally keep recording during an internet outage if the cameras and recorder remain powered. Aim cameras at relevant areas of your property and respect others' privacy."],
        ],
        actionTitle: "Plan your home camera layout",
        actionText: "Upload a floor plan or photo, add doors, cameras and walls. The planner offers approximate 2D coverage; verify scale, heights, blind spots and equipment on site.",
        planner: "Open the camera planner",
        service: "Security camera installation",
        local: "CCTV installation in Tbilisi",
        projects: "Completed projects",
        faqTitle: "Frequently asked questions",
        faq: [
            ["Should every camera be 4MP?", "No. Resolution is only one parameter. Angle, distance, lens, lighting and the size of the desired detail in the image also matter."],
            ["Will cameras work without internet?", "Compatible NVR/DVR systems can record locally without internet if powered. Remote phone viewing requires connectivity."],
            ["Can I install directly from the planner image?", "Use the planner for an initial concept. Distances, mounting heights, walls, lighting, network and power need an on-site check."],
        ],
    },
    ru: {
        title: "Сколько камер видеонаблюдения нужно для частного дома?",
        description: "Число камер зависит не только от площади: важны входы, форма участка, освещение и требуемая детализация — общий обзор или распознаваемое лицо посетителя.",
        intro: "Этот материал поможет определить зоны контроля и подготовить исходные данные для специалиста. Окончательное количество камер и углы обзора нужно согласовать после осмотра объекта.",
        exampleTitle: "Пример: предварительная схема дома с участком",
        exampleIntro: "Представим один въезд, входную дверь, парковку и задний двор. Это задачи наблюдения, а не обязательное число камер.",
        zones: [
            ["Ворота и въезд", "Определите, откуда входят люди и въезжают машины. Широкий обзор и чёткое изображение лица — разные задачи."],
            ["Входная дверь", "Подберите ракурс, при котором лицо не теряется из-за контрового света или слабого ночного освещения."],
            ["Парковка и проезд", "Уточните, нужно ли просто видеть машину или читать номер: для второго может понадобиться отдельный ракурс и объектив."],
            ["Задний двор и боковые проходы", "Проверьте, не закрывают ли нужную зону стена, дерево, забор или угол крыши."],
        ],
        countTitle: "Две, четыре или шесть камер?",
        countText: "Предварительный ориентир — 2–4 камеры для небольшого простого объекта либо 4–8 для дома и участка со сложной геометрией. Это не универсальная норма: один удачный ракурс иногда решает несколько задач, а для одного входа нужны две разные камеры. Сначала определите зоны, затем считайте устройства.",
        checklistTitle: "Что проверить до монтажа",
        checklist: [
            ["Задача камеры", "Нужно замечать движение, узнавать лицо или различать мелкие детали? Широкий угол сам по себе не гарантирует достаточную детализацию."],
            ["Ночная съёмка", "Учитывайте фактическое освещение, модель камеры и направление светильников. Работа Full Color зависит от условий."],
            ["Сеть и питание", "Спланируйте кабельные трассы, бюджет PoE, расположение NVR и потребность в резервном питании."],
            ["Срок хранения", "Объём HDD рассчитывают по числу камер, фактическому битрейту, кодеку и числу дней архива. Одного разрешения недостаточно."],
            ["Интернет и приватность", "Локальная запись обычно продолжается без интернета, если камеры и регистратор питаются. Направляйте камеры на нужные зоны своего объекта и учитывайте приватность окружающих."],
        ],
        actionTitle: "Спланируйте камеры для своего дома",
        actionText: "Загрузите план или фото, отметьте двери, камеры и стены. Планировщик показывает приблизительное покрытие в 2D; масштаб, высоты, слепые зоны и оборудование проверяются на объекте.",
        planner: "Открыть планировщик камер",
        service: "Монтаж камер видеонаблюдения",
        local: "Монтаж камер в Тбилиси",
        projects: "Выполненные проекты",
        faqTitle: "Частые вопросы",
        faq: [
            ["Все камеры должны быть 4MP?", "Нет. Разрешение — лишь один параметр. Важны угол, дистанция, оптика, свет и размер нужной детали в кадре."],
            ["Камеры работают без интернета?", "Совместимый NVR/DVR может вести локальную запись без интернета при наличии питания. Для удалённого просмотра нужна связь."],
            ["Можно монтировать только по схеме планировщика?", "Планировщик нужен для первого наброска. Дистанции, высоту, стены, освещение, сеть и питание проверяют на объекте."],
        ],
    },
} as const;

export async function generateMetadata(): Promise<Metadata> {
    const locale = await getCurrentLocale();
    const copy = guide[locale];
    return createMetadata({
        title: copy.title,
        description: copy.description,
        path: "/guides/how-many-cameras-for-a-house",
        locale,
    });
}

export default async function HomeCameraCountGuide() {
    const locale = await getCurrentLocale();
    const copy = guide[locale];
    const link = (path: string) => localizeHref(path, locale);

    return (
        <article className="mx-auto max-w-container-max px-5 pb-20 pt-32 text-on-surface md:px-8 xl:px-14">
            <div className="max-w-4xl">
                <h1 className="text-4xl font-semibold leading-tight md:text-5xl">{copy.title}</h1>
                <p className="mt-6 text-lg leading-8 text-on-surface-variant">{copy.description}</p>
                <p className="mt-4 leading-8 text-on-surface-variant">{copy.intro}</p>
            </div>

            <section className="mt-14 max-w-5xl" aria-labelledby="zones-heading">
                <h2 id="zones-heading" className="text-3xl font-semibold">{copy.exampleTitle}</h2>
                <p className="mt-4 leading-8 text-on-surface-variant">{copy.exampleIntro}</p>
                <ol className="mt-7 grid gap-4 md:grid-cols-2">
                    {copy.zones.map(([title, description]) => (
                        <li className="rounded-2xl border border-white/10 bg-surface-container p-6" key={title}>
                            <h3 className="text-xl font-semibold">{title}</h3>
                            <p className="mt-3 leading-7 text-on-surface-variant">{description}</p>
                        </li>
                    ))}
                </ol>
            </section>

            <section className="mt-14 max-w-4xl" aria-labelledby="count-heading">
                <h2 id="count-heading" className="text-3xl font-semibold">{copy.countTitle}</h2>
                <p className="mt-5 leading-8 text-on-surface-variant">{copy.countText}</p>
            </section>

            <section className="mt-14 max-w-5xl" aria-labelledby="checklist-heading">
                <h2 id="checklist-heading" className="text-3xl font-semibold">{copy.checklistTitle}</h2>
                <ol className="mt-7 space-y-4">
                    {copy.checklist.map(([title, description], index) => (
                        <li className="rounded-2xl border border-white/10 p-6" key={title}>
                            <h3 className="text-lg font-semibold">{index + 1}. {title}</h3>
                            <p className="mt-2 leading-7 text-on-surface-variant">{description}</p>
                        </li>
                    ))}
                </ol>
            </section>

            <section className="mt-14 rounded-3xl border border-primary/20 bg-primary/10 p-7 sm:p-10" aria-labelledby="planner-heading">
                <h2 id="planner-heading" className="text-3xl font-semibold">{copy.actionTitle}</h2>
                <p className="mt-4 max-w-4xl leading-8 text-on-surface-variant">{copy.actionText}</p>
                <div className="mt-7 flex flex-wrap gap-3">
                    <Link className="rounded-xl bg-primary-container px-6 py-4 font-semibold text-on-primary-container" href={link("/camera-planner")}>
                        {copy.planner}
                    </Link>
                    <Link className="rounded-xl border border-outline-variant px-6 py-4 font-semibold text-on-surface" href={link("/services/security-camera-installation")}>
                        {copy.service}
                    </Link>
                    <Link className="rounded-xl border border-outline-variant px-6 py-4 font-semibold text-on-surface" href={link("/services/security-camera-installation/tbilisi")}>
                        {copy.local}
                    </Link>
                    <Link className="rounded-xl border border-outline-variant px-6 py-4 font-semibold text-on-surface" href={link("/projects")}>
                        {copy.projects}
                    </Link>
                </div>
            </section>

            <section className="mt-14 max-w-4xl" aria-labelledby="faq-heading">
                <h2 id="faq-heading" className="text-3xl font-semibold">{copy.faqTitle}</h2>
                <div className="mt-7 space-y-4">
                    {copy.faq.map(([question, answer]) => (
                        <details key={question} className="rounded-2xl border border-white/10 p-6">
                            <summary className="cursor-pointer font-semibold">{question}</summary>
                            <p className="mt-4 leading-7 text-on-surface-variant">{answer}</p>
                        </details>
                    ))}
                </div>
            </section>
        </article>
    );
}
