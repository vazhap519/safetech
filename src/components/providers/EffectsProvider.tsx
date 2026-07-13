"use client";

import { useEffect } from "react";

import { initGlassEffect }
    from "@/lib/animations/glassEffect";

import { initScrollReveal }
    from "@/lib/animations/scrollReveal";

export default function EffectsProvider() {

    useEffect(() => {

        /*
        |--------------------------------------------------------------------------
        | INIT EFFECTS
        |--------------------------------------------------------------------------
        */

        const cleanupGlass =
            initGlassEffect();

        const observer =
            initScrollReveal();



        /*
        |--------------------------------------------------------------------------
        | CLEANUP
        |--------------------------------------------------------------------------
        */

        return () => {

            cleanupGlass();

            observer.disconnect();
        };

    }, []);

    return null;
}