"use client";

import { useEffect, useRef, useState } from "react";

export default function useFadeIn(options = {}) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(false);
  const threshold = options.threshold ?? 0.15;
  const rootMargin = options.rootMargin ?? "0px 0px -80px 0px";

  useEffect(() => {
    const element = ref.current;
    if (!element) return;

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true);
          observer.unobserve(element);
        }
      },
      { threshold, rootMargin },
    );

    observer.observe(element);

    return () => observer.disconnect();
  }, [rootMargin, threshold]);

  return [ref, visible];
}
