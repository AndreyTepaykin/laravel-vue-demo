<template>
  <div class="card ms-2 me-3">
    <div class="card-header pb-0 d-flex align-items-center">
      <h6 class="mb-0">Users List</h6>
      <argon-button
        color="success"
        size="sm"
        class="ms-auto"
        @click.prevent="openCreate"
      >
        <i class="fa fa-plus me-2"></i>Add User
      </argon-button>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
      <div class="table-responsive p-0">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                Name
              </th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Email
              </th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Role
              </th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Created at
              </th>
              <th class="text-secondary opacity-7"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>
                <div class="d-flex px-2">
                  <div class="my-auto">
                    <h6 class="mb-0 text-sm">{{ user.name }}</h6>
                  </div>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-secondary text-xs font-weight-bold">{{ user.email }}</span>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="badge badge-sm bg-gradient-info">{{ (user.roles || []).join(', ') || '—' }}</span>
              </td>
              <td class="align-middle text-center">
                <span class="text-secondary text-xs font-weight-bold">
                  {{ formatDate(user.created_at) }}
                </span>
              </td>
              <td class="align-middle">
                <a
                  class="text-secondary font-weight-bold text-xs me-3 cursor-pointer"
                  @click.prevent="openEdit(user)"
                >
                  <i class="fas fa-user-edit"></i>
                </a>
                <a
                  v-if="currentUserId !== user.id"
                  class="text-secondary font-weight-bold text-xs cursor-pointer"
                  @click.prevent="confirmDelete(user)"
                >
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <tr v-if="!users.length">
              <td colspan="5" class="text-center text-sm text-secondary py-4">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div v-if="showForm" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.45)">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ editingUser ? 'Edit User' : 'Add User' }}</h5>
          <button type="button" class="btn-close" @click="closeForm"></button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <argon-input
                v-model="form.name"
                type="text"
                :minlength="$fieldMin.name"
                :maxlength="$fieldMax.name"
                :value="form.name"
              />
              <validation-error :errors="apiValidationErrors.name" />
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <argon-input
                v-model="form.email"
                type="email"
                :minlength="$fieldMin.email"
                :maxlength="$fieldMax.email"
                :value="form.email"
              />
              <validation-error :errors="apiValidationErrors.email" />
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select v-model="form.role" class="form-select">
                <option value="user">user</option>
                <option value="admin">admin</option>
              </select>
              <validation-error :errors="apiValidationErrors.role" />
            </div>
            <div class="mb-3">
              <label class="form-label">
                Password
                <span v-if="editingUser" class="text-xs text-secondary">(leave blank to keep)</span>
              </label>
              <argon-input
                v-model="form.password"
                type="password"
                :minlength="$fieldMin.password"
                :maxlength="$fieldMax.password"
                :value="form.password"
              />
              <validation-error :errors="apiValidationErrors.password" />
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <argon-input
                v-model="form.password_confirmation"
                type="password"
                :minlength="$fieldMin.password"
                :maxlength="$fieldMax.password"
                :value="form.password_confirmation"
              />
            </div>
          </div>
          <div class="modal-footer">
            <argon-button type="button" color="secondary" size="sm" @click="closeForm">
              Cancel
            </argon-button>
            <argon-button color="success" size="sm" :disabled="saving">
              {{ saving ? 'Saving…' : 'Save' }}
            </argon-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ArgonButton from '../../../components/ArgonButton.vue';
import ArgonInput from '../../../components/ArgonInput.vue';
import ValidationError from '../../../components/ValidationError.vue';
import formMixin from '../../../mixins/form-mixin.js';
import showSwal from '../../../mixins/showSwal.js';

export default {
  name: 'ListUserPage',
  components: {
    ArgonButton,
    ArgonInput,
    ValidationError,
  },
  mixins: [formMixin, showSwal],
  data() {
    return {
      showForm: false,
      editingUser: null,
      saving: false,
      form: {
        name: '',
        email: '',
        role: 'user',
        password: '',
        password_confirmation: '',
      },
    };
  },
  computed: {
    users() {
      return this.$store.getters['users/items'];
    },
    currentUserId() {
      return this.$store.getters['profile/me']?.id;
    },
  },
  async created() {
    try {
      await this.$store.dispatch('users/fetchAll');
    } catch (error) {
      this.showSwal({
        type: 'error',
        message: error.response?.status === 403 && !error.response?.data?.message
          ? 'Only admins can manage users.'
          : this.apiErrorMessage(error, 'Failed to load users.'),
      });
      if (error.response?.status === 403) {
        this.$router.push({ name: 'Dashboard' });
      }
    }
  },
  methods: {
    formatDate(value) {
      if (!value) {
        return '—';
      }

      return String(value).slice(0, 10);
    },
    resetForm() {
      this.form = {
        name: '',
        email: '',
        role: 'user',
        password: '',
        password_confirmation: '',
      };
      this.editingUser = null;
      this.resetApiValidation();
    },
    openCreate() {
      this.resetForm();
      this.showForm = true;
    },
    openEdit(user) {
      this.resetForm();
      this.editingUser = user;
      this.form = {
        name: user.name,
        email: user.email,
        role: (user.roles && user.roles[0]) || 'user',
        password: '',
        password_confirmation: '',
      };
      this.showForm = true;
    },
    closeForm() {
      this.showForm = false;
      this.resetForm();
    },
    async submitForm() {
      this.saving = true;
      this.resetApiValidation();

      try {
        if (this.editingUser) {
          await this.$store.dispatch('users/update', {
            id: this.editingUser.id,
            user: this.form,
          });
        } else {
          await this.$store.dispatch('users/create', this.form);
        }

        this.showSwal({
          type: 'success',
          message: this.editingUser ? 'User updated.' : 'User created.',
        });
        this.closeForm();
      } catch (error) {
        this.setApiValidation(error.response?.data?.errors);
        this.showSwal({
          type: 'error',
          message: this.apiErrorMessage(error),
        });
      } finally {
        this.saving = false;
      }
    },
    async confirmDelete(user) {
      const result = await this.showSwalConfirmationDelete();

      if (!result.isConfirmed) {
        return;
      }

      try {
        await this.$store.dispatch('users/remove', user.id);
        this.showSwal({ type: 'success', message: 'User deleted.' });
      } catch (error) {
        this.showSwal({
          type: 'error',
          message: this.apiErrorMessage(error, 'Delete failed.'),
        });
      }
    },
  },
};
</script>
