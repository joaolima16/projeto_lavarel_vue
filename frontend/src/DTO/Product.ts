export interface ProductImage {
    id: number;
    product_id: number;
    path: string;
}

export interface Product {
    id: number;
    name: string;
    description: string;
    price_cost: number;
    price_sale: number;
    images?: ProductImage[];
    created_at?: string;
}
