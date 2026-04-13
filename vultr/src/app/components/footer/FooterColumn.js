export default function FooterColumn({ title, children }) {
  return (
    <div className="flex flex-col items-center md:items-start">
      <h3 className="font-semibold mb-4">{title}</h3>
      {children}
    </div>
  );
}