import Left from "@/components/Contact/Form/Left";
import Right from "@/components/Contact/Form/Right";

export default function Form() {
    return (
        <section
            id="contact-form"
            className="
                relative
                py-10 sm:py-12 lg:py-16
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-4
                    sm:px-6
                    lg:px-margin-desktop
                "
            >
                <div
                    className="
                        grid
                        grid-cols-1
                        items-start
                        gap-5 sm:gap-6
                        lg:grid-cols-12
                        lg:gap-8
                    "
                >
                    <Left />
                    <Right />
                </div>
            </div>
        </section>
    );
}
