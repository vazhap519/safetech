// import {ElementType, ReactNode} from "react";
// import clsx from "clsx";
//
// type TypographyProps = {
//     children: ReactNode;
//
//     variant?:
//         | "hero"
//         | "section-title"
//         | "cta-title"
//         | "contact-title"
//         | "contact-intro-title"
//         | "contact-support-title"
//         | "contact-faq"
//         | "contact-final-cta"
//         | "footer-title"
//         | "description"
//         | "section-description";
//     as?: ElementType;
//
//     className?: string;
// };
//
// export default function HeroTypography({
//                                        children,
//                                        variant = "section-title",
//                                        as: Component = "p",
//                                        className,
//                                    }: TypographyProps) {
//     return (
//         <Component
//             className={clsx(
//                 // BASE
//                 `
//                 tracking-tight
//                 text-on-surface
//                 `,
//                 variant === "footer-title" &&
//                 `
//     text-primary
//     font-bold
//     font-label-md
//     text-label-md
//     uppercase
//     tracking-wider
// `,
//
//
//
//                 /*
// |--------------------------------------------------------------------------
// | DESCRIPTION
// |--------------------------------------------------------------------------
// */
//
//                 variant === "description" &&
//                 `
//     font-body-lg
//     text-base
//     md:text-lg
//     leading-relaxed
//     text-on-surface-variant
// `,
//
//                 /*
//                 |--------------------------------------------------------------------------
//                 | SECTION DESCRIPTION
//                 |--------------------------------------------------------------------------
//                 */
//
//                 variant === "section-description" &&
//                 `
//     mt-5
//     text-base
//     md:text-lg
//     leading-relaxed
//     text-on-surface-variant
// `,
//                 /*
//                 |--------------------------------------------------------------------------
//                 | HERO TITLE
//                 |--------------------------------------------------------------------------
//                 */
//
//                 variant === "hero" &&
//                 `
//                     font-display-lg
//                     text-[42px]
//                     leading-[1.05]
//                     sm:text-[52px]
//                     md:text-[64px]
//                     xl:text-display-lg
//                     `,
//
//                 /*
//                 |--------------------------------------------------------------------------
//                 | SECTION TITLE
//                 |--------------------------------------------------------------------------
//                 */
//
//                 variant === "section-title" &&
//                 `
//                     font-headline-xl
//                     text-[36px]
//                     leading-tight
//                     sm:text-[42px]
//                     md:text-[52px]
//                     xl:text-headline-xl
//                     `,
//
//                 /*
//                 |--------------------------------------------------------------------------
//                 | CTA TITLE
//                 |--------------------------------------------------------------------------
//                 */
//
//                 variant === "cta-title" &&
//                 `
//                     font-display-lg
//                     text-[38px]
//                     leading-[1.05]
//                     sm:text-[48px]
//                     md:text-[64px]
//                     `,
//                 variant === "contact-title" &&
//                 `
//     font-display-lg
//     text-[42px]
//     leading-[1.05]
//     sm:text-[52px]
//     md:text-[64px]
//     mb-unit-md
//     text-primary
//     drop-shadow-2xl
// `,
//                 variant === "contact-intro-title" &&
//                 `
//    font-headline-xl text-headline-xl mb-unit-lg text-primary
// `,
//     variant === "contact-support-title" &&
//                 `
//   font-headline-xl text-headline-xl mb-unit-lg text-primary
// `,  variant === "contact-faq" &&
//                 `
// font-headline-xl text-headline-xl mb-unit-xl text-center text-primary
// `,
//                 variant === "contact-final-cta" &&
//                 `
//     font-display-lg
//     text-[42px]
//     leading-[1.05]
//     sm:text-[52px]
//     md:text-[64px]
//     mb-unit-lg
//     text-on-surface
//     drop-shadow-lg
// `,
//
//                 className
//             )}
//         >
//             {children}
//         </Component>
//     );
// }

import { ElementType, ReactNode } from "react";
import clsx from "clsx";

type TypographyProps = {
    children: ReactNode;

    variant?:
        | "hero"
        | "section-title"
        | "cta-title"
        | "contact-title"
        | "contact-intro-title"
        | "contact-support-title"
        | "contact-faq"
        | "contact-final-cta"
        | "contact-final"
        | "footer-title"
        | "description"
        | "section-description"
        | "why-component-header"
        | "[slug]-hero"
        | "step-title"
        | "step-description"

    as?: ElementType;

    className?: string;
};

export default function Typography({
                                       children,
                                       variant = "section-title",
                                       as: Component = "p",
                                       className,
                                   }: TypographyProps) {
    return (
        <Component
            className={clsx(
                /*
                |--------------------------------------------------------------------------
                | BASE
                |--------------------------------------------------------------------------
                */
                `
                tracking-tight
                text-on-surface
                `,
                variant === "step-title" &&
                `
    font-headline-md
    text-[18px]
    mb-2
`,
                variant === "step-description" &&
                `
    font-body-md
    text-body-md
    text-on-surface-variant
`,
                /*
                |--------------------------------------------------------------------------
                | FOOTER TITLE
                |--------------------------------------------------------------------------
                */
                variant === "footer-title" &&
                `
                    text-primary
                    font-bold
                    font-label-md
                    text-label-md
                    uppercase
                    tracking-wider
                `,
       variant === "why-component-header" &&
                `
             font-headline-md text-headline-md mb-2
                `,
                variant === "[slug]-hero" &&
                `
 font-display

text-[34px]
sm:text-[40px]
md:text-[52px]
lg:text-[60px]

leading-[1.1]

mb-6

text-on-surface
`,
                /*
                |--------------------------------------------------------------------------
                | DESCRIPTION
                |--------------------------------------------------------------------------
                */
                variant === "description" &&
                `font-body

text-sm
sm:text-base
md:text-lg

leading-relaxed

max-w-xl

text-on-surface-variant

                `,

                /*
                |--------------------------------------------------------------------------
                | SECTION DESCRIPTION
                |--------------------------------------------------------------------------
                */
                variant === "section-description" &&
                `
                    mt-5

                    text-sm
                    sm:text-base
                    md:text-lg

                    leading-relaxed

                    text-on-surface-variant
                `,

                /*
                |--------------------------------------------------------------------------
                | HERO TITLE
                |--------------------------------------------------------------------------
                */
                variant === "hero" &&
                `
                    font-display-lg

                    text-[32px]
                    sm:text-[42px]
                    md:text-[56px]
                    lg:text-[60px]

                    leading-[1.08]
                `,

                /*
                |--------------------------------------------------------------------------
                | SECTION TITLE
                |--------------------------------------------------------------------------
                */
                variant === "section-title" &&
                `
                    font-headline-xl

                    text-[30px]
                    sm:text-[38px]
                    md:text-[52px]
                    xl:text-headline-xl

                    leading-tight
                `,

                /*
                |--------------------------------------------------------------------------
                | CTA TITLE
                |--------------------------------------------------------------------------
                */
                variant === "cta-title" &&
                `
                    font-display-lg

                    text-[32px]
                    sm:text-[42px]
                    md:text-[56px]
                    lg:text-[64px]

                    leading-[1.05]
                `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT HERO TITLE
                |--------------------------------------------------------------------------
                */
                variant === "contact-title" &&
                `
                    font-display-lg

                    text-[32px]
                    sm:text-[42px]
                    md:text-[56px]
                    lg:text-[64px]

                    leading-[1.08]

                    mb-unit-md

                    text-primary
                    drop-shadow-2xl
                `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT INTRO TITLE
                |--------------------------------------------------------------------------
                */
                variant === "contact-intro-title" &&
                `
                    font-headline-xl

                    text-[28px]
                    sm:text-[34px]
                    md:text-[44px]
                    lg:text-headline-xl

                    mb-unit-lg

                    text-primary
                `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT SUPPORT TITLE
                |--------------------------------------------------------------------------
                */
                variant === "contact-support-title" &&
                `
                    font-headline-xl

                    text-[28px]
                    sm:text-[34px]
                    md:text-[44px]
                    lg:text-headline-xl

                    mb-unit-lg

                    text-primary
                `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT FAQ TITLE
                |--------------------------------------------------------------------------
                */
                variant === "contact-faq" &&
                `
                    font-headline-xl

                    text-[30px]
                    sm:text-[38px]
                    md:text-[52px]

                    mb-unit-xl

                    text-center
                    text-primary
                `,

                /*
                |--------------------------------------------------------------------------
                | CONTACT FINAL CTA
                |--------------------------------------------------------------------------
                */
                variant === "contact-final-cta" &&
                `
                   font-display-lg

    text-[30px]
    sm:text-[40px]
    md:text-[56px]
    lg:text-[72px]

    leading-[1.05]

    mb-6

    text-on-surface
    drop-shadow-lg
                `,
                variant === "contact-final" &&
                `
    bg-primary-container
    text-on-primary-container

    rounded-xl

    px-6
    sm:px-8
    md:px-10

    py-3
    md:py-4

    text-sm
    sm:text-base
    md:text-lg

    font-bold

    transition-all
    duration-300

    hover:-translate-y-1
    hover:shadow-[0_0_30px_rgba(37,99,235,0.4)]
`,
                className
            )}
        >
            {children}
        </Component>
    );
}
