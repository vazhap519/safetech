import Socials from "./Socials";

export default function Brand({settings}){
    return(
<>
        <div className="flex flex-col items-center md:items-start">
          <h2 className="text-2xl font-bold mb-4">
            <span>Safe</span>
            <span className="text-[#00C2A8]">tech</span>
          </h2>

          <p className="text-sm text-gray-300 max-w-xs">
            {settings?.brand_description}
            {/* უსაფრთხოების კამერები, ქსელური სერვისები და IT მხარდაჭერა საქართველოში. */}
          </p>

          {/* 🔥 SOCIAL */}
        <Socials socials={settings?.socials}/>
        </div>
</>
    );
}