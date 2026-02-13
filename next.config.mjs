/** @type {import('next').NextConfig} */
const nextConfig = {
  output: "export",
  // La app se servirá bajo https://dvguzman.com/AppSocial
  basePath: "/AppSocial",
  assetPrefix: "/AppSocial",
  trailingSlash: true,
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
