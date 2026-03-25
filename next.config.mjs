/** @type {import('next').NextConfig} */
const nextConfig = {
  reactCompiler: true,

  images: {
    // 🔥 დატოვე მხოლოდ თუ იყენებ უკვე WebP + resized backend-ში
    unoptimized: true,

    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },

      // dev
      {
        protocol: "http",
        hostname: "localhost",
      },
      {
        protocol: "http",
        hostname: "127.0.0.1",
      },

      // API server
      {
        protocol: "http",
        hostname: "localhost",
        port: "8000",
      },
      {
        protocol: "http",
        hostname: "127.0.0.1",
        port: "8000",
      },

      // production API
      {
        protocol: "https",
        hostname: "api.safetech.ge",
      },
    ],
  },
};

export default nextConfig;