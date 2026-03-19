/** @type {import('next').NextConfig} */
const nextConfig = {
  reactCompiler: true,

  images: {
    unoptimized: true, // 🔥 აქ უნდა იყოს

    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
      {
        protocol: "http",
        hostname: "localhost",
        port: "8000",
      },
    ],
  },
};

export default nextConfig;