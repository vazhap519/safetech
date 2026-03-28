const SectionCard = ({ children }) => {
  return (
    <section className="px-4 sm:px-6 lg:px-8 py-14">
      <div className="
        max-w-6xl mx-auto
        rounded-2xl
        bg-white/[0.03]
        border border-white/10
        p-6 sm:p-10 lg:p-12
        shadow-[0_10px_40px_rgba(0,0,0,0.3)]
        hover:bg-white/[0.05]
        transition-all duration-300
      ">
        {children}
      </div>
    </section>
  );
};
export default SectionCard