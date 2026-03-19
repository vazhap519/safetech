import Hero from "./components/home/hero";
import QuickCTA from "./components/home/QuickCTA";
import HomeClient from "./components/HomeClient";
import { getHomeData } from "../lib/settings";

export default async function Home() {

  const { hero, cta } = await getHomeData();

  return (
    <>
      <Hero hero={hero} />
      <QuickCTA cta={cta} />
      <HomeClient />
    </>
  );
}