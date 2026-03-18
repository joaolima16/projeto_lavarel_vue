<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ProductService from '@/services/ProductService';
import type { Product } from '@/DTO/Product';

const route = useRoute();
const router = useRouter();

const product = ref<Product | null>(null);
const loading = ref<boolean>(true);
const error = ref<string>('');
const currentImageIndex = ref<number>(0);

const fetchProduct = async () => {
  loading.value = true;
  const id = Number(route.params.id);
  
  if (!id) {
    error.value = 'Produto inválido.';
    loading.value = false;
    return;
  }

  try {
    product.value = await ProductService.getById(id);
    currentImageIndex.value = 0; 
  } catch (e) {
    error.value = 'Erro ao carregar detalhes do produto.';
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const getImageUrl = (path: string) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }
  const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
  const baseUrl = apiUrl.replace(/\/api\/?$/, '');
  return `${baseUrl}/storage/${path.replace(/^\//, '')}`;
};

const nextImage = () => {
  if (product.value?.images && product.value.images.length > 0) {
    currentImageIndex.value = (currentImageIndex.value + 1) % product.value.images.length;
  }
};

const prevImage = () => {
  if (product.value?.images && product.value.images.length > 0) {
    currentImageIndex.value = (currentImageIndex.value - 1 + product.value.images.length) % product.value.images.length;
  }
};

const setImage = (index: number) => {
  currentImageIndex.value = index;
}

const hasImages = computed(() => product.value?.images && product.value.images.length > 0);

onMounted(() => {
  fetchProduct();
});
</script>

<template>
  <div class="product-details-container">
    <button class="btn-back" @click="router.back()">&larr; Voltar</button>

    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else-if="product" class="product-content">
      <div class="product-images">
        <div class="main-image-container">
          <template v-if="hasImages">
            <img :src="getImageUrl(product.images![currentImageIndex].path)" alt="Imagem do produto" class="main-image" />
            
            <div v-if="product.images!.length > 1" class="carousel-controls">
              <button class="btn-nav" @click.stop="prevImage">&lsaquo;</button>
              <button class="btn-nav" @click.stop="nextImage">&rsaquo;</button>
            </div>
          </template>
          <div v-else class="placeholder-image">Sem imagem</div>
        </div>

        <!-- Miniaturas -->
        <div v-if="hasImages && product.images!.length > 1" class="thumbnails">
          <img 
            v-for="(img, index) in product.images" 
            :key="img.id" 
            :src="getImageUrl(img.path)" 
            class="thumb" 
            :class="{ active: index === currentImageIndex }"
            @click="setImage(index)"
          />
        </div>
      </div>

      <!-- Informações -->
      <div class="product-info">
        <span class="product-id">ID: #{{ product.id }}</span>
        <h1 class="product-name">{{ product.name }}</h1>
        
        <div class="price-section">
          <div class="price-value">{{ formatCurrency(product.price_sale) }}</div>
          <div class="cost-price">Custo: {{ formatCurrency(product.price_cost) }}</div>
        </div>

        <span class="product-description-label">Descrição:</span>
        <p class="product-description">{{ product.description || 'Nenhuma descrição informada.' }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped src="@/assets/css/ProductDetailsPage.css"></style>
