import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default function IndustriesSection({ service }: { service: ServiceDetail }) {
    return (
        <section className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl" aria-labelledby="industries-title">
            <div id="industries-title"><SectionHeading centered>ინდუსტრიები</SectionHeading></div>
            <ul className="flex flex-wrap justify-center gap-unit-md">
                {service.industries.map((industry) => <li className="glass-card rounded-full px-unit-xl py-unit-md font-headline-md text-lg text-white" key={industry}>{industry}</li>)}
            </ul>
        </section>
    );
}
