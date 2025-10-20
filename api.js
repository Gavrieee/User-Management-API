// GLOBAL CONFIG
const apiUrl = "api.php";

function escapeHtml(s) {
  return (s + "").replace(/[&<>"']/g, function (m) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    }[m];
  });
}

// REGISTER PAGE HANDLER
function handleRegisterPage() {
  const registerForm = document.getElementById("registerForm");
  if (!registerForm) return; // only run if we’re on register.php

  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const firstname = document.getElementById("firstname").value.trim();
    const lastname = document.getElementById("lastname").value.trim();
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;
    const is_admin = document.getElementById("is_admin")?.checked ? 1 : 0;

    // === Basic client-side validation ===
    if (!username || !firstname || !lastname || !password || !confirm) {
      Swal.fire({
        icon: "warning",
        title: "Empty field",
        text: "All fields are required.",
      });
      return;
    }
    if (password.length < 8) {
      Swal.fire({
        icon: "warning",
        title: "Weak password",
        text: "Password must be at least 8 characters.",
      });
      return;
    }
    if (password !== confirm) {
      Swal.fire({
        icon: "warning",
        title: "Mismatch",
        text: "Passwords do not match.",
      });
      return;
    }

    // === Check if username exists ===
    try {
      const r = await fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "check_username", username }),
      });
      const data = await r.json();
      if (data.status === "exists") {
        Swal.fire({
          icon: "error",
          title: "This username is taken!",
          text: "Please choose another username.",
        });
        return;
      }
    } catch (err) {
      console.error("Username check failed:", err);
    }

    // === Register account ===
    try {
      const resp = await fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "register",
          username,
          firstname,
          lastname,
          password,
          confirm_password: confirm,
          is_admin,
        }),
      });
      const json = await resp.json();

      if (json.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Registered",
          text: json.message,
        }).then(() => (window.location = "login.php"));
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: json.message,
        });
      }
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Request failed.",
      });
      console.error(err);
    }
  });
}

// LOGIN PAGE HANDLER
function handleLoginPage() {
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return; // will only run if on login.php

  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;

    if (!username || !password) {
      Swal.fire({
        icon: "warning",
        title: "Empty field",
        text: "Please enter username and password.",
      });
      return;
    }

    try {
      const resp = await fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "login",
          username,
          password,
        }),
      });
      const data = await resp.json();

      if (data.status === "success") {
        window.location = "index.php";
      } else {
        Swal.fire({
          icon: "error",
          title: "Login failed",
          text: data.message,
        });
      }
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Request failed.",
      });
      console.error(err);
    }
  });
}

// INDEX PAGE HANDLER (LOGOUT)
function handleIndexPage() {
  const logoutBtn = document.getElementById("logoutBtn");
  if (!logoutBtn) return; // only run if on index.php

  logoutBtn.addEventListener("click", async function () {
    try {
      const resp = await fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "logout" }),
      });
      const data = await resp.json();

      if (data.status === "success") {
        window.location = "login.php";
      } else {
        Swal.fire({
          icon: "error",
          title: "Logout failed",
          text: data.message,
        });
      }
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Logout request failed.",
      });
      console.error(err);
    }
  });
}

function handleAllUsersPage() {
  const search = document.getElementById("search");
  const form = document.getElementById("addUserForm");
  const tbody = document.getElementById("usersBody");

  // Will only run if these elements exist on all_users.php
  if (!search || !form || !tbody) return;

  // Load and display users
  async function loadUsers(q = "") {
    const action = q ? "search_users" : "get_users";
    const body = q ? { action, q } : { action };
    const resp = await fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body),
    });
    const json = await resp.json();
    if (json.status === "success") {
      tbody.innerHTML = "";
      json.users.forEach((u) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${u.id}</td>
          <td>${escapeHtml(u.username)}</td>
          <td>${escapeHtml(u.firstname)}</td>
          <td>${escapeHtml(u.lastname)}</td>
          <td>${u.is_admin == 1 ? "Yes" : "No"}</td>
          <td>${u.date_added}</td>
        `;
        tbody.appendChild(tr);
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: json.message || "Failed to load users",
      });
    }
  }

  // Search handler
  search.addEventListener("input", (e) => {
    const q = e.target.value.trim();
    loadUsers(q);
  });

  // Add user handler
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("add_username").value.trim();
    const firstname = document.getElementById("add_firstname").value.trim();
    const lastname = document.getElementById("add_lastname").value.trim();
    const password = document.getElementById("add_password").value;
    const confirm = document.getElementById("add_confirm_password").value;
    const is_admin = document.getElementById("add_is_admin").checked ? 1 : 0;

    if (!username || !firstname || !lastname || !password || !confirm) {
      Swal.fire({
        icon: "warning",
        title: "Empty",
        text: "All fields are required.",
      });
      return;
    }
    if (password.length < 8) {
      Swal.fire({
        icon: "warning",
        title: "Weak",
        text: "Password must be at least 8 characters.",
      });
      return;
    }
    if (password !== confirm) {
      Swal.fire({
        icon: "warning",
        title: "Mismatch",
        text: "Passwords do not match.",
      });
      return;
    }

    // check if username exists
    const ch = await fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "check_username", username }),
    });
    const chj = await ch.json();
    if (chj.status === "exists") {
      Swal.fire({
        icon: "error",
        title: "Username taken",
        text: "Choose a different username.",
      });
      return;
    }

    // add user
    const resp = await fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        action: "add_user",
        username,
        firstname,
        lastname,
        password,
        confirm_password: confirm,
        is_admin,
      }),
    });
    const json = await resp.json();

    if (json.status === "success") {
      Swal.fire({ icon: "success", title: "Added", text: json.message });
      const modalEl = document.getElementById("addUserModal");
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal.hide();
      form.reset();
      loadUsers();
    } else {
      Swal.fire({ icon: "error", title: "Error", text: json.message });
    }
  });

  loadUsers();
}

// INITIALIZE ALL PAGES !!!

window.addEventListener("DOMContentLoaded", () => {
  handleRegisterPage();
  handleLoginPage();
  handleIndexPage();
  handleAllUsersPage();
});
