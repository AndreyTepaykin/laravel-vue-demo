<template>
  <div class="container top-0 position-sticky z-index-sticky">
    <div class="row">
      <div class="col-12">
        <navbar
          isBlur="login-nav border-radius-lg my-3 py-2 start-0 end-0 mx-4 shadow"
          v-bind:darkMode="true"
          isBtn="bg-gradient-success"
        />
      </div>
    </div>
  </div>
  <main class="mt-0 main-content login-page">
    <section>
      <div class="page-header min-vh-100">
        <div
          class="login-illustration min-vh-100 d-lg-none overflow-hidden"
          :style="{
            backgroundImage: `url('${illustration}')`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
          }"
        >
          <span class="mask bg-gradient-success opacity-6"></span>
        </div>
        <div class="container px-0">
          <div class="row">
            <div class="auth-form-col mx-auto col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 position-relative">
              <div class="card shadow-lg mt-n6 mt-lg-0 position-relative z-index-2">
                <slot />
              </div>
              <app-footer />
            </div>
            <div
              class="top-0 my-auto text-center col-6 d-lg-flex d-none h-100 pe-0 position-absolute end-0 justify-content-center flex-column"
            >
              <div
                class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                :style="{
                  backgroundImage: `url('${illustration}')`,
                  backgroundSize: 'cover',
                }"
              >
                <span class="mask bg-gradient-success opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">
                  "Attention is the new currency"
                </h4>
                <p class="text-white position-relative">
                  The more effortless the writing looks, the more effort the writer actually put into the process.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script>
import Navbar from "@/examples/PageLayout/Navbar.vue";
import AppFooter from "@/examples/PageLayout/Footer.vue";

const body = document.getElementsByTagName("body")[0];

export default {
  name: "AuthLayout",
  components: {
    Navbar,
    AppFooter,
  },
  data() {
    return {
      illustration:
        "https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg",
    };
  },
  created() {
    this.$store.state.hideConfigButton = true;
    this.$store.state.showNavbar = false;
    this.$store.state.showSidenav = false;
    this.$store.state.showFooter = false;
    body.classList.remove("bg-gray-100");
  },
  beforeUnmount() {
    this.$store.state.hideConfigButton = false;
    this.$store.state.showNavbar = true;
    this.$store.state.showSidenav = true;
    this.$store.state.showFooter = true;
    body.classList.add("bg-gray-100");
  },
};
</script>

<style scoped>
:deep(.login-nav) {
  background-color: rgba(255, 255, 255, 0.6);
}

@media (max-width: 991.98px) {
  .login-illustration {
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
  }

  .page-header {
    align-items: end;
  }

  .page-header > .container {
    margin-top: 280px;
  }

  .card {
    border-radius: 0;
    background-color: rgba(255, 255, 255, 0.6);
    padding-bottom: 2.5rem;
  }

  .card-header {
    background-color: transparent;
  }
}
</style>
