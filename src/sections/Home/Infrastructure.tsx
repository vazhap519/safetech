import Infrastructurecomponent from "@/components/Home/Infrastructurecomponent";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Infrastructure() {
    const { branding } = await getSiteSettings();

    return (
        <section
            className="
                relative
                overflow-hidden
                py-20
                md:py-28
                bg-surface-container-lowest/30
            "
        >

            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-5
                    md:px-10
                    xl:px-margin-desktop
                    grid
                    grid-cols-1
                    lg:grid-cols-2
                    gap-16
                    xl:gap-unit-xl
                    items-center
                "
            >

                {/* LEFT IMAGE */}
                <div
                    className="
                        relative
                        order-2
                        lg:order-1
                        group
                        w-full
                        max-w-[760px]
                        mx-auto
                    "
                >

                    {/* Glow */}
                    <div
                        className="
                            absolute
                            -inset-4
                            bg-primary/10
                            blur-3xl
                            rounded-full
                            group-hover:bg-primary/20
                            transition-all
                            duration-700
                        "
                    ></div>

                    {/* Image */}
                    <Image
                        variant="home-infrastructure"
                        src={branding.defaultImage}
                        alt="SafeTech-ის ქსელური ინფრასტრუქტურის ტოპოლოგია"
                        sizes="(max-width: 768px) 100vw, 50vw"
                        width={600}
                        height={410}
                    />
                </div>

                {/* RIGHT CONTENT */}
                <div
                    className="
                        order-1
                        lg:order-2
                        space-y-8
                        text-center
                        lg:text-left
                    "
                >

                    {/* Badge */}
                    <div
                        className="
                            inline-flex
                            items-center
                            gap-2
                            px-4
                            py-2
                            rounded-full
                            glass-card
                            mx-auto
                            lg:mx-0
                        "
                    >
                        <span className="status-dot"></span>

                        <span
                            className="
                                text-secondary
                                font-mono-sm
                                text-mono-sm
                                uppercase
                                tracking-widest
                            "
                        >
                            Enterprise Architecture
                        </span>
                    </div>

                    <Typography
                        as="h2"
                        variant="section-title"
                    >
                        ინფრასტრუქტურული მიდგომა
                    </Typography>

                    {/* Description */}
                    <Typography
                        as="p"
                        variant="description"
                        className="
        max-w-[620px]
        mx-auto
        lg:mx-0
    "
                    >
                        ჩვენ არ ვაყენებთ უბრალოდ მოწყობილობებს.
                        ჩვენ ვქმნით ერთიან, მართვად და
                        მასშტაბირებად ეკოსისტემას თქვენი
                        ბიზნესის ზრდისთვის.
                    </Typography>

                    {/* Features */}
                    <div
                        className="
                            space-y-5
                            pt-2
                        "
                    >
                        <Infrastructurecomponent />
                        <Infrastructurecomponent />
                        <Infrastructurecomponent />
                    </div>
                </div>
            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    w-[500px]
                    h-[500px]
                    bg-primary/5
                    rounded-full
                    blur-[150px]
                    -z-10
                "
            ></div>
        </section>
    );
}
