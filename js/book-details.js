document.addEventListener("DOMContentLoaded", async function () {
  const detailsBox = document.getElementById("googleBookDetails");
  if (!detailsBox) return;

  const volumeId = detailsBox.dataset.volumeId || "";
  const title = detailsBox.dataset.title || "";
  const authors = detailsBox.dataset.authors || "";
  const query = `${title} ${authors}`.trim();

  function scoreBook(info) {
    let score = 0;
    if (info.pageCount) score++;
    if (info.publisher) score++;
    if (info.publishedDate) score++;
    if (info.categories) score++;
    if (info.description) score++;
    if (info.industryIdentifiers) score++;
    return score;
  }

  async function fetchBooks() {
    const searches = [];

    if (volumeId) {
      searches.push(fetch(`https://www.googleapis.com/books/v1/volumes/${encodeURIComponent(volumeId)}?key=AIzaSyAbhpNuzreYrqOsU0u4nj75_NO5WohpKNE`));
    }

    if (query) {
      searches.push(fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=10&keyAIzaSyAbhpNuzreYrqOsU0u4nj75_NO5WohpKNE`));
    }

    for (const request of searches) {
      try {
        const response = await request;
        if (!response.ok) continue;

        const data = await response.json();

        if (data.volumeInfo) {
          return data.volumeInfo;
        }

        if (data.items && data.items.length) {
          const best = data.items
            .map(item => item.volumeInfo || {})
            .sort((a, b) => scoreBook(b) - scoreBook(a))[0];

          return best;
        }
      } catch (error) {
        console.error("Google Books fetch failed:", error);
      }
    }

    return {};
  }

  const info = await fetchBooks();

  const ids = info.industryIdentifiers || [];
  const isbn10 = ids.find(id => id.type === "ISBN_10")?.identifier || "Not available";
  const isbn13 = ids.find(id => id.type === "ISBN_13")?.identifier || "Not available";

  document.getElementById("js-pages").textContent = info.pageCount || "Not available";
  document.getElementById("js-publisher").textContent = info.publisher || "Unknown";
  document.getElementById("js-published").textContent = info.publishedDate || "Unknown";
  document.getElementById("js-categories").textContent = info.categories ? info.categories.join(", ") : "Not available";
  document.getElementById("js-language").textContent = info.language ? info.language.toUpperCase() : "N/A";
  document.getElementById("js-isbn10").textContent = isbn10;
  document.getElementById("js-isbn13").textContent = isbn13;
  document.getElementById("js-rating").textContent =
    (info.averageRating || "Not rated") + "/5 (" + (info.ratingsCount || 0) + " ratings)";
  document.getElementById("js-description").innerHTML = info.description || "No description available.";

  const previewLink = document.getElementById("js-preview-link");

  if (previewLink && info.previewLink) {
    previewLink.href = info.previewLink;
    previewLink.style.display = "inline-block";
  }
});