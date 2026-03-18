<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ProductService from '@/services/ProductService';
import type { Product } from '@/DTO/Product';

const route = useRoute();
const router = useRouter();

const product = ref<Product | null>(null);
const loading = ref<boolean>(true);
const saving = ref<boolean>(false);
const error = ref<string>('');
const newImages = ref<FileList | null>(null);
const validationError = ref<string>('');

const fetchProduct = async () => {
  loading.value = true;
  const id = Number(route.params.id);
  if (!id) {
    error.value = 'ID inválido';
    loading.value = false;
    return;
  }

  try {
    product.value = await ProductService.getById(id);
  } catch (e) {
    error.value = 'Erro ao carregar dados do produto.';
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    newImages.value = target.files;
  }
};

const handleSave = async () => {
  if (!product.value) return;
  validationError.value = '';

  if (Number(product.value.price_sale) <= Number(product.value.price_cost) * 1.10) {
    validationError.value = 'O preço de venda deve ser maior que o preço de custo + 10%.';
    return;
  }

  saving.value = true;

  const formData = new FormData();
  formData.append('name', product.value.name);
  formData.append('description', product.value.description || '');
  formData.append('price_cost', String(product.value.price_cost));
  formData.append('price_sale', String(product.value.price_sale));
  formData.append('_method', 'PUT');

  if (newImages.value) {
    for (let i = 0; i < newImages.value.length; i++) {
      formData.append('images[]', newImages.value[i]);
    }
  }

  try {
    await ProductService.update(product.value.id, formData);
    router.push('/products');
  } catch (e) {
    error.value = 'Erro ao salvar alterações.';
    console.error(e);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchProduct();
});
</script>

<template>
  <div class="edit-product-container">
    <div class="header">
      <h2>Editar Produto</h2>
      <button class="btn-back" @click="router.back()">Voltar</button>
    </div>

    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="product" class="form-container">
      <div v-if="validationError" class="error">{{ validationError }}</div>
      <form @submit.prevent="handleSave">
        <div class="form-group">
          <label for="name">Nome:</label>
          <input id="name" v-model="product.name" required />
        </div>

        <div class="form-group">
          <label for="description">Descrição:</label>
          <textarea id="description" v-model="product.description" rows="4"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="price_cost">Preço de Custo (R$):</label>
            <input id="price_cost" type="number" step="0.01" v-model="product.price_cost" required />
          </div>

          <div class="form-group">
            <label for="price_sale">Preço de Venda (R$):</label>
            <input id="price_sale" type="number" step="0.01" v-model="product.price_sale" required />
          </div>
        </div>

        <div class="form-group">
          <label for="images">Adicionar Imagens:</label>
          <input id="images" type="file" multiple @change="handleFileChange" accept="image/png, image/jpeg" />
        </div>

        <div class="actions">
          <button type="submit" class="btn-save" :disabled="saving">
            {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped src="@/assets/css/EditProductPage.css"></style>
