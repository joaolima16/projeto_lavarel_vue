<script setup lang="ts">
import { ref, onMounted } from 'vue';
import ProductService from '@/services/ProductService';
import type { Product } from '@/DTO/Product';
import { useRouter } from 'vue-router';

const products = ref<Product[]>([]);
const loading = ref<boolean>(true);
const error = ref<string>('');
const router = useRouter();

const fetchProducts = async () => {
  loading.value = true;
  try {
    products.value = await ProductService.getAll();
  } catch (e) {
    error.value = 'Erro ao carregar produtos.';
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const handleEdit = (id: number) => {
  router.push(`/products/edit/${id}`);
};

const handleInactivate = async (id: number) => {
  if (confirm('Tem certeza que deseja inativar/excluir este produto?')) {
    try {
      await ProductService.delete(id);
      // Remove o item da lista localmente para não precisar recarregar tudo
      products.value = products.value.filter(p => p.id !== id);
    } catch (e) {
      alert('Erro ao excluir o produto.');
      console.error(e);
    }
  }
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

onMounted(() => {
  fetchProducts();
});
</script>

<template>
  <div class="product-list-container">
    <div class="header">
      <h2>Lista de Produtos</h2>
      <button class="btn-create" @click="router.push('/products/create')">Novo Produto</button>
    </div>

    <div v-if="loading" class="loading">Carregando produtos...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <table v-else class="product-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Preço Custo</th>
          <th>Preço Venda</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ formatCurrency(product.price_cost) }}</td>
          <td class="sale-price">{{ formatCurrency(product.price_sale) }}</td>
          <td class="actions">
            <button @click="handleEdit(product.id)" class="btn-edit">Alterar</button>
            <button @click="handleInactivate(product.id)" class="btn-delete">Inativar</button>
            <button @click="router.push(`/products/details/${product.id}`)" class="btn-details">Detalhes</button>
          </td>
        </tr>
        <tr v-if="products.length === 0">
          <td colspan="5" class="empty">Nenhum produto encontrado.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped src="@/assets/css/ProductListPage.css"></style>
