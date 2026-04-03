"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";

import Link from "next/link";
import Image from "next/image";
import { motion } from "framer-motion";
import MagneticCard from "./MagneticCard";
export default function RelatedProjects({ projects = [] }) {
  if (!projects.length) return null;

  return (
<div className="mt-32 py-20 bg-gradient-to-b from-[#F8FAFC] to-white border-t">
      {/* TITLE */}
<h2 className="text-4xl font-bold text-[#0B3C5D] mb-12 text-center">
        მსგავსი პროექტები
      </h2>

      {/* SLIDER */}
    <Swiper
  modules={[Autoplay]}
  spaceBetween={24}
  slidesPerView={1.2}
  breakpoints={{
    640: { slidesPerView: 2 },
    1024: { slidesPerView: 3 },
  }}
  autoplay={{
    delay: 2500,
    disableOnInteraction: false,
  }}
  loop
  className="!px-4"
>
        {projects.map((project, i) => (
          <SwiperSlide key={project.slug}>
            <MagneticCard project={project} i={i} />
          </SwiperSlide>
        ))}

      </Swiper>
    </div>
  );
}