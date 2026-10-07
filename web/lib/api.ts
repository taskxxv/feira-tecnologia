export type User = {
  id: number;
  name: string;
  email: string;
  role?: string;
};

export type Post = {
  id: number;
  title: string;
  content: string;
  status: string;
  created_at: string;
  user?: User;
  subject?: { id: number; name: string };
  comments?: Comment[];
  comments_count?: number;
  notice?: string | null;
};

export type Subject = { id: number; name: string };

export type Comment = {
  id: number;
  content: string;
  created_at: string;
  user?: User;
};

const base = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/+$/, '');

export class ApiError extends Error {
  constructor(message: string, public status: number) { super(message); }
}

export function token() {
  return typeof window !== 'undefined' ? localStorage.getItem('token') : null;
}

export async function api<T>(path: string, options: RequestInit = {}) {
  const headers = new Headers(options.headers);
  headers.set('Content-Type', 'application/json');
  headers.set('Accept', 'application/json');

  const accessToken = token();

  // O token só é incluído quando existe uma sessão autenticada.
  if (accessToken) {
    headers.set('Authorization', `Bearer ${accessToken}`);
  }

  const response = await fetch(`${base}${path}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const error = await response
      .json()
      .catch(() => ({ message: 'Não foi possível concluir a operação' }));

    const messages = error.errors ? Object.values(error.errors).flat().join(' ') : '';
    throw new ApiError(messages || error.message || 'Erro na API', response.status);
  }

  return response.status === 204 ? (undefined as T) : response.json();
}
