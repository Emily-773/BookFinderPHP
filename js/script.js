const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");
const results = document.getElementById("results");
const suggestions = document.getElementById("suggestions");
const darkModeToggle = document.getElementById("darkModeToggle");

const authorFilter = document.getElementById("authorFilter");
const subjectFilter = document.getElementById("subjectFilter");
const sortResults = document.getElementById("sortResults");
const applyFiltersBtn = document.getElementById("applyFiltersBtn");
const clearFiltersBtn = document.getElementById("clearFiltersBtn");

const API_KEY = "AIzaSyAbhpNuzreYrqOsU0u4nj75_NO5WohpKNE";

const FALLBACK_IMAGE = "https://via.placeholder.com/128x190?text=No+Cover";
const HISTORY_KEY = "bookfinder_history";
const THEME_KEY = "bookfinder_theme";

let currentController = null;
let suggestionIndex = -1;
let currentBooks = [];

/* -------------------------
   Event listeners
-------------------------- */
if (searchBtn) {
  searchBtn.addEventListener("click", searchBooks);
}

if (applyFiltersBtn) {
  applyFiltersBtn.addEventListener("click", applyFiltersAndSort);
}

if (clearFiltersBtn) {
  clearFiltersBtn.addEventListener("click", () => {
    if (authorFilter) authorFilter.value = "";
    if (subjectFilter) subjectFilter.value = "";
    if (sortResults) sortResults.value = "";
    displayBooks(currentBooks);
  });
}

/* -------------------------
   Dark mode
-------------------------- */
function applySavedTheme() {
  const savedTheme = localStorage.getItem(THEME_KEY);

  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    if (darkModeToggle) darkModeToggle.textContent = "☀️";
  } else {
    document.body.classList.remove("dark-mode");
    if (darkModeToggle) darkModeToggle.textContent = "🌙";
  }
}

function toggleDarkMode() {
  document.body.classList.toggle("dark-mode");
  const isDark = document.body.classList.contains("dark-mode");
  localStorage.setItem(THEME_KEY, isDark ? "dark" : "light");

  if (darkModeToggle) {
    darkModeToggle.textContent = isDark ? "☀️" : "🌙";
  }
}

/* -------------------------
   Suggestions
-------------------------- */
function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
}

async function fetchSuggestions(query) {
  if (!suggestions) return;

  if (!query || query.length < 2) {
    hideSuggestions();
    return;
  }

  try {
    const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=5&key=${API_KEY}`;
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`Suggestion error: ${response.status}`);
    }

    const data = await response.json();
    const items = data.items || [];

    const titles = [
      ...new Set(items.map((item) => item.volumeInfo?.title).filter(Boolean))
    ];

    renderSuggestions(titles);
  } catch (error) {
    console.error("Suggestion fetch failed:", error);
    hideSuggestions();
  }
}

function renderSuggestions(items) {
  if (!suggestions) return;

  suggestions.innerHTML = "";
  suggestionIndex = -1;

  if (!items.length) {
    hideSuggestions();
    return;
  }

  items.forEach((item, index) => {
    const div = document.createElement("div");
    div.className = "suggestion-item";
    div.textContent = item;
    div.setAttribute("role", "option");
    div.setAttribute("tabindex", "-1");
    div.dataset.index = index;

    div.addEventListener("click", () => {
      if (searchInput) searchInput.value = item;
      hideSuggestions();
      searchBooks();
    });

    suggestions.appendChild(div);
  });

  suggestions.style.display = "block";
}

function hideSuggestions() {
  if (!suggestions) return;
  suggestions.style.display = "none";
  suggestions.innerHTML = "";
  suggestionIndex = -1;
}

function updateSuggestionHighlight(items) {
  items.forEach((item, index) => {
    item.classList.toggle("active", index === suggestionIndex);
  });
}

/* -------------------------
   Initialise on page load
-------------------------- */
document.addEventListener("DOMContentLoaded", () => {
  renderHistory();
  applySavedTheme();

  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", toggleDarkMode);
  }

  if (searchInput) {
    const debouncedSuggestions = debounce((value) => {
      fetchSuggestions(value);
    }, 300);

    searchInput.addEventListener("input", () => {
      debouncedSuggestions(searchInput.value.trim());
    });

    searchInput.addEventListener("keydown", (e) => {
      const items = suggestions ? suggestions.querySelectorAll(".suggestion-item") : [];

      if (items.length) {
        if (e.key === "ArrowDown") {
          e.preventDefault();
          suggestionIndex = (suggestionIndex + 1) % items.length;
          updateSuggestionHighlight(items);
          return;
        }

        if (e.key === "ArrowUp") {
          e.preventDefault();
          suggestionIndex = (suggestionIndex - 1 + items.length) % items.length;
          updateSuggestionHighlight(items);
          return;
        }

        if (e.key === "Enter" && suggestionIndex >= 0) {
          e.preventDefault();
          searchInput.value = items[suggestionIndex].textContent;
          hideSuggestions();
          searchBooks();
          return;
        }
      }

      if (e.key === "Enter") {
        e.preventDefault();
        searchBooks();
      } else if (e.key === "Escape") {
        hideSuggestions();
      }
    });
  }

  document.addEventListener("click", (e) => {
    if (suggestions && searchInput && !suggestions.contains(e.target) && e.target !== searchInput) {
      hideSuggestions();
    }
  });
});

/* -------------------------
   Search
-------------------------- */
async function searchBooks() {
  if (!searchInput || !results) return;

  const query = searchInput.value.trim();
  hideSuggestions();

  if (!query) {
    showMessage("Please enter a book title.");
    searchInput.focus();
    return;
  }

  if (currentController) {
    currentController.abort();
  }

  currentController = new AbortController();

  setLoading(true);
  showSpinner();

  try {
    const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=12&key=${API_KEY}`;

    const response = await fetch(url, {
      signal: currentController.signal
    });

    if (!response.ok) {
      throw new Error(`HTTP error ${response.status}`);
    }

    const data = await response.json();

    currentBooks = data.items || [];

    saveSearchHistory(query);
    renderHistory();
    applyFiltersAndSort();
  } catch (error) {
    if (error.name === "AbortError") return;

    console.error("Search failed:", error);
    showMessage("Error loading books. Please try again.");
  } finally {
    setLoading(false);
  }
}

/* -------------------------
   Filter and sort
-------------------------- */
function applyFiltersAndSort() {
  let filteredBooks = [...currentBooks];

  const authorValue = authorFilter ? authorFilter.value.trim().toLowerCase() : "";
  const subjectValue = subjectFilter ? subjectFilter.value.trim().toLowerCase() : "";
  const sortValue = sortResults ? sortResults.value : "";

  if (authorValue) {
    filteredBooks = filteredBooks.filter((book) => {
      const authors = book.volumeInfo?.authors || [];
      return authors.join(" ").toLowerCase().includes(authorValue);
    });
  }

  if (subjectValue) {
    filteredBooks = filteredBooks.filter((book) => {
      const categories = book.volumeInfo?.categories || [];
      const description = book.volumeInfo?.description || "";
      const title = book.volumeInfo?.title || "";

      return (
        categories.join(" ").toLowerCase().includes(subjectValue) ||
        description.toLowerCase().includes(subjectValue) ||
        title.toLowerCase().includes(subjectValue)
      );
    });
  }

  if (sortValue === "title-az") {
    filteredBooks.sort((a, b) => {
      const titleA = a.volumeInfo?.title || "";
      const titleB = b.volumeInfo?.title || "";
      return titleA.localeCompare(titleB);
    });
  }

  if (sortValue === "newest") {
    filteredBooks.sort((a, b) => getBookYear(b) - getBookYear(a));
  }

  if (sortValue === "oldest") {
    filteredBooks.sort((a, b) => getBookYear(a) - getBookYear(b));
  }

  displayBooks(filteredBooks);
}

function getBookYear(book) {
  const publishedDate = book.volumeInfo?.publishedDate || "";
  const year = parseInt(publishedDate.substring(0, 4), 10);
  return Number.isNaN(year) ? 0 : year;
}

/* -------------------------
   Display books
-------------------------- */
function displayBooks(books) {
  if (!results) return;

  results.innerHTML = "";

  if (!books.length) {
    showMessage("No books found.");
    return;
  }

  books.forEach((book) => {
    const info = book.volumeInfo || {};
    const bookId = book.id || crypto.randomUUID();

    const title = info.title || "No title";
    const authors = Array.isArray(info.authors) ? info.authors.join(", ") : "Unknown author";
    const image = info.imageLinks?.thumbnail || FALLBACK_IMAGE;
    const description = info.description || "No description available.";
    const publishedDate = info.publishedDate || "Unknown";
    const publisher = info.publisher || "Unknown";
    const previewLink = info.previewLink || "#";

    const col = document.createElement("div");
    col.className = "col-md-3 mb-4";

    const card = document.createElement("div");
    card.className = "card book-card h-100 shadow-sm";

    const img = document.createElement("img");
    img.src = image;
    img.className = "card-img-top";
    img.alt = `Cover of ${title}`;
    img.loading = "lazy";
    img.onerror = function () {
      this.src = FALLBACK_IMAGE;
    };

    const cardBody = document.createElement("div");
    cardBody.className = "card-body d-flex flex-column";

    const titleEl = document.createElement("h6");
    titleEl.className = "card-title";
    titleEl.textContent = title;

    const authorEl = document.createElement("p");
    authorEl.className = "card-text";
    authorEl.textContent = authors;

    const yearEl = document.createElement("p");
    yearEl.className = "card-text small text-muted";
    yearEl.textContent = `Published: ${publishedDate}`;

    const buttonGroup = document.createElement("div");
    buttonGroup.className = "mt-auto d-flex gap-2 flex-wrap";

    const detailsBtn = document.createElement("button");
    detailsBtn.className = "btn btn-sm btn-outline-primary";
    detailsBtn.textContent = "View Details";
    detailsBtn.addEventListener("click", () => {
      showBookDetails({
        id: bookId,
        title,
        authors,
        image,
        description,
        publishedDate,
        publisher,
        previewLink
      });
    });

    const saveForm = document.createElement("form");
    saveForm.method = "POST";
    saveForm.action = "save-book.php";
    saveForm.className = "d-inline";

    saveForm.innerHTML = `
      <input type="hidden" name="book_id" value="${escapeHtml(bookId)}">
      <input type="hidden" name="title" value="${escapeHtml(title)}">
      <input type="hidden" name="authors" value="${escapeHtml(authors)}">
      <input type="hidden" name="thumbnail" value="${escapeHtml(image)}">
      <input type="hidden" name="status" value="Want to Read">
      <button type="submit" class="btn btn-sm btn-outline-danger">Save Book</button>
    `;

    buttonGroup.appendChild(detailsBtn);
    buttonGroup.appendChild(saveForm);

    cardBody.appendChild(titleEl);
    cardBody.appendChild(authorEl);
    cardBody.appendChild(yearEl);
    cardBody.appendChild(buttonGroup);

    card.appendChild(img);
    card.appendChild(cardBody);
    col.appendChild(card);

    results.appendChild(col);
  });
}

/* -------------------------
   Messages and loading
-------------------------- */
function showMessage(message) {
  if (!results) return;
  results.innerHTML = `<p class="text-center">${message}</p>`;
}

function showSpinner() {
  if (!results) return;

  results.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border" role="status" aria-hidden="true"></div>
      <p class="mt-2">Loading books...</p>
    </div>
  `;
}

function setLoading(isLoading) {
  if (!searchBtn) return;
  searchBtn.disabled = isLoading;
  searchBtn.textContent = isLoading ? "Searching..." : "Search";
}

/* -------------------------
   Search history
-------------------------- */
function getHistory() {
  return JSON.parse(localStorage.getItem(HISTORY_KEY)) || [];
}

function saveHistory(history) {
  localStorage.setItem(HISTORY_KEY, JSON.stringify(history));
}

function saveSearchHistory(query) {
  let history = getHistory();

  history = history.filter((item) => item.toLowerCase() !== query.toLowerCase());
  history.unshift(query);

  if (history.length > 8) {
    history = history.slice(0, 8);
  }

  saveHistory(history);
}

function renderHistory() {
  const historyContainer = document.getElementById("history");
  if (!historyContainer) return;

  const history = getHistory();
  historyContainer.innerHTML = "";

  if (!history.length) {
    historyContainer.innerHTML = "<p>No recent searches.</p>";
    return;
  }

  history.forEach((query) => {
    const btn = document.createElement("button");
    btn.className = "btn btn-sm btn-outline-secondary me-2 mb-2";
    btn.textContent = query;
    btn.addEventListener("click", () => {
      if (searchInput) searchInput.value = query;
      searchBooks();
    });

    historyContainer.appendChild(btn);
  });
}

/* -------------------------
   Book details
-------------------------- */
function showBookDetails(book) {
  let modal = document.getElementById("bookDetailsModal");

  if (!modal) {
    modal = document.createElement("div");
    modal.id = "bookDetailsModal";
    modal.className = "book-modal-overlay";
    document.body.appendChild(modal);
  }

  modal.innerHTML = `
    <div class="book-modal-content">
      <button class="book-modal-close" aria-label="Close book details">&times;</button>
      <img src="${escapeHtml(book.image)}" alt="Cover of ${escapeHtml(book.title)}" class="img-fluid mb-3" style="max-height: 250px; object-fit: contain;">
      <h3>${escapeHtml(book.title)}</h3>
      <p><strong>Author(s):</strong> ${escapeHtml(book.authors)}</p>
      <p><strong>Publisher:</strong> ${escapeHtml(book.publisher)}</p>
      <p><strong>Published:</strong> ${escapeHtml(book.publishedDate)}</p>
      <p>${escapeHtml(book.description)}</p>
      <p>
        <a href="${escapeHtml(book.previewLink)}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
          Preview Book
        </a>
      </p>
    </div>
  `;

  modal.style.display = "flex";

  const closeBtn = modal.querySelector(".book-modal-close");
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
}

/* -------------------------
   Helper
-------------------------- */
function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

/* -------------------------
   Popular Current Reads Carousel
-------------------------- */
const popularCarousel = document.getElementById("popularCarousel");
const carouselPrev = document.getElementById("carouselPrev");
const carouselNext = document.getElementById("carouselNext");

async function loadPopularBooks() {
  if (!popularCarousel) return;

  try {
    const url = `https://www.googleapis.com/books/v1/volumes?q=bestseller+fiction&orderBy=relevance&maxResults=10&key=${API_KEY}`;
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error("Could not load popular books.");
    }

    const data = await response.json();
    const books = data.items || [];

    popularCarousel.innerHTML = "";

    books.forEach((book) => {
      const info = book.volumeInfo || {};

      const title = info.title || "No title";
      const authors = Array.isArray(info.authors) ? info.authors.join(", ") : "Unknown author";
      const image = info.imageLinks?.thumbnail || FALLBACK_IMAGE;

      const card = document.createElement("article");
      card.className = "carousel-book-card";

      card.innerHTML = `
        <img src="${escapeHtml(image)}" alt="Cover of ${escapeHtml(title)}" loading="lazy">
        <h3>${escapeHtml(title)}</h3>
        <p>${escapeHtml(authors)}</p>
      `;

      popularCarousel.appendChild(card);
    });

  } catch (error) {
    console.error("Carousel failed:", error);
    popularCarousel.innerHTML = "<p>Popular books could not be loaded.</p>";
  }
}

if (carouselPrev && popularCarousel) {
  carouselPrev.addEventListener("click", () => {
    popularCarousel.scrollBy({
      left: -220,
      behavior: "smooth"
    });
  });
}

if (carouselNext && popularCarousel) {
  carouselNext.addEventListener("click", () => {
    popularCarousel.scrollBy({
      left: 220,
      behavior: "smooth"
    });
  });
}

document.addEventListener("DOMContentLoaded", loadPopularBooks);