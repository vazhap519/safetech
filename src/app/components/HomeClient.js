"use client";

import { useEffect } from "react";

import {
  initHomeModal,
  initHomeFade,
  initHomeLayout,
  initHomeFAQ,
  initHomeSlider,
  initHomeFilter
} from "@/lib/home";

export default function HomeClient() {

  useEffect(() => {

    initHomeModal();
    initHomeFade();
    initHomeLayout();
    initHomeFAQ();
    initHomeSlider();
    initHomeFilter();

  }, []);

  return null;
}