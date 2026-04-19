const USERS_KEY = "bookfinder_users";
const CURRENT_USER_KEY = "bookfinder_current_user";

function getUsers() {
  return JSON.parse(localStorage.getItem(USERS_KEY)) || [];
}

function saveUsers(users) {
  localStorage.setItem(USERS_KEY, JSON.stringify(users));
}

function saveCurrentUser(user) {
  localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(user));
}

function getCurrentUser() {
  return JSON.parse(localStorage.getItem(CURRENT_USER_KEY));
}

const signupForm = document.getElementById("signupForm");
const signupMessage = document.getElementById("signupMessage");

if (signupForm) {
  signupForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("signupName").value.trim();
    const email = document.getElementById("signupEmail").value.trim().toLowerCase();
    const password = document.getElementById("signupPassword").value;

    if (!name || !email || !password) {
      signupMessage.textContent = "Please complete all fields.";
      signupMessage.className = "mt-3 text-center text-danger";
      return;
    }

    if (password.length < 6) {
      signupMessage.textContent = "Password must be at least 6 characters.";
      signupMessage.className = "mt-3 text-center text-danger";
      return;
    }

    const users = getUsers();
    const existingUser = users.find((user) => user.email === email);

    if (existingUser) {
      signupMessage.textContent = "An account with this email already exists.";
      signupMessage.className = "mt-3 text-center text-danger";
      return;
    }

    const newUser = { name, email, password };
    users.push(newUser);
    saveUsers(users);
    saveCurrentUser({ name, email });

    signupMessage.textContent = "Account created successfully. Redirecting...";
    signupMessage.className = "mt-3 text-center text-success";

    setTimeout(() => {
      window.location.href = "index.html";
    }, 1000);
  });
}

const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

if (loginForm) {
  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.getElementById("loginEmail").value.trim().toLowerCase();
    const password = document.getElementById("loginPassword").value;

    const users = getUsers();
    const user = users.find((user) => user.email === email && user.password === password);

    if (!user) {
      loginMessage.textContent = "Invalid email or password.";
      loginMessage.className = "mt-3 text-center text-danger";
      return;
    }

    saveCurrentUser({ name: user.name, email: user.email });

    loginMessage.textContent = "Login successful. Redirecting...";
    loginMessage.className = "mt-3 text-center text-success";

    setTimeout(() => {
      window.location.href = "index.html";
    }, 1000);
  });
}
