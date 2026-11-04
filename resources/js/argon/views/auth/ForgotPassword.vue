<template>
  <auth-layout>
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
            :value="email"
          />
          <validation-error :errors="apiValidationErrors.email" />
        </div>

        <div class="text-center">
          <argon-button
            class="mt-4"
            variant="gradient"
            color="success"
            fullWidth
            size="lg"
          >
            <div v-if="!loading">Send</div>
            <i v-else class="fas fa-spinner fa-pulse"></i>
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
  </auth-layout>
</template>

<script>
import AuthLayout from "./AuthLayout.vue";
import ArgonInput from "@/components/ArgonInput.vue";
import ArgonButton from "@/components/ArgonButton.vue";
import formMixin from "../../mixins/form-mixin.js";
import ValidationError from "../../components/ValidationError.vue";
import showSwal from "../../mixins/showSwal.js";

export default {
  name: "ForgotPassword",
  components: {
    AuthLayout,
    ArgonInput,
    ArgonButton,
    ValidationError,
  },
  mixins: [formMixin, showSwal],
  data() {
    return {
      email: "",
      loading: false,
    };
  },
  methods: {
    async handleForgotPassword() {
      try {
        if (this.$isDemo == 1) {
          this.showSwal({
            type: "error",
            message: "Password forgot is disabled in the demo.",
            width: 350,
          });
        } else {
          this.loading = true;
          this.resetApiValidation();
          await this.$store.dispatch("auth/forgotPassword", {
            email: this.email,
          });
          this.showSwal({
            type: "success",
            message: "Reset password email was sent.",
          });
          this.loading = false;
        }
      } catch (error) {
        this.loading = false;
        this.setApiValidation(error.response?.data?.errors);
        this.showSwal({
          type: "error",
          message: this.firstValidationMessage(error.response?.data?.errors),
        });
      }
    },
  },
};
</script>
