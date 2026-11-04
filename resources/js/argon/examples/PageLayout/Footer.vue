<template>
  <footer class="py-3 footer auth-footer text-center text-nowrap">
    <p class="mb-0 text-sm text-dark">
      © AndreyTepaykin {{ new Date().getFullYear() }}
    </p>
  </footer>
</template>

<script>
export default {
  name: "app-footer",
};
</script>

<style scoped>
@media (max-width: 991.98px) {
  .auth-footer {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
  }
}
</style>
