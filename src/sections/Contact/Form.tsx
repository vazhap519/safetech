import Left from "@/components/Contact/Form/Left";
import Right from "@/components/Contact/Form/Right";
import { getBackendContactServices } from "@/lib/backend";

export default async function Form() {
    const services = await getBackendContactServices();

    return (
        <section
            id="contact-form"
            className="
                relative
                py-unit-xl
            "
        >
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-margin-desktop
                "
            >
                <div
                    className="
                        grid
                        grid-cols-1
                        items-start
                        gap-unit-lg
                        lg:grid-cols-12
                        lg:gap-unit-xl
                    "
                >
                    <Left services={services} />
                    <Right />
                </div>
            </div>
        </section>
    );
}
