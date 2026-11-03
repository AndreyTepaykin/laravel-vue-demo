import axios from 'axios';
import authHeader from './auth-header';

async function getMe() {
  const response = await axios.get('/api/user', { headers: authHeader() });

  return response.data.data ?? response.data;
}

async function updateProfile(updateUser) {
  const payload = {
    name: updateUser.name,
    email: updateUser.email,
  };

  if (updateUser.password) {
    payload.password = updateUser.password;
    payload.password_confirmation = updateUser.password_confirmation ?? updateUser.confirm_password;
  }

  const response = await axios.put('/api/user', payload, { headers: authHeader() });

  return response.data.user?.data ?? response.data.user ?? response.data;
}

export default {
  getMe,
  updateProfile,
};
