// =====================
// MODAL
// =====================
export function initHomeModal() {
  const modal = document.getElementById("modal");
  const modalImg = document.getElementById("modalImg");
  const closeModal = document.getElementById("closeModal");

  if (!modal || !modalImg) return;

  document.querySelectorAll(".project-item").forEach(item => {
    item.addEventListener("click", () => {
      const img = item.querySelector("img");
      if (!img) return;

      modal.classList.remove("hidden");
      modal.classList.add("flex");

      modalImg.src = img.src;
    });
  });

  if (closeModal) {
    closeModal.onclick = () => {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    };
  }
}


// =====================
// FADE-IN
// =====================
export function initHomeFade() {
  const elements = document.querySelectorAll(".fade-in");
  if (!elements.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  elements.forEach(el => observer.observe(el));
}


// =====================
// MOBILE MENU + FLOATING
// =====================
export function initHomeLayout() {
  const menuBtn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");
  const closeMenu = document.getElementById("closeMenu");

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener("click", () => {
      mobileMenu.classList.remove("opacity-0", "pointer-events-none");
    });
  }

  if (closeMenu && mobileMenu) {
    closeMenu.addEventListener("click", () => {
      mobileMenu.classList.add("opacity-0", "pointer-events-none");
    });
  }

  const btn = document.getElementById("floatingBtn");
  const menu = document.getElementById("floatingMenu");

  if (btn && menu) {
    btn.addEventListener("click", () => {
      menu.classList.toggle("opacity-0");
      menu.classList.toggle("pointer-events-none");
    });
  }
}


// =====================
// FAQ
// =====================
export function initHomeFAQ() {
  const items = document.querySelectorAll(".faq-item");
  if (!items.length) return;

  items.forEach(item => {
    const btn = item.querySelector(".faq-question");
    const answer = item.querySelector(".faq-answer");
    const icon = item.querySelector(".faq-icon");

    if (!btn || !answer) return;

    btn.addEventListener("click", () => {

      // close others
      items.forEach(el => {
        if (el !== item) {
          el.querySelector(".faq-answer").style.maxHeight = null;
          el.querySelector(".faq-icon").innerText = "+";
        }
      });

      // toggle
      if (answer.style.maxHeight) {
        answer.style.maxHeight = null;
        icon.innerText = "+";
      } else {
        answer.style.maxHeight = answer.scrollHeight + "px";
        icon.innerText = "−";
      }

    });
  });
}


// =====================
// BEFORE / AFTER SLIDER
// =====================
export function initHomeSlider() {
  const sliders = document.querySelectorAll(".slider");
  if (!sliders.length) return;

  sliders.forEach(slider => {
    const box = slider.parentElement.querySelector(".afterBox");
    if (!box) return;

    slider.addEventListener("input", (e) => {
      box.style.width = e.target.value + "%";
    });
  });
}


// =====================
// FILTER
// =====================
export function initHomeFilter() {
  const buttons = document.querySelectorAll(".filter-btn");
  const items = document.querySelectorAll(".project-item");

  if (!buttons.length || !items.length) return;

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {

      buttons.forEach(b => {
        b.classList.remove("bg-primary", "text-darkbg");
      });

      btn.classList.add("bg-primary", "text-darkbg");

      const filter = btn.dataset.filter;

      items.forEach(item => {
        item.style.display =
          (filter === "all" || item.dataset.category === filter)
            ? "block"
            : "none";
      });

    });
  });
}