<template>
  <auth-layout>
    <div class="pb-0 card-header text-start">
      <h4 class="font-weight-bolder">Sign In</h4>
    </div>
    <div class="card-body">
      <form role="form" @submit.prevent="handleLogin()">
        <div class="mb-3">
          <argon-input
            v-model="user.email"
            type="email"
            class="mb-0"
            placeholder="Email"
            name="email"
            size="lg"
            :minlength="$fieldMin.email"
            :maxlength="$fieldMax.email"
            :value="user.email"
          />
          <validation-error :errors="apiValidationErrors.email" />
        </div>
        <div class="mb-3">
          <argon-input
            v-model="user.password"
            type="password"
            placeholder="Password"
            name="password"
            size="lg"
            :maxlength="$fieldMax.password"
            :value="user.password"
          />
        </div>
        <argon-switch id="rememberMe">Remember me</argon-switch>

        <div class="text-center">
          <argon-button
            class="mt-4"
            variant="gradient"
            color="success"
            fullWidth
            size="lg"
          >Sign in</argon-button>
        </div>
      </form>
    </div>
    <div class="px-1 pt-0 text-center card-footer px-lg-2">
      <p class="mx-auto mb-4 text-sm">
        Don't have an account?
        <router-link
          :to="{ name: 'Register' }"
          class="text-success text-gradient font-weight-bold"
        >Sign up</router-link>
      </p>
      <p class="mx-auto mb-4 text-sm">
        <router-link
          :to="{ name: 'Forgot Password' }"
          class="text-dark text-gradient font-weight-bold"
        >Forgot Password?</router-link>
      </p>
    </div>
  </auth-layout>
</template>

<script>
import AuthLayout from "./AuthLayout.vue";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonSwitch from "@/components/ArgonSwitch.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import formMixin from "../../mixins/form-mixin.js";
import ValidationError from "../../components/ValidationError.vue";
import showSwal from "../../mixins/showSwal.js";

export default {
  name: "Login",
  components: {
    AuthLayout,
    ArgonInput,
    ArgonSwitch,
    ArgonButton,
    ValidationError,
  },
  mixins: [formMixin, showSwal],
  data() {
    return {
      user: {
        email: "admin@jsonapi.com",
        password: "secret",
      },
    };
  },
  methods: {
    async handleLogin() {
      try {
        this.resetApiValidation();
        await this.$store.dispatch("auth/login", this.user);
        this.$router.push({ name: "Dashboard" });
      } catch (error) {
        this.setApiValidation(error.response?.data?.errors);
        this.showSwal({
          type: "error",
          message:
            this.firstValidationMessage(error.response?.data?.errors) ||
            "Invalid credentials!",
        });
      }
    },
  },
};
</script>
