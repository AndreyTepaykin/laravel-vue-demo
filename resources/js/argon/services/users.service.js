import axios from 'axios';
import authHeader from './auth-header';

async function list() {
  const response = await axios.get('/api/users', { headers: authHeader() });

  return response.data.data ?? response.data;
}

async function create(user) {
  const response = await axios.post(
    '/api/users',
    {
      name: user.name,
      email: user.email,
      password: user.password,
      password_confirmation: user.password_confirmation ?? user.confirm_password,
      role: user.role ?? 'user',
    },
    { headers: authHeader() },
  );

  return response.data.user ?? response.data;
}

async function update(id, user) {
  const payload = {
    name: user.name,
    email: user.email,
    role: user.role,
  };

  if (user.password) {
    payload.password = user.password;
    payload.password_confirmation = user.password_confirmation ?? user.confirm_password;
  }

  const response = await axios.put(`/api/users/${id}`, payload, { headers: authHeader() });

  return response.data.user ?? response.data;
}

async function destroy(id) {
  await axios.delete(`/api/users/${id}`, { headers: authHeader() });
}

export default {
  list,
  create,
  update,
  destroy,
};
