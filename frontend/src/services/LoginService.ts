import type { LoginResponse } from '@/DTO/LoginResponse';
import api from './ApiService';

class LoginService {
    loginRequest = async (email: string, password: string) => {
        const { data } = await api.post<LoginResponse>('/login', {
            email,
            password
        });
        return data;
    }


}
export default new LoginService();