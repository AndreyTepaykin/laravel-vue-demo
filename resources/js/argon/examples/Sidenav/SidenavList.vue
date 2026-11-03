<template>
  <div
    class="collapse navbar-collapse w-auto h-auto h-100"
    id="sidenav-collapse-main"
  >
    <ul class="navbar-nav">
      <li class="nav-item">
        <sidenav-item
          url="/dashboard-default"
          :class="getRoute() === 'dashboard-default' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'لوحة القيادة' : 'Dashboard'"
        >
          <template v-slot:icon>
            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>

      <li class="nav-item">
        <div class="nav-link cursor-pointer" @click.prevent="useDropDown()">
          <div
            class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center"
          >
            <i class="fab fa-vuejs text-info text-sm opacity-10"></i>
          </div>
          Examples (API)
          <i v-if="!dropDownStatus" class="ms-3 fas fa-angle-right"></i>
          <i v-else class="ms-3 fas fa-angle-down"></i>
        </div>
        <ul v-if="dropDownStatus">
          <sidenav-item
            url="/examples/user-profile"
            :class="getRoute() === 'examples/user-profile' ? 'active' : ''"
            navText="User Profile"
          >
            <template v-slot:icon>
              <i class="fas fa-user-circle text-dark text-sm opacity-10"></i>
            </template>
          </sidenav-item>
          <sidenav-item
            v-if="isAdmin"
            url="/examples/user-management/list-users"
            :class="getRoute() === 'examples' ? 'active' : ''"
            navText="User Management"
          >
            <template v-slot:icon>
              <i class="fas fa-user-edit text-dark text-sm opacity-10"></i>
            </template>
          </sidenav-item>
        </ul>
      </li>

      <li class="nav-item">
        <sidenav-item
          url="/tables"
          :class="getRoute() === 'tables' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'الجداول' : 'Tables'"
        >
          <template v-slot:icon>
            <i
              class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"
            ></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/billing"
          :class="getRoute() === 'billing' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'الفواتیر' : 'Billing'"
        >
          <template v-slot:icon>
            <i class="ni ni-credit-card text-success text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/virtual-reality"
          :class="getRoute() === 'virtual-reality' ? 'active' : ''"
          :navText="
            this.$store.state.isRTL ? 'الواقع الافتراضي' : 'Virtual Reality'
          "
        >
          <template v-slot:icon>
            <i class="ni ni-app text-info text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li class="mt-3 nav-item">
        <h6
          v-if="this.$store.state.isRTL"
          class="text-xs ps-4 text-uppercase font-weight-bolder opacity-6"
          :class="this.$store.state.isRTL ? 'me-4' : 'ms-2'"
        >
          صفحات المرافق
        </h6>
        <h6
          v-else
          class="text-xs ps-4 text-uppercase font-weight-bolder opacity-6"
          :class="this.$store.state.isRTL ? 'me-4' : 'ms-2'"
        >
          ACCOUNT PAGES
        </h6>
      </li>
      <li class="nav-item">
        <sidenav-item
          url="/profile"
          :class="getRoute() === 'profile' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'حساب تعريفي' : 'Profile'"
        >
          <template v-slot:icon>
            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li v-if="!loggedIn" class="nav-item">
        <sidenav-item
          url="/signin"
          :class="getRoute() === 'signin' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'تسجيل الدخول' : 'Sign In'"
        >
          <template v-slot:icon>
            <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li v-if="!loggedIn" class="nav-item">
        <sidenav-item
          url="/signup"
          :class="getRoute() === 'signup' ? 'active' : ''"
          :navText="this.$store.state.isRTL ? 'اشتراك' : 'Sign Up'"
        >
          <template v-slot:icon>
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>
      <li v-if="loggedIn" class="nav-item">
        <a href="#" class="nav-link cursor-pointer" @click.prevent="handleLogout">
          <div
            class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center"
          >
            <i class="ni ni-user-run text-danger text-sm opacity-10"></i>
          </div>
          <span
            class="nav-link-text"
            :class="this.$store.state.isRTL ? ' me-1' : 'ms-1'"
            >{{ this.$store.state.isRTL ? 'تسجيل الخروج' : 'Logout' }}</span
          >
        </a>
      </li>
    </ul>
  </div>
</template>
<script>
import SidenavItem from "./SidenavItem.vue";

export default {
  name: "SidenavList",
  data() {
    return {
      title: "AndreyTepaykin test app",
      controls: "dashboardsExamples",
      isActive: "active",
      dropDownStatus: true,
    };
  },
  components: {
    SidenavItem,
  },
  computed: {
    loggedIn() {
      return this.$store.getters["auth/isAuthenticated"];
    },
    isAdmin() {
      const roles = this.$store.getters["profile/me"]?.roles || [];

      return roles.includes("admin");
    },
  },
  mounted() {},
  methods: {
    getRoute() {
      const routeArr = this.$route.path.split("/");
      return routeArr[1];
    },
    useDropDown() {
      this.dropDownStatus = !this.dropDownStatus;
    },
    async handleLogout() {
      try {
        await this.$store.dispatch("auth/logout");
      } finally {
        await this.$router.push({ name: "Login" });
      }
    },
  },
};
</script>
