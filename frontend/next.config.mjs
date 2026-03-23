/** @type {import('next').NextConfig} */
const nextConfig = {
  reactCompiler: true,

  images: {
        unoptimized: true, // 🔥 ეს დაამატე

    remotePatterns: [
      // Unsplash
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },

      // localhost WITHOUT port 🔥 (შენი current case)
      {
        protocol: "http",
        hostname: "localhost",
      },

      // localhost WITH port
      {
        protocol: "http",
        hostname: "localhost",
        port: "8000",
      },

      // 127.0.0.1
      {
        protocol: "http",
        hostname: "127.0.0.1",
        port: "8000",
      },

      // production
      {
        protocol: "https",
        hostname: "api.safetech.ge",
      },
    ],
  },
};

export default nextConfig;