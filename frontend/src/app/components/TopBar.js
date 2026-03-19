export default function TopBar({ settings }) {
  const topbar = settings?.topbar || {};

  return (
    <div className="bg-primary text-darkbg text-sm py-2">
      <div className="max-w-7xl mx-auto flex justify-between px-6">

        <span>{topbar.text}</span>

        <div className="flex gap-6">
          <span>📞 {topbar.phone}</span>
          <span>💬 WhatsApp</span>
        </div>

      </div>
    </div>
  );
}