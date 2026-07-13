import Overly from "@/components/Service/Hero/Overly";
import Enterprise from "@/components/Service/Hero/Enterprise";
import Header from "@/components/Service/Hero/Header";
import HeroTypography from "../../../components/Service/Hero/HeroTypography";
import HeroVerified from "@/components/Service/Hero/HeroVerified";
import HeroSupport from "@/components/Service/Hero/HeroSupport";
import HeroImage from "@/components/Service/Hero/HeroImage";

export default function HeroSection() {
    return (
        <header
            className="
  relative

  pt-32
  md:pt-44

  pb-16
  md:pb-24

  overflow-hidden
    isolate

  px-5
  md:px-10
  xl:px-16

  max-w-container
  mx-auto">


  <Overly/>
            <div className="  grid
  grid-cols-1
  lg:grid-cols-2

  items-center

  gap-12
  lg:gap-20">
                <div className="z-10  order-2
  lg:order-1

  max-w-2xl">
                <Enterprise/>
                  <Header/>
                 <HeroTypography/>
                    <div className="flex flex-wrap gap-unit-md mb-unit-xl">
<HeroVerified/>
                      <HeroSupport/>
                    </div>
                </div>
                <div className="relative group">
                    <div
                        className="absolute inset-0 bg-primary/20 blur-[100px] rounded-full group-hover:bg-primary/30 transition-all duration-700"></div>
<HeroImage/>
                </div>
            </div>
        </header>
    )
}