
    export function initGlassEffect() {
        const glassCards =
            document.querySelectorAll<HTMLElement>(".glass-card");

        const cleanupFunctions: (() => void)[] = [];

        glassCards.forEach((card) => {
            const handleMouseMove = (e: MouseEvent) => {
                const rect = card.getBoundingClientRect();

                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.setProperty("--mouse-x", `${x}px`);
                card.style.setProperty("--mouse-y", `${y}px`);
            };

            card.addEventListener("mousemove", handleMouseMove);

            cleanupFunctions.push(() => {
                card.removeEventListener(
                    "mousemove",
                    handleMouseMove
                );
            });
        });

        return () => {
            cleanupFunctions.forEach((cleanup) => cleanup());
        };

}