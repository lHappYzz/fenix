<template>
    <div class="product">
        <img :src="product.image" alt="Зображення продукту" class="product-image" />
        <div class="product-details">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-description">{{ product.description }}</p>
            <p class="product-price">Ціна: {{ product.price }} ₴</p>
            <button @click="addToCart" class="add-to-cart-button">Додати до корзини</button>
        </div>
    </div>
</template>

<style scoped>
    .product {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        transition: box-shadow 0.3s ease;
        margin-bottom: 15px;
    }

    .product:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .product-details {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-name {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .product-price {
        margin: 5px 0;
        font-size: 16px;
        color: #666;
    }

    .add-to-cart-button {
        align-self: start;
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        cursor: pointer;
        border-radius: 3px;
        font-size: 14px;
    }

    .add-to-cart-button:hover {
        background-color: #218838;
    }
</style>
<script>
    export default {
        props: {
            product: {
                type: Object,
                required: true,
            },
        },
        methods: {
            addToCart() {
                this.$emit('add-to-cart', this.product);
            },
        },
    };
</script>
