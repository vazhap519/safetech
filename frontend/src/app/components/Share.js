export default function Share({ data, url: propUrl }) {
  if (!data) return null;

  const url =
    propUrl ||
    (typeof window !== "undefined" ? window.location.href : "");

  return (
    <div className="bg-white py-6 border-b">
      <div className="max-w-7xl mx-auto px-4 flex flex-col items-center gap-4 text-center">

        <p className="text-sm text-gray-600">
          {data.title}
        </p>

        <div className="flex flex-wrap justify-center gap-3">

          {data.buttons?.map((btn, i) => {
            const Icon = icons[btn.icon];

            if (btn.icon === "link") {
              return <CopyButton key={i} url={url} />;
            }

            const shareUrl = btn.url.replace("{url}", url);

            return (
              <a
                key={i}
                href={shareUrl}
                target="_blank"
                rel="noopener noreferrer"
                className={`px-4 py-2 text-white rounded-lg text-sm flex items-center gap-2 hover:scale-105 transition ${btn.color}`}
              >
                {Icon && <Icon size={16} />}
                {btn.name}
              </a>
            );
          })}

        </div>
      </div>
    </div>
  );
}