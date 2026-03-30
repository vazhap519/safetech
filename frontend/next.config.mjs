/** @type {import('next').NextConfig} */
const nextConfig = {
  reactCompiler: true,

  images: {
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

      // 🔥 შენი რეალური IP (ძალიან მნიშვნელოვანია)
      {
        protocol: "http",
        hostname: "192.168.100.238",
        port: "8000",
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

      // production
      {
        protocol: "https",
        hostname: "api.safetech.ge",
      },
    ],
  },
};

export default nextConfig;