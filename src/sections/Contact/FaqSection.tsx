

import Typography from "@/components/ui/Typography";
import Faq from "@/components/Contact/Faq/Faq";

export default function FaqSection() {
    return (
        <section
            className="
                py-unit-xl
                bg-surface-container-low
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-margin-desktop
                "
            >
                {/* Title */}
                <Typography
                    as="h2"
                    variant="contact-faq"
                    className="
                        max-w-4xl
                        mx-auto
                    "
                >
                    ხშირად დასმული კითხვები
                </Typography>

                {/* FAQ Container */}
                <div
                    className="
                        max-w-3xl
                        mx-auto

                        space-y-4
                        md:space-y-unit-md
                    "
                >
                    <Faq
                        question="რამდენ ხანში ხდება პროექტის შეფასება?"
                        answer="პროექტის პირველადი შეფასება ხდება 24-48 საათის განმავლობაში."
                        defaultOpen
                    />

                    <Faq
                        question="აკეთებთ მონტაჟსაც?"
                        answer="დიახ, ვახორციელებთ სრულ მონტაჟსა და კონფიგურაციას."
                    />

                    <Faq
                        question="გაქვთ გარანტია?"
                        answer="ყველა პროექტზე ვრცელდება ოფიციალური გარანტია."
                    />
                </div>
            </div>
        </section>
    );
}