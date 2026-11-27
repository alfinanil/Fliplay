// ===== 1. NAVBAR TOGGLE =====
const menuToggle = document.querySelector("#menu-toggle");
const menuNav = document.querySelector("#menu-nav");

const setMenu = (open) => {
  // "max-sm:hidden" = menu disembunyikan hanya di layar kecil
  menuNav.classList.toggle("max-sm:hidden", !open);
  menuToggle.setAttribute("aria-expanded", String(open));
  menuToggle.textContent = open ? "Tutup" : "Menu";
};

menuToggle.addEventListener("click", () => {
  const isOpen = menuToggle.getAttribute("aria-expanded") === "true";
  setMenu(!isOpen);
});

// Tutup menu otomatis setelah salah satu link diklik (mobile)
menuNav.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => setMenu(false));
});

// ===== 2. VALIDASI FORM =====
const form = document.querySelector("#contact-form");
const formStatus = document.querySelector("#form-status");

const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Setiap field punya aturan validasi: return pesan error, atau "" jika valid
const rules = {
  nama: (value) => {
    if (value === "") return "Nama wajib diisi.";
    if (value.length < 3) return "Nama minimal 3 karakter.";
    return "";
  },
  email: (value) => {
    if (value === "") return "Email wajib diisi.";
    if (!emailPattern.test(value)) return "Format email tidak valid, contoh: nama@email.com";
    return "";
  },
  pesan: (value) => {
    if (value === "") return "Pesan wajib diisi.";
    if (value.length < 10) return "Pesan minimal 10 karakter.";
    return "";
  },
};

const showError = (field, message) => {
  const input = document.querySelector(`#${field}`);
  const errorElement = document.querySelector(`#error-${field}`);

  errorElement.textContent = message;
  errorElement.hidden = message === "";
  input.setAttribute("aria-invalid", String(message !== ""));
  input.classList.toggle("border-red-600", message !== "");
};

const validateField = (field) => {
  const value = document.querySelector(`#${field}`).value.trim();
  const message = rules[field](value);
  showError(field, message);
  return message === "";
};

// Validasi real-time: cek ulang saat user mengetik
Object.keys(rules).forEach((field) => {
  document.querySelector(`#${field}`).addEventListener("input", () => {
    validateField(field);
    formStatus.hidden = true;
  });
});

form.addEventListener("submit", (event) => {
  event.preventDefault();

  // Cek semua field (tanpa berhenti di field pertama yang salah)
  const results = Object.keys(rules).map((field) => validateField(field));
  const isValid = results.every((result) => result);

  if (!isValid) {
    formStatus.hidden = true;
    // Fokus ke field pertama yang bermasalah
    form.querySelector('[aria-invalid="true"]').focus();
    return;
  }

  const nama = document.querySelector("#nama").value.trim();
  formStatus.textContent = `Terima kasih, ${nama}! Pesanmu sudah kami terima.`;
  formStatus.hidden = false;
  form.reset();
});