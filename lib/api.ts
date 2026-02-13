export interface User {
    id: string;
    email: string;
    name: string;
    role: 'admin' | 'viewer';
}

export interface AuthResponse {
    user: User;
    token: string;
}

export const ApiService = {
    async login(credentials: { email: string; password: string }): Promise<AuthResponse> {
        const res = await fetch('/AppSocial/backend/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(credentials),
        });

        if (!res.ok) {
            const error = await res.json().catch(() => ({ message: 'Login failed' }));
            throw new Error(error.message || 'Login failed');
        }
        return res.json();
    },

    async register(data: { email: string; password: string; name: string; registrationCode: string }): Promise<AuthResponse> {
        const res = await fetch('/AppSocial/backend/api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        if (!res.ok) {
            const error = await res.json().catch(() => ({ message: 'Registration failed' }));
            throw new Error(error.message || 'Registration failed');
        }
        return res.json();
    }
};
