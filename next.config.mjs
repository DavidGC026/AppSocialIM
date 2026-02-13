/** @type {import('next').NextConfig} */
const nextConfig = {
  output: "export",
  // La app se servirá bajo https://dvguzman.com/calendario
  //basePath: "/calendario",
  //assetPrefix: "/calendario",
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
    ],
    unoptimized: true,
  },
};

export default nextConfig;
