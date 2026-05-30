# 📚 BookFinder (PHP Version)

BookFinder is a dynamic web application developed for the Advanced Web Development module at the University of Suffolk. It allows users to search for books using the Google Books API, view detailed book information and descriptions, save books to a personal library, track reading progress, and write reviews.

## 🌐 Live Website

🔗 https://erutherford.uosweb.co.uk/

## 💻 GitHub Repository

🔗 https://github.com/Emily-773/BookFinderPHP

---

# 🚀 Features

## 🔐 User Authentication

* User registration and login system
* Secure PHP session management
* Password hashing using `password_hash()`
* Password reset functionality using Brevo email integration
* Logout functionality

## 🔍 Google Books API Search

* Search books by title, author, or subject
* Real-time search suggestions
* Search history storage
* Filter and sort search results
* Detailed book information displayed in a pop-up modal
* Book descriptions retrieved from the Google Books API
* Google Books preview links

## 📚 Personal Library

* Save books to a personal collection
* View saved books
* Delete books from the library

### Reading Status Tracking

* Want to Read
* Currently Reading
* Completed

## ⭐ Reviews and Ratings

* Add personal reviews
* Edit reviews
* Delete reviews
* Average rating calculations
* Review display on the Book Details page

## 📖 Book Details Page

Displays:

* Cover image
* Title
* Author
* Publisher
* Published date
* Categories
* ISBN-10
* ISBN-13
* Page count
* Language
* Full book description retrieved from the Google Books API
* Average rating
* Personal review information
* Reading status
* Google Books preview link

## 🎨 User Experience Features

* Dark Mode
* High Contrast Mode
* Responsive Design
* Accessible navigation
* Keyboard-friendly controls
* Search history
* Recommended books section
* Fallback image for unavailable book covers

## ⚡ Performance Optimisation

* Minified CSS
* Optimised images
* Browser caching
* Content Security Policy (CSP)
* HTTPS redirection
* Lighthouse optimisation

---

# 🛡 Security Features

* Prepared SQL statements
* Password hashing
* Session-based authentication
* HTTPS enforcement
* Content Security Policy (CSP)
* Input sanitisation and validation
* Protection against SQL injection

---

# 🛠 Technologies Used

## Front End

* HTML5
* CSS3
* JavaScript

## Back End

* PHP

## Database

* MySQL

## APIs

* Google Books API
* Brevo Email API

---

# 📊 Lighthouse Scores

Typical Lighthouse scores achieved after optimisation:

| Category       | Score  |
| -------------- | ------ |
| Performance    | 99–100 |
| Accessibility  | 100    |
| Best Practices | 92–96  |
| SEO            | 100    |

---

# 📂 Project Structure

```text
BookFinderPHP/

├── css/
│   ├── styles.css
│   └── styles.min.css

├── js/
│   ├── script.js
│   ├── auth.js
│   └── book-details.js

├── images/

├── includes/

├── index.php
├── search.php
├── my-books.php
├── book.php
├── login.php
├── signup.php
├── forgot-password.php
├── reset-password.php

└── README.md
```

---

# 🎓 Academic Context

This project was developed as part of the **Advanced Web Development** module for the **BSc Computer Science** programme at the **University of Suffolk**.

The application demonstrates:

* API integration
* Database-driven development
* User authentication
* CRUD operations
* Responsive web design
* Accessibility best practices
* Web security implementation
* Performance optimisation

---

# 👩‍💻 Author

**Emily Rutherford**

BSc Computer Science

University of Suffolk
