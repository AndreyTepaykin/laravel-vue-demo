import Swal from "sweetalert2";

function isMobileViewport() {
    return typeof window !== "undefined"
        && window.matchMedia("(max-width: 576px)").matches;
}

export default {
    methods:{
        showSwal(options) {
            const mobile = isMobileViewport();

            new Swal({
                toast: true,
                position: options.position || (mobile ? "top" : "top-right"),
                iconColor: "white",
                width: options.width ? options.width : (mobile ? "92%" : 300),
                text: options.message,
                customClass: {
                    popup: options.type === "success" ? "colored-toast-success" : "colored-toast-error",
                },
                showConfirmButton: false,
                showCloseButton: true,
                timer: 2000,
                timerProgressBar: true,
            });
        },
        showSwalConfirmationDelete() {
            const swalDelete = new Swal({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true,
                customClass: {
                  confirmButton: "btn bg-gradient-success",
                  cancelButton: "btn bg-gradient-danger",
                },
                buttonsStyling: false,
              });
            
            return swalDelete;
        },
    }
}