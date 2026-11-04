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
              backgroundImage: `url('${signinIllustration}')`,
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
                  <div class="pb-0 card-header text-start">
                    <h4 class="font-weight-bolder">Forgot Password</h4>
                    <p class="mb-0">You will receive an e-mail in maximum 60 seconds</p>
                  </div>
                  <div class="card-body">
                    <form role="form" @submit.prevent="handleForgotPassword()">
                      <div class="mb-3">
                        <argon-input
                            v-model="email"
                            type="email"
                            class="mb-0"
                            placeholder="Email"
                            name="email"
                            size="lg"
                            :minlength="$fieldMin.email"
                            :maxlength="$fieldMax.email"
                            :value="email" />
                            <validation-error :errors="apiValidationErrors.email" />
                      </div>
  
                      <div class="text-center">
                        <argon-button
                          class="mt-4"
                          variant="gradient"
                          color="success"
                          fullWidth
                          size="lg"
                        ><div v-if="!loading">Send</div> <i v-else class="fas fa-spinner fa-pulse"></i>
                        </argon-button>
                      </div>
                    </form>
                  </div>
                  <div class="px-1 pt-0 text-center card-footer px-lg-2">
                    <p class="mx-auto mb-4 text-sm">
                      <router-link
                        :to="{ name: 'Login' }"
                        class="text-dark text-gradient font-weight-bold"
                      >Back to login</router-link>
                    </p>
                  </div>
                </div>
                <app-footer />
              </div>
              <div
                class="top-0 my-auto text-center col-6 d-lg-flex d-none h-100 pe-0 position-absolute end-0 justify-content-center flex-column"
              >
                <div
                  class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                  :style="{
                    backgroundImage: `url('${signinIllustration}')`,
                    backgroundSize: 'cover',
                  }"
                >
                  <span class="mask bg-gradient-success opacity-6"></span>
                  <h4
                    class="mt-5 text-white font-weight-bolder position-relative"
                  >"Attention is the new currency"</h4>
                  <p
                    class="text-white position-relative"
                  >The more effortless the writing looks, the more effort the writer actually put into the process.</p>
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
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import formMixin from "../../mixins/form-mixin.js"
import ValidationError from "../../components/ValidationError.vue";
import showSwal from "../../mixins/showSwal.js";
const body = document.getElementsByTagName("body")[0];

export default {
  name: "ForgotPassword",
  components: {
    Navbar,
    AppFooter,
    ArgonInput,
    ArgonButton,
    ValidationError
  },
  mixins: [formMixin, showSwal],
  data() {
    return {
        signinIllustration:
          "https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg",
        email: '',
        loading: false
    }
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
  methods: {
    async handleForgotPassword() {
      try{
        if(this.$isDemo == 1) {
          this.showSwal({
            type: "error",
            message: "Password forgot is disabled in the demo.",
            width: 350
          });
        }
        else{
          this.loading = true;
          this.resetApiValidation();
          await this.$store.dispatch("auth/forgotPassword", { email: this.email });
          this.showSwal({
              type: "success",
              message: "Reset password email was sent."
          });
          this.loading = false;
        }
      }
      catch(error)
      {
        this.loading = false;
        this.setApiValidation(error.response?.data?.errors);
        this.showSwal({
          type:"error",
          message: this.firstValidationMessage(error.response?.data?.errors)
        });
      }
    }
  }
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
