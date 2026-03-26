<template>
  <div>

    <h2 style="font-weight:700;margin-bottom:20px;">
      My Orders
    </h2>

    <div v-if="isLoading">
      Loading orders...
    </div>

    <div v-else-if="!orders.length">
      No orders found
    </div>

    <table v-else class="orders-table">

      <thead>
        <tr>
          <th>Order ID</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>

      <tbody>

        <tr v-for="order in orders" :key="order.id">

          <td>#{{ order.id }}</td>

          <td>${{ order.total }}</td>

          <td>
            {{ order.status }}
          </td>

          <td>
            {{ formatDate(order.created_at) }}
          </td>

        </tr>

      </tbody>

    </table>

  </div>
</template>


<script setup>

defineProps({
  orders: Array,
  isLoading: Boolean
})

const formatDate = (date) => {

  return new Date(date).toLocaleDateString()

}

</script>


<style scoped>

.orders-table {

  width:100%;
  border-collapse:collapse;

}

.orders-table th {

  text-align:left;
  padding:10px;
  background:#f4f4f4;

}

.orders-table td {

  padding:10px;
  border-bottom:1px solid #eee;

}

</style>
