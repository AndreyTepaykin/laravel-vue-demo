export default {
  data() {
    return {
      isLoading: false,
      apiValidationErrors: {},
    };
  },
  methods: {
    setApiValidation(serverErrors) {
      if (!serverErrors) {
        this.apiValidationErrors = {};

        return;
      }

      if (serverErrors.attachment) {
        this.apiValidationErrors = { attachment: [serverErrors.attachment[0]] };

        return { attachment: serverErrors.attachment[0] };
      }

      // Laravel validation format: { field: ['message'] }
      if (!Array.isArray(serverErrors)) {
        this.apiValidationErrors = serverErrors;

        return;
      }

      // Legacy JSON:API error objects
      this.apiValidationErrors = serverErrors.reduce((accumulator, errorObject) => {
        if (typeof errorObject.source === 'undefined') {
          return accumulator;
        }

        const errorFieldName = errorObject.source.pointer.split('/')[3];
        const errorDetail = (accumulator[errorFieldName] || []).concat(errorObject.detail);

        return {
          ...accumulator,
          [errorFieldName]: errorDetail,
        };
      }, {});
    },

    firstValidationMessage(serverErrors) {
      if (!serverErrors) {
        return 'Oops, something went wrong!';
      }

      if (!Array.isArray(serverErrors)) {
        const first = Object.values(serverErrors).flat()[0];

        return first || 'Oops, something went wrong!';
      }

      return serverErrors[0]?.detail || 'Oops, something went wrong!';
    },

    resetApiValidation() {
      this.apiValidationErrors = {};
    },
  },
};
