<template>
  <div class="coming-soon-page">
    <div class="coming-soon-box">
      <h1>🚧 Coming Soon</h1>
      <p>
        This customization feature is currently under development.
        <br />
        It will be available very soon.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ComingSoon"
};
</script>

<style scoped>
.coming-soon-page{
  width:100%;
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#f5f5f5;
  font-family:'Poppins',sans-serif;
}

.coming-soon-box{
  text-align:center;
  background:white;
  padding:60px 80px;
  border-radius:16px;
  box-shadow:0 10px 30px rgba(0,0,0,0.15);
}

.coming-soon-box h1{
  font-size:48px;
  margin-bottom:20px;
  color:#111;
}

.coming-soon-box p{
  font-size:18px;
  color:#666;
}
</style>
