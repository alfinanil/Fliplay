// Kitsu API (JSON:API): 20 anime terpopuler + kategori (genre) tiap anime
const API_URL =
  "https://kitsu.io/api/edge/anime?sort=popularityRank&page%5Blimit%5D=20&include=categories";

const animeList = document.querySelector("#anime-list");
const loadingMessage = document.querySelector("#loading-message");
const errorBox = document.querySelector("#error-box");
const errorMessage = document.querySelector("#error-message");
const retryButton = document.querySelector("#retry-button");
const controls = document.querySelector("#controls");
const searchInput = document.querySelector("#search-input");
const auraButtons = document.querySelector("#aura-buttons");
const resultCount = document.querySelector("#result-count");
const emptyMessage = document.querySelector("#empty-message");

// ===== STATE =====
let animeData = []; // data asli dari API (tidak diubah, hanya difilter)
let activeAura = "all";
let keyword = "";

// ===== AURA =====
// API tidak punya field "aura", jadi dipetakan dari kategori (genre) / jumlah episode
const getGenres = (anime) => anime.genres.map((genre) => genre.toLowerCase());

// Kitsu memisahkan data utama (data) dan kategori (included), jadi digabung dulu
// menjadi satu object sederhana per anime agar mudah dipakai di bagian lain
const normalizeAnime = (item, included) => {
  const { attributes, relationships } = item;

  const genres = relationships.categories.data
    .map((ref) => included.find((inc) => inc.type === "categories" && inc.id === ref.id))
    .filter(Boolean)
    .map((category) => category.attributes.title);

  const rating = attributes.averageRating === null ? NaN : Number(attributes.averageRating);

  return {
    id: item.id,
    title: attributes.canonicalTitle,
    titleEnglish: attributes.titles?.en ?? "",
    type: attributes.subtype,
    episodes: attributes.episodeCount,
    // averageRating Kitsu berskala 0-100, diubah ke skala 0-10
    score: Number.isNaN(rating) ? null : (rating / 10).toFixed(1),
    poster: attributes.posterImage?.medium ?? "",
    genres,
  };
};

const AURAS = [
  { id: "all", label: "Semua", match: () => true },
  {
    id: "high-energy",
    label: "High Energy",
    match: (anime) => getGenres(anime).some((g) => ["action", "adventure", "sports"].includes(g)),
  },
  {
    id: "melancholy",
    label: "Melancholy",
    match: (anime) => getGenres(anime).some((g) => ["drama", "romance", "tragedy", "psychological"].includes(g)),
  },
  {
    id: "binge-mode",
    label: "Binge Mode",
    match: (anime) => (anime.episodes ?? 0) >= 24,
  },
];

// Mencegah XSS: ubah karakter HTML pada teks dari API menjadi teks biasa
const escapeHTML = (text) => {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
};

// ===== RENDER =====
const renderAuraButtons = () => {
  auraButtons.innerHTML = AURAS.map(
    (aura) => `
      <button type="button" data-aura="${aura.id}" aria-pressed="${aura.id === activeAura}"
        class="aura-btn rounded-full border-2 px-5 py-2 text-sm font-bold focus-visible:outline-2 focus-visible:outline-play-400">
        ${aura.label}
      </button>
    `
  ).join("");
  updateAuraStyle();
};

// Beri gaya "aktif" pada tombol Aura yang sedang dipilih
const updateAuraStyle = () => {
  auraButtons.querySelectorAll(".aura-btn").forEach((button) => {
    const isActive = button.dataset.aura === activeAura;
    button.setAttribute("aria-pressed", String(isActive));
    button.classList.toggle("bg-flip-500", isActive);
    button.classList.toggle("border-flip-500", isActive);
    button.classList.toggle("text-white", isActive);
    button.classList.toggle("bg-white", !isActive);
    button.classList.toggle("border-paper-200", !isActive);
    button.classList.toggle("text-ink-900", !isActive);
  });
};

const renderAnime = (animes) => {
  animeList.innerHTML = animes
    .map((anime) => {
      const { id, title, type, episodes, score, poster } = anime;
      const genres = anime.genres.slice(0, 2).join(", ");

      return `
        <article class="overflow-hidden rounded-2xl bg-white shadow" data-id="${id}">
          <img src="${escapeHTML(poster)}" alt="Poster ${escapeHTML(title)}" loading="lazy"
            class="aspect-[2/3] w-full object-cover">
          <div class="p-4">
            <h2 class="line-clamp-2 text-lg font-bold">${escapeHTML(title)}</h2>
            <p class="mt-2 text-sm text-ink-700">${escapeHTML(type ?? "-")} &bull; ${episodes ?? "?"} episode</p>
            <p class="mt-1 text-sm text-ink-700">${escapeHTML(genres || "-")}</p>
            <p class="mt-1 text-sm font-bold text-flip-600">&#9733; ${score ?? "-"}</p>
          </div>
        </article>
      `;
    })
    .join("");
};

// ===== FILTER: gabungan search + Aura =====
const applyFilters = () => {
  const aura = AURAS.find((item) => item.id === activeAura);

  const filtered = animeData.filter((anime) => {
    const titles = `${anime.title} ${anime.titleEnglish}`.toLowerCase();
    return titles.includes(keyword) && aura.match(anime);
  });

  renderAnime(filtered);
  emptyMessage.hidden = filtered.length > 0;
  resultCount.textContent = `Menampilkan ${filtered.length} dari ${animeData.length} anime`;
};

// ===== EVENT HANDLING =====
searchInput.addEventListener("input", () => {
  keyword = searchInput.value.trim().toLowerCase();
  applyFilters();
});

// 2) click: pilih Aura (event delegation pada container tombol)
auraButtons.addEventListener("click", (event) => {
  const button = event.target.closest(".aura-btn");
  if (!button) return;

  activeAura = button.dataset.aura;
  updateAuraStyle();
  applyFilters();
});

// 3) click: coba lagi saat error
retryButton.addEventListener("click", () => getAnime());

// ===== FETCH =====
const getAnime = async () => {
  try {
    // State: loading
    loadingMessage.textContent = "Memuat data anime...";
    loadingMessage.hidden = false;
    errorBox.hidden = true;
    controls.hidden = true;
    emptyMessage.hidden = true;
    animeList.innerHTML = "";

    const response = await fetch(API_URL);

    // fetch() tidak otomatis menganggap 404/500 sebagai error, jadi cek manual
    if (!response.ok) {
      throw new Error(`Data gagal dimuat (status ${response.status})`);
    }

    const result = await response.json();
    animeData = result.data.map((item) => normalizeAnime(item, result.included ?? []));

    // Reset filter, lalu tampilkan kontrol dan card
    activeAura = "all";
    keyword = "";
    searchInput.value = "";
    renderAuraButtons();
    controls.hidden = false;
    applyFilters();
  } catch (error) {
    // State: error
    errorMessage.textContent = "Data gagal dimuat. Periksa koneksi internet, lalu coba lagi.";
    errorBox.hidden = false;
    console.error(error);
  } finally {
    loadingMessage.hidden = true;
  }
};

getAnime();
