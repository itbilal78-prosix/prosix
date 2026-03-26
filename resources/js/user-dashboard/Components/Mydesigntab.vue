<template>
  <div class="my-design-wrapper">

    <h2 class="title">
      My Design
    </h2>

    <div class="coming-soon-box">


      <h3>
        Coming Soon
      </h3>

      <p>
        Your saved designs will appear here.
      </p>

    </div>

  </div>
</template>

<script setup>
</script>

<style scoped>

.my-design-wrapper {
  padding: 20px;
}

.title {
  font-weight: 700;
  margin-bottom: 20px;
}

.coming-soon-box {
  height: 250px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  border: 1px dashed #ddd;
  border-radius: 12px;

  background: #fafafa;
}

.icon {
  font-size: 50px;
  margin-bottom: 10px;
}

h3 {
  font-weight: 700;
}

p {
  color: #888;
}

</style>
