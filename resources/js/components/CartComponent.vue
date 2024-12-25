<template>
    <div class="cart">
        <div class="products">
            <h2>Товари</h2>
            <div v-if="loadingProducts">Завантаження...</div>

            <product-component
                v-else
                v-for="product in products"
                :key="product.id"
                :product="product"
                @add-to-cart="addToCart"
            ></product-component>

            <div v-if="errorLoadingProducts" class="message error">
                Помилка: {{ errorLoadingProducts }}
            </div>
        </div>

        <div class="cart-details">
            <h2>Корзина</h2>

            <div v-if="cart.length > 0" class="cart-container">
                <ul class="cart-list">
                    <li
                        class="cart-item"
                        v-for="item in cart"
                        :key="item.product.id"
                        :class="{'added': item.addedToCart}"
                    >
                        <div class="cart-item-details">
                            <span class="cart-item-name">{{ item.product.name }}</span>
                            <span class="cart-item-price">{{ item.product.price }} ₴</span>
                            <span class="cart-item-quantity">x{{ item.quantity }}</span>
                        </div>
                        <button class="remove-product-from-cart-btn" @click="removeFromCart(item.product)">Видалити</button>
                    </li>
                </ul>
                <div class="cart-summary">
                    <p>Загальна сума: <strong>{{ total }} ₴</strong></p>
                    <button class="create-order-btn" @click="placeOrder" :disabled="placeOrderRequestSent">
                        <span v-if="placeOrderRequestSent">Завантаження...</span>
                        <span v-else>Створити замовлення</span>
                    </button>
                </div>
            </div>
            <p v-else>Корзина порожня</p>

            <div v-if="orderCreationError.message" class="message error">
                <div>Помилка: {{ orderCreationError.message }}</div>
                <div v-if="Object.keys(orderCreationError.errorList).length">
                    <ul>
                        <li v-for="(errors, fieldName) in orderCreationError.errorList" :key="fieldName">
                            <ul>
                                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div v-if="orderCreationSuccess" class="message success">
                {{ orderCreationSuccess }}
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import ProductComponent from "@/components/ProductComponent.vue";

export default {
    components: {ProductComponent},
    mounted() {
        this.fetchProducts();
    },
    data() {
        return {
            loadingProducts: true,
            errorLoadingProducts: null,
            products: [],
            cart: [],
            orderCreationError: {
                message: null,
                errorList: [],
            },
            orderCreationSuccess: null,
            placeOrderRequestSent: false,
        };
    },
    computed: {
        total() {
            return this.cart.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
        },
    },
    methods: {
        async fetchProducts() {
            await axios
                .get('/products').then(response => {
                    if (response.data.ok) {
                        this.products = response.data.data || [];
                        this.loadingProducts = false;
                        this.errorLoadingProducts = null;
                        return;
                    }

                    this.errorLoadingProducts = response.data.message;
                }).catch(error => {
                    this.errorLoadingProducts = error.message;
                });
        },
        addToCart(product) {
            this.orderCreationSuccess = null;
            const existingItem = this.cart.find((item) => item.product.id === product.id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.cart.push({ product, quantity: 1, addedToCart: true });
                setTimeout(() => {
                    const item = this.cart.find(item => item.product.id === product.id);
                    item.addedToCart = false;
                }, 50);
            }
        },
        removeFromCart(product) {
            const index = this.cart.findIndex((item) => item.product.id === product.id);
            if (index > -1) {
                const item = this.cart[index];
                if (item.quantity > 1) {
                    item.quantity--;
                } else {
                    this.cart.splice(index, 1);
                }
            }
        },
        async placeOrder() {
            this.placeOrderRequestSent = true;
            const order = {
                items: this.cart.map((item) => ({
                    product_id: item.product.id,
                    quantity: item.quantity,
                }))
            };
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            await axios.post('/createOrder', order, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                if (response.data.ok) {
                    this.cart = [];
                    this.flushErrors();
                    this.orderCreationSuccess = "Замовлення створено на суму: " + response.data.total + " ₴";
                } else {
                    this.orderCreationError.message = response.data.message || 'Error without message.';
                    this.orderCreationSuccess = null;
                }
            }).catch(error => {
                console.log('Failed: ', error);
                this.orderCreationSuccess = null;

                if (error.response) {
                    this.orderCreationError.message = error.response.data.message || error.message || 'No error message.';
                    if (error.response.data.errors) {
                        this.orderCreationError.errorList = error.response.data.errors || []; //Laravel validation errors
                    }
                }
            }).finally(() => {
                this.placeOrderRequestSent = false;
            });
        },
        flushErrors() {
            this.orderCreationError = {
                message: null,
                errorList: [],
            }
        }
    },
};
</script>

<style scoped>
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .message {
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.5s ease-in-out;
    }

    .error {
        background-color: #e74c3c;
        border: 2px solid #c0392b;
    }

    .success {
        background-color: rgba(0, 170, 62, 0.91);
        border: 2px solid #18a150;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .cart-container {
        width: 100%;
        max-width: 600px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        padding: 10px;
    }

    .cart-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;

        transition: transform 0.5s ease;
    }

    .cart-item.added {
        transform: scale(1.02);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-details {
        display: flex;
        flex-direction: column;
        max-width: 70%;
    }

    .cart-item-name {
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    .cart-item-price,
    .cart-item-quantity {
        margin: 5px 0;
        font-size: 16px;
        color: #666;
    }

    .remove-product-from-cart-btn {
        background-color: #e74c3c;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .remove-product-from-cart-btn:hover {
        background-color: #c0392b;
    }

    .cart-summary {
        margin-top: 20px;
        text-align: right;
    }

    .create-order-btn {
        background-color: #3498db;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .create-order-btn:disabled {
        background-color: #bdc3c7;
        cursor: not-allowed;
    }

    .create-order-btn:hover:not(:disabled) {
        background-color: #2980b9;
    }

    .cart {
        width: 100%;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        gap: 30px
    }

    .products {
        flex: 3;
    }

    .cart-details {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
</style>
