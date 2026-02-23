import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCheckoutStore = defineStore('checkout', () => {
  const currentStep = ref(1) // 1: User Info, 2: Address, 3: Payment

  const form = ref({
    name: '',
    age: null,
    phone: '',
    deliveryDays: '3-5', // default
    address: '',
    city: '',
    province: 'Punjab', // default Pakistan
    postalCode: '',
    paymentMethod: 'cod' // 'cod', 'easypaisa', 'jazzcash', 'card'
  })

  const resetForm = () => {
    form.value = {
      name: '',
      age: null,
      phone: '',
      deliveryDays: '3-5',
      address: '',
      city: '',
      province: 'Punjab',
      postalCode: '',
      paymentMethod: 'cod'
    }
    currentStep.value = 1
  }

  const goToNextStep = () => {
    if (currentStep.value < 3) currentStep.value++
  }

  const goToPrevStep = () => {
    if (currentStep.value > 1) currentStep.value--
  }

  return {
    currentStep,
    form,
    resetForm,
    goToNextStep,
    goToPrevStep
  }
})