"use client";

import { useEffect, useRef, useState } from "react";

export default function useFadeIn(options = {}) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(false);

  const { threshold = 0.15, rootMargin = "0px 0px -80px 0px" } = options;

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
      { threshold, rootMargin }
    );

    observer.observe(element);

    return () => {
      observer.disconnect();
    };
  }, [threshold, rootMargin]); // ✅ eslint fix

  return [ref, visible]; // ✅ clean return
}