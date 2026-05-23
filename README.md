# 📚 BookFinder (PHP Version)

BookFinder is a dynamic full-stack web application developed for the Advanced Web Development module at the University of Suffolk. The application allows users to search for books using the Google Books API, manage a personalised reading collection, write reviews, and track reading progress through a responsive and accessible interface.

---

## 🌐 Live Website
🔗 https://erutherford.uosweb.co.uk/

---

## 💻 GitHub Repository
🔗 https://github.com/Emily-773/BookFinderPHP

---

# 🚀 Features

## 🔐 User Authentication & Account Management
- Secure sign up, login, and logout system using PHP sessions
- Passwords securely hashed using `password_hash()`
- Forgot password and reset password functionality
- Secure password reset emails sent using Brevo (Sendinblue) transactional email API
- Token-based password reset validation with expiry protection

---

## 🔍 Google Books API Integration
- Search for books using the Google Books API
- Displays:
  - Book title
  - Author(s)
  - Publication date
  - Categories
  - Ratings
  - Cover images
  - Descriptions
  - ISBN information
- Dynamic book details page (`book.php`)
- Preview books directly on Google Books
- Placeholder image shown when no book cover is available

---

## 💡 Search Enhancements
- Auto-suggestions while typing
- Search history stored using `localStorage`
- Recently viewed books tracking
- Popular books carousel on homepage
- Responsive search results modal popup

---

## 🎯 Filtering & Sorting
Filter current search results by:
- Author
- Subject/category

Sort results by:
- A–Z (title)
- Newest publication year
- Oldest publication year

Implemented client-side using JavaScript for improved responsiveness.

---

## 📖 My Books (CRUD Functionality)
Users can manage a personal reading collection:

### CRUD Operations
- **Create** — Save books to collection
- **Read** — View saved books
- **Update** — Change reading status
- **Delete** — Remove books from collection

### Reading Status Tracking
- Want to Read
- Reading
- Finished

---

## ⭐ Review System (CRUD)
Users can:
- Add reviews
- Edit reviews
- Delete reviews
- Submit star ratings and written feedback

Reviews include:
- Rating value
- Written review text
- Date and time submitted

Review functionality is integrated into both:
- `my-books.php`
- `book.php`

---

## 🌙 Accessibility & User Experience
- Dark mode toggle
- High contrast mode
- Responsive design for desktop and mobile devices
- Keyboard-accessible navigation
- ARIA labels and semantic HTML
- Accessible form labels and buttons
- Mobile-friendly layouts and controls

---

# 🧠 Database Integration
MySQL database used to store:
- User accounts
- Saved books
- Reviews
- Reading statuses
- Password reset tokens

Security implemented using:
- Prepared statements
- Input sanitisation
- Session management

---

# 🛠️ Technologies Used
- HTML5
- CSS3
- JavaScript (ES6)
- PHP
- MySQL
- Bootstrap 5
- Google Books API
- Brevo API

---

# 🔒 Security & Best Practices
- Password hashing using `password_hash()`
- SQL injection prevention using prepared statements
- Session-based authentication
- Input validation and sanitisation
- HTTPS enforcement via `.htaccess`
- Content Security Policy (CSP) headers
- Secure password reset workflow using email tokens

---

# ♿ Accessibility & Performance
Accessibility features include:
- Semantic HTML structure
- ARIA attributes
- Accessible labels
- Keyboard navigation support
- Responsive layouts
- Dark mode and contrast mode

Performance optimisations include:
- WebP image optimisation
- Responsive image handling
- Deferred JavaScript loading
- Browser caching via `.htaccess`
- Lazy loading strategies for dynamic content

Tested using Google Chrome Lighthouse:
- Accessibility: 95+
- Best Practices: 100
- SEO: 90+
- Performance improvements implemented throughout development

---

# 🧪 Testing
The application has been tested for:
- User authentication
- Password reset functionality
- Google Books API integration
- Search suggestions
- Filtering and sorting
- CRUD operations
- Review system functionality
- Responsive design
- Accessibility compliance
- Error handling and validation

Full testing details are included within the submitted testing documentation.

---

# 📄 Assignment Requirements Covered

This project meets the Advanced Web Development assignment requirements:

✅ Live deployed web application  
✅ Front-end and back-end integration  
✅ User authentication system  
✅ CRUD operations with MySQL database  
✅ Third-party API integration  
✅ Responsive and accessible design  
✅ Enhanced functionality and user experience  
✅ Secure password reset system  
✅ Dynamic API-driven content  
✅ Mobile-responsive layouts  

---

# 📌 Notes
- This project was developed as part of university coursework.
- API keys are restricted and not publicly exposed.
- External data is retrieved dynamically from the Google Books API.

---

# 👩‍💻 Author
Emily Rutherford  
BSc Computer Science – University of Suffolk
