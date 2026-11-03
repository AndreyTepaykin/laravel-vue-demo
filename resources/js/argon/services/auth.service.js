import axios from 'axios';

async function ensureCsrf() {
  await axios.get('/sanctum/csrf-cookie');
}

async function login(user) {
  await ensureCsrf();

  const response = await axios.post('/login', {
    email: user.email,
    password: user.password,
    remember: Boolean(user.remember),
  });

  localStorage.setItem('is_authenticated', '1');

  return response.data;
}

async function logout() {
  try {
    await axios.post('/logout');
  } finally {
    localStorage.removeItem('is_authenticated');
  }
}

async function register(user) {
  await ensureCsrf();

  const response = await axios.post('/register', {
    name: user.name,
    email: user.email,
    password: user.password,
    password_confirmation: user.confirm_password ?? user.password_confirmation,
  });

  localStorage.setItem('is_authenticated', '1');

  return response.data;
}

async function forgotPassword(data) {
  await ensureCsrf();

  const response = await axios.post('/forgot-password', {
    email: data.email,
  });

  return response.data;
}

async function resetPassword(data) {
  await ensureCsrf();

  const response = await axios.post('/reset-password', {
    password: data.password,
    password_confirmation: data.password_confirmation,
    email: data.email,
    token: data.token,
  });

  return response.data;
}

export default {
  login,
  logout,
  register,
  forgotPassword,
  resetPassword,
};
