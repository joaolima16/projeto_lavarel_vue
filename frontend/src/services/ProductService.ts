import api from './ApiService';
import type { Product } from '@/DTO/Product';

interface ProductResponse {
    data: Product[];
}

interface SingleProductResponse {
    data: Product;
}

class ProductService {
    getAll = async (): Promise<Product[]> => {
        const { data } = await api.get<ProductResponse>('/products');
        return data.data;
    }

    getById = async (id: number): Promise<Product> => {
        const { data } = await api.get<SingleProductResponse>(`/products/${id}`);
        return data.data;
    }

    create = async (productData: FormData): Promise<Product> => {
        const { data } = await api.post<SingleProductResponse>('/products', productData);
        return data.data;
    }

    update = async (id: number, productData: FormData): Promise<Product> => {
        const { data } = await api.post<SingleProductResponse>(`/products/${id}`, productData);
        return data.data;
    }

    delete = async (id: number): Promise<void> => {
        await api.delete(`/products/${id}`);
    }
}

export default new ProductService();
