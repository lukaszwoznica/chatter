<template>
  <div class="app-wrapper">
    <AppHeader/>
    <main class="main">
      <router-view/>
    </main>
    <AppFooter/>
  </div>
</template>

<script>
import AppFooter from "./components/layout/AppFooter";
import AppHeader from "./components/layout/AppHeader";

export default {
  name: "App",

  components: {
    AppHeader,
    AppFooter
  },

}
</script>
