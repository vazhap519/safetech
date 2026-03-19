// MODAL
export function initModal() {
  const modal = document.getElementById("modal");
  if (!modal) return;

  const modalImg = document.getElementById("modalImg");
  const closeModal = document.getElementById("closeModal");

  document.querySelectorAll(".project-item").forEach(item=>{
    item.addEventListener("click", ()=>{
      const img = item.querySelector("img");

      modal.classList.remove("hidden");
      modal.classList.add("flex");

      modalImg.src = img.src;
    });
  });

  closeModal.onclick = ()=>{
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  };
}


// FADE-IN
export function initFadeIn() {
  const observer = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  },{ threshold:0.2 });

  document.querySelectorAll(".fade-in").forEach(el=>{
    observer.observe(el);
  });
}


// MOBILE MENU + FLOATING
export function initLayout() {

  const menuBtn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");
  const closeMenu = document.getElementById("closeMenu");

  if(menuBtn){
    menuBtn.addEventListener("click", ()=>{
      mobileMenu.classList.remove("opacity-0","pointer-events-none");
    });
  }

  if(closeMenu){
    closeMenu.addEventListener("click", ()=>{
      mobileMenu.classList.add("opacity-0","pointer-events-none");
    });
  }

  const btn = document.getElementById("floatingBtn");
  const menu = document.getElementById("floatingMenu");

  if(btn){
    btn.addEventListener("click", ()=>{
      menu.classList.toggle("opacity-0");
      menu.classList.toggle("pointer-events-none");
    });
  }
}


// FAQ
export function initFAQ() {
  document.querySelectorAll(".faq-question").forEach(btn => {

    btn.addEventListener("click", () => {

      const item = btn.parentElement;
      const answer = item.querySelector(".faq-answer");
      const icon = btn.querySelector(".faq-icon");

      document.querySelectorAll(".faq-item").forEach(el => {
        if(el !== item){
          el.querySelector(".faq-answer").style.maxHeight = null;
          el.querySelector(".faq-icon").innerText = "+";
        }
      });

      if(answer.style.maxHeight){
        answer.style.maxHeight = null;
        icon.innerText = "+";
      }else{
        answer.style.maxHeight = answer.scrollHeight + "px";
        icon.innerText = "−";
      }

    });

  });
}


// SLIDER
export function initSlider() {
  document.querySelectorAll(".slider").forEach(slider=>{
    const box = slider.parentElement.querySelector(".afterBox");

    slider.addEventListener("input", (e)=>{
      box.style.width = e.target.value + "%";
    });
  });
}


// FILTER
export function initFilter() {
  const buttons = document.querySelectorAll(".filter-btn");
  const items = document.querySelectorAll(".project-item");

  buttons.forEach(btn=>{
    btn.addEventListener("click", ()=>{

      buttons.forEach(b=>b.classList.remove("bg-primary","text-darkbg"));
      btn.classList.add("bg-primary","text-darkbg");

      const filter = btn.dataset.filter;

      items.forEach(item=>{
        item.style.display =
        (filter === "all" || item.dataset.category === filter)
        ? "block"
        : "none";
      });

    });
  });
}