import HowComponent from "@/components/About/HowComponent";

export default function HowSection(){
    return (
        <section className="py-unit-xl relative overflow-hidden">
            <div className="max-w-container-max mx-auto px-margin-desktop">
                <h2 className="font-headline-xl text-headline-xl text-on-surface text-center mb-unit-xl">სამუშაო
                    პროცესი</h2>
                <div className="relative">
                    <div className="absolute left-1/2 top-0 bottom-0 w-px bg-outline-variant/30 hidden md:block"></div>
                    <div className="space-y-unit-lg relative">
                        {/*Step 1 */}
                <HowComponent/>

                    </div>
                </div>
            </div>
        </section>

    )
}