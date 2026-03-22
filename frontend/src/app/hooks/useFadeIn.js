"use client";

import { useEffect, useRef, useState } from "react";

export default function useFadeIn(options = {}) {
  const ref = useRef(null);

  const [visible, setVisible] = useState(false);
  const [mounted, setMounted] = useState(false); // 🔥 მთავარი fix

  useEffect(() => {
    setMounted(true); // 🔥 hydration fix

    const element = ref.current;
    if (!element) return;

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true);
          observer.unobserve(element);
        }
      },
      {
        threshold: options.threshold || 0.15,
        rootMargin: options.rootMargin || "0px 0px -80px 0px",
      }
    );

    observer.observe(element);

    return () => {
      if (element) observer.unobserve(element);
    };
  }, []);

  return [ref, mounted && visible]; // 🔥 critical change
}