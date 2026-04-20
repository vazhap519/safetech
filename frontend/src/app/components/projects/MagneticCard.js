"use client";

import Link from "next/link";
import Image from "next/image";
import { motion } from "framer-motion";
import { mediaUrl } from "@/lib/media";

export default function MagneticCard({ project, i }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 80 }}
      whileInView={{ opacity: 1, y: 0 }}
      transition={{ delay: i * 0.1, duration: 0.6 }}
    >
      <Link href={`/projects/${project.slug}`}>
        <motion.div
          className="group relative rounded-3xl overflow-hidden bg-white shadow-md hover:shadow-2xl transition-all duration-500"
          whileHover={{ y: -8 }}
        >

          {/* IMAGE */}
          <div className="relative h-64 overflow-hidden">

            <Image
              src={mediaUrl(project.image)}
              alt={project.title}
              fill
              sizes="(max-width: 768px) 100vw, 33vw"
              className="object-cover transition duration-700 group-hover:scale-110"
            />

            {/* DARK GRADIENT */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />

          </div>

          {/* CONTENT */}
          <div className="absolute bottom-0 p-5 text-white w-full">

            <span className="inline-block text-xs bg-[#00C2A8] px-3 py-1 rounded-full mb-2">
              {project.category?.name}
            </span>

            <h3 className="text-lg font-semibold leading-tight">
              {project.title}
            </h3>

            {/* CTA */}
            <div className="mt-2 text-sm opacity-0 group-hover:opacity-100 transition">
              ნახე პროექტი →
            </div>

          </div>

          {/* GLOW BORDER */}
          <div className="absolute inset-0 border border-transparent group-hover:border-[#00C2A8]/40 rounded-3xl transition pointer-events-none" />

        </motion.div>
      </Link>
    </motion.div>
  );
}
