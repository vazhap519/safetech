// export default function ServiceCardComponent() {
//     return (
//         <div className="glass-card rounded-2xl p-unit-lg flex flex-col h-full">
//             <div className="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-unit-md">
//                 <span className="material-symbols-outlined text-primary text-[28px]">videocam</span>
//             </div>
//             <h3 className="font-headline-md text-headline-md mb-unit-sm">IP კამერები &amp; CCTV</h3>
//             <p className="font-body-md text-body-md text-on-surface-variant mb-unit-lg flex-grow">მაღალი
//                 რეზოლუციის ვიდეომეთვალყურეობის სისტემების დაპროექტება და ინსტალაცია Hikvision და Dahua-ს
//                 ბაზაზე.</p>
//             <a className="inline-flex items-center text-primary font-label-md text-label-md gap-2 group"
//                href="#">
//                 დეტალურად
//                 <span
//                     className="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
//             </a>
//         </div>
//
//     )
// }


import Link from "next/link";

type Service = {
    slug: string;
    title: string;
    description: string;
    icon: string;
};

type Props = {
    service: Service;
};

export default function ServiceCardComponent({
                                                 service,
                                             }: Props) {
    return (
        <div className="glass-card rounded-2xl p-unit-lg flex flex-col h-full">
            <div className="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-unit-md">
                <span className="material-symbols-outlined text-primary text-[28px]">
                    {service.icon}
                </span>
            </div>

            <h3 className="font-headline-md text-headline-md mb-unit-sm">
                {service.title}
            </h3>

            <p className="font-body-md text-body-md text-on-surface-variant mb-unit-lg flex-grow">
                {service.description}
            </p>

            <Link
                href={`/services/${service.slug}`}
                className="inline-flex items-center text-primary font-label-md text-label-md gap-2 group"
            >
                დეტალურად

                <span className="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">
                    arrow_forward
                </span>
            </Link>
        </div>
    );
}