export default function Header({settings}){
    return (

<header className="border-b border-slate-800 bg-darkbg sticky top-0">

<div className="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

<div className="text-2xl font-bold text-primary">
SAFETECH
</div>

<nav className="hidden md:flex gap-8 text-sm">

<a href="index.html" className="hover:text-primary">Home</a>
<a href="#" className="text-primary">Services</a>
<a href="#">Projects</a>
<a href="#">About</a>
<a href="#">Blog</a>
<a href="#">Contact</a>

</nav>

<button className="bg-primary text-darkbg px-4 py-2 rounded-md">
Get Consultation
</button>

</div>

</header>
    )
}