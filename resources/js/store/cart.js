// resources/js/stores/cart.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);

  const loadCart = () => {
    const saved = localStorage.getItem('cart');
    if (saved) items.value = JSON.parse(saved);
  };

  const saveCart = () => {
    localStorage.setItem('cart', JSON.stringify(items.value));
  };

  const addToCart = (product, size, quantity = 1) => {
    const existing = items.value.find(item => item.id === product.id && item.size === size);
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({ ...product, size, quantity });
    }
    saveCart();
  };

  const updateQuantity = (id, size, newQty) => {
    const item = items.value.find(i => i.id === id && i.size === size);
    if (item) {
      if (newQty <= 0) removeItem(id, size);
      else {
        item.quantity = newQty;
        saveCart();
      }
    }
  };

  const removeItem = (id, size) => {
    items.value = items.value.filter(i => !(i.id === id && i.size === size));
    saveCart();
  };

  // ✅ YE NAYA METHOD ADD KIYA – ab clearCart() call kar sakte ho
  const clearCart = () => {
    items.value = [];
    localStorage.removeItem('cart');  // localStorage bhi clear
  };

  const totalItems = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0));

  const totalPrice = computed(() => 
    items.value.reduce((sum, i) => {
      const price = typeof i.price === 'string' 
        ? parseFloat(i.price.replace(/[^0-9.]/g, '')) || 0 
        : Number(i.price) || 0;
      return sum + (price * i.quantity);
    }, 0)
  );

  const likedProducts = ref(new Set());
  const toggleLike = (productId) => {
    if (likedProducts.value.has(productId)) likedProducts.value.delete(productId);
    else likedProducts.value.add(productId);
  };
  const isLiked = (id) => likedProducts.value.has(id);

  loadCart();

  return {
    items,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart,          // ✅ YE RETURN KARNA ZAROORI HAI
    totalItems,
    totalPrice,
    toggleLike,
    isLiked
  };
});