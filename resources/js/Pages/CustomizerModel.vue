<template>
  <div class="coming-wrapper">

    <!-- Top Logo -->
    <div class="top-logo">
      <img :src="assets.logoUrl" alt="Logo" />
    </div>

    <!-- Center Content -->
    <div class="coming-card">
      <h1 class="title"> Coming Soon</h1>

      <p class="subtitle">
        The model customization studio is currently under development.
        <br />
        We are working hard to launch it very soon.
      </p>

      <div class="divider"></div>

      <p class="small-text">
        Stay tuned for an amazing customization experience.
      </p>

      <a :href="routes.modelsIndex" class="back-btn">
        ← Back to Models
      </a>
    </div>

  </div>
</template>

<script>
export default {
  name: "ComingSoonPage",
  props: {
    routes: {
      type: Object,
      default: () => ({ modelsIndex: "/" })
    },
    assets: {
      type: Object,
      default: () => ({ logoUrl: "" })
    }
  }
};
</script>

<style scoped>

.coming-wrapper{
  width:100%;
  height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;

  background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
  font-family:'Poppins',sans-serif;
  color:white;
}

/* logo */
.top-logo{
  position:absolute;
  top:30px;
}

.top-logo img{
  height:50px;
}

/* card */
.coming-card{
  text-align:center;
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(12px);
  padding:60px 80px;
  border-radius:20px;
  box-shadow:0 20px 60px rgba(0,0,0,0.4);
  max-width:600px;
  animation:fadeIn 0.8s ease;
}

/* heading */
.title{
  font-size:52px;
  font-weight:700;
  margin-bottom:20px;
}

/* subtitle */
.subtitle{
  font-size:18px;
  line-height:1.6;
  opacity:0.9;
}

/* divider */
.divider{
  width:80px;
  height:4px;
  background:white;
  margin:30px auto;
  border-radius:10px;
}

/* text */
.small-text{
  opacity:0.8;
  margin-bottom:30px;
}

/* button */
.back-btn{
  display:inline-block;
  padding:12px 26px;
  background:white;
  color:#203a43;
  font-weight:600;
  border-radius:8px;
  text-decoration:none;
  transition:0.3s;
}

.back-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 20px rgba(0,0,0,0.25);
}

/* animation */
@keyframes fadeIn{
  from{
    opacity:0;
    transform:translateY(20px);
  }
  to{
    opacity:1;
    transform:translateY(0);
  }
}

</style>
