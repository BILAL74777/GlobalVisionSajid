<template>
  <div class="main">
    <!-- Header -->
    <header id="header" class="app-header fixed-top d-flex align-items-center">
      <div class="d-flex align-items-center justify-content-between w-100">
        <!-- Left: Logo + Toggle -->
        <div class="d-flex align-items-center gap-3">
          <button class="icon-btn d-lg-none" type="button">
            <i class="bi bi-list toggle-sidebar-btn"></i>
          </button>

          <a :href="route('dashboard')" class="logo d-flex align-items-center gap-2">
            <!-- If you have a logo image, uncomment -->
            <!-- <img src="/backend/assets/img/logo_black.png" alt="logo" class="logo-img" /> -->
            <span class="logo-text">Global Vision</span>
          </a>
        </div>

        <!-- Right: User -->
        <nav class="header-nav ms-auto">
          <ul class="d-flex align-items-center m-0">
            <li class="nav-item dropdown pe-2">
              <a
                class="nav-link nav-profile d-flex align-items-center gap-2 pe-0 dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown"
              >
                <span class="avatar-circle">
                  <i class="bi bi-person"></i>
                </span>
                <span class="user-name">{{ $page.props.user.name }}</span>
              </a>

              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-title px-3 pt-2 pb-1">
                  <small class="text-muted">Signed in as</small>
                  <div class="fw-semibold">{{ $page.props.user.name }}</div>
                </li>
                <li><hr class="dropdown-divider my-2" /></li>

                <li>
                  <a class="dropdown-item d-flex align-items-center gap-2" href="/profile">
                    <i class="bi bi-gear"></i>
                    <span>Account Settings</span>
                  </a>
                </li>

                <li><hr class="dropdown-divider my-2" /></li>

                <li>
                  <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:;" @click="logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </header>
    <!-- End Header -->

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
      <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
          <a
            class="nav-link"
            :class="{ active: isActive('dashboard') }"
            :href="route('dashboard')"
          >
            <i class="bi bi-house-door-fill"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <!-- Pak Visa (Super Admin & Employee) -->
        <li
          v-if="$page.props.user.role === 'super admin' || $page.props.user.role === 'employee'"
          class="nav-item"
        >
          <a
            class="nav-link with-caret"
            data-bs-target="#icons-nav"
            data-bs-toggle="collapse"
            href="#"
            :class="{ active: isParentActive(['visa.individual', 'visa.family']) }"
          >
            <i class="bi bi-building"></i>
            <span>Pak Visa</span>
            <i class="bi bi-chevron-down ms-auto caret"></i>
          </a>
          <ul
            id="icons-nav"
            class="nav-content collapse"
            data-bs-parent="#sidebar-nav"
            :class="{ show: isParentActive(['visa.individual', 'visa.family']) }"
          >
            <li>
              <a :href="route('visa.individual')" :class="{ active: isActive('visa.individual') }">
                <span>Individual</span>
              </a>
            </li>
            <li>
              <a :href="route('visa.family')" :class="{ active: isActive('visa.family') }">
                <span>Family</span>
              </a>
            </li>
          </ul>
        </li>

        <!-- Employees (Super Admin) -->
        <li v-if="$page.props.user.role === 'super admin'" class="nav-item">
          <a class="nav-link" :href="route('employees')" :class="{ active: isActive('employees') }">
            <i class="bi bi-people"></i>
            <span>Employees</span>
          </a>
        </li>

        <!-- Referrals (Super Admin) -->
        <li v-if="$page.props.user.role === 'super admin'" class="nav-item">
          <a class="nav-link" :href="route('referrals')" :class="{ active: isActive('referrals') }">
            <i class="bi bi-person-lines-fill"></i>
            <span>Referrals</span>
          </a>
        </li>

        <!-- Software Users (Super Admin) -->
        <li v-if="$page.props.user.role === 'super admin'" class="nav-item">
          <a class="nav-link" :href="route('users')" :class="{ active: isActive('users') }">
            <i class="bi bi-person-badge-fill"></i>
            <span>Software Users</span>
          </a>
        </li>

        <!-- All Gmails (Super Admin & Employee) -->
        <li v-if="$page.props.user.role === 'super admin' || $page.props.user.role === 'employee'" class="nav-item">
          <a class="nav-link" :href="route('gmails')" :class="{ active: isActive('gmails') }">
            <i class="bi bi-envelope"></i>
            <span>All Gmails</span>
          </a>
        </li>
      </ul>
    </aside>
    <!-- End Sidebar -->

    <!-- Content -->
    <slot></slot>

    <!-- Footer -->
    <footer id="footer" class="footer">
      <div class="copyright">
        &copy; <strong>Global Vision</strong>. All rights reserved
      </div>
      <div class="credits">
        Developed by <a href="https://goritmi.co.uk/">Goritmi</a>
      </div>
    </footer>
    <!-- End Footer -->
  </div>
</template>

<script>
import axios from "axios";

export default {
  mounted() {
    // (Keep your bootstrap + main script injection)
    const bootstrapJs = document.createElement("script");
    bootstrapJs.src = "/backend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js";
    document.head.appendChild(bootstrapJs);

    const mainJs = document.createElement("script");
    mainJs.src = "/backend/assets/js/main.js";
    document.head.appendChild(mainJs);
  },

  methods: {
    logout() {
      axios
        .post(route("api.logout"), this.form, {
          headers: { Authorization: "Bearer " + this.$page.props.auth_token },
        })
        .then(() => {
          this.$inertia.get(route("login"));
        })
        .catch(() => {});
    },

    isParentActive(routeNames) {
      return routeNames.some((name) => this.isActive(name));
    },

    isActive(routeName) {
      const currentPath = window.location.pathname;
      const routePath = new URL(this.route(routeName), window.location.origin).pathname;
      return currentPath === routePath || currentPath.startsWith(routePath);
    },
  },
};
</script>

<style>
/* vendor bundles (keep) */
@import url("public/backend/assets/vendor/bootstrap/css/bootstrap.min.css");
@import url("public/backend/assets/vendor/bootstrap-icons/bootstrap-icons.css");
@import url("public/backend/assets/vendor/boxicons/css/boxicons.min.css");
@import url("public/backend/assets/vendor/remixicon/remixicon.css");
@import url("public/backend/assets/vendor/simple-datatables/style.css");
@import url("public/backend/assets/css/style.css");
@import url("public/backend/assets/css/custom.css");
@import "toastr";
@import "@vueform/multiselect/themes/default.css";
</style>

<style>
/* ===== Brand tokens ===== */
:root{
  --brand:#1C0D82;           /* primary */
  --brand-ink:#15104e;       /* darker text */
  --brand-soft:rgba(28,13,130,.12);
  --bg-soft:#f6f7fb;
  --card:#ffffff;
  --shadow:0 10px 30px rgba(16,24,40,.08);
}

/* ===== Header ===== */
.app-header{
  background: linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.85));
  backdrop-filter: saturate(160%) blur(6px);
  border-bottom: 1px solid var(--brand-soft);
  padding: .6rem .9rem;
  box-shadow: var(--shadow);
}
.icon-btn{
  border: 1px solid var(--brand-soft);
  background: var(--card);
  width: 42px; height: 42px;
  border-radius: 12px;
  display: grid; place-items:center;
  color: var(--brand-ink);
}
.logo-text{
  font-weight: 800;
  letter-spacing: .2px;
  color: var(--brand-ink);
}
.logo-img{ height: 36px; }

/* Avatar */
.avatar-circle{
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--brand);
  display: grid; place-items:center;
  color: #fff;
}
.user-name{ color: var(--brand-ink); font-weight: 600; }

/* Dropdown polish */
.dropdown-menu.profile{
  border-radius: 14px;
  border: 1px solid var(--brand-soft);
  box-shadow: var(--shadow);
}
.dropdown-menu.profile .dropdown-item{
  padding: .55rem .9rem;
  border-radius: 10px;
}
.dropdown-menu.profile .dropdown-item:hover{
  background: rgba(28,13,130,.08);
  color: var(--brand-ink);
}
.dropdown-title{ line-height: 1.1; }

/* ===== Sidebar ===== */
.sidebar{
  background: var(--card);
  border-right: 1px solid var(--brand-soft);
  /* padding-top: 76px; under fixed header */
}
.sidebar-nav{
  padding: .75rem .75rem 1.25rem;
}
.sidebar-nav .nav-item{ margin-bottom: .25rem; }
.sidebar-nav .nav-link{
  display: flex; align-items: center; gap: .6rem;
  padding: .7rem .75rem;
  border-radius: 12px;
  color: var(--brand-ink);
  background: #fff;
  border: 1px solid transparent;
  transition: .15s ease;
  font-weight: 600;
}
.sidebar-nav .nav-link i{ color: var(--brand-ink); }
.sidebar-nav .nav-link:hover{
  border-color: var(--brand-soft);
  background: #fff;
  transform: translateY(-1px);
  box-shadow: var(--shadow);
}
.sidebar-nav .nav-link.active{
  background: rgba(28,13,130,.08);
  border-color: rgba(28,13,130,.22);
  color: var(--brand-ink);
}
.sidebar-nav .nav-link.active i{ color: var(--brand); }

.sidebar-nav .nav-link.with-caret .caret{
  transition: transform .18s ease;
}
.sidebar-nav .nav-link.active .caret{
  transform: rotate(180deg);
}

.sidebar-nav .nav-content{
  padding-left: 2.25rem;
  border-left: 2px dashed var(--brand-soft);
  margin: .35rem 0 .65rem .45rem;
}
.sidebar-nav .nav-content a{
  display: block;
  padding: .45rem .4rem;
  border-radius: 10px;
  color: var(--brand-ink);
  font-weight: 500;
}
.sidebar-nav .nav-content a:hover{
  background: rgba(28,13,130,.06);
}
.sidebar-nav .nav-content a.active{
  background: rgba(28,13,130,.12);
  color: var(--brand-ink);
  font-weight: 700;
}

/* ===== Footer ===== */
.footer{
  border-top: 1px solid var(--brand-soft);
  background: var(--card);
  color: var(--brand-ink);
}

/* ===== Theme utilities replacing older rules ===== */
.theme-text-color{ color: var(--brand-ink) !important; }
.theme-bg-color{ background-color: var(--brand) !important; }

/* Multiselect tune */
.multiselect{
  --ms-border-color: var(--brand-soft);
  --ms-ring-color: rgba(28,13,130,.25);
  --ms-radius: 12px;
  --ms-option-bg-selected: var(--brand);
  --ms-option-color-selected: #fff;
  --ms-tag-bg: var(--brand);
  --ms-tag-color: #fff;
}

/* Remove conflicting/duplicate old rules */
.sidebar-nav .nav-link.active { font-weight: 700; }
</style>
