export function initScrollReveal() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(
                        "opacity-100",
                        "translate-y-0"
                    );

                    entry.target.classList.remove(
                        "opacity-0",
                        "translate-y-10"
                    );
                }
            });
        },
        {
            threshold: 0.1,
        }
    );

    const sections = document.querySelectorAll("section");

    sections.forEach((section) => {
        section.classList.add(
            "transition-all",
            "duration-1000",
            "opacity-0",
            "translate-y-10"
        );

        observer.observe(section);
    });

    return observer;
}