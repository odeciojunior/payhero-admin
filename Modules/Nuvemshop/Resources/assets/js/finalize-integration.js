document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get("code");

    const timeout = 3000;

    const showAlert = (type, message) => {
        swal({
            position: "bottom",
            type: type,
            toast: "true",
            title: message,
            showConfirmButton: false,
            timer: timeout,
        });
    };

    const redirect = () => {
        setTimeout(() => {
            localStorage.removeItem("nuvemshop_pending_integration");
            window.location.href = "/apps/nuvemshop";
        }, timeout);
    };

    if (!token) {
        showAlert("error", "Token não encontrado.");
        redirect();
        return;
    }

    if (!localStorage.getItem("nuvemshop_pending_integration")) {
        showAlert("error", "Integração não encontrada.");
        redirect();
        return;
    }

    fetch("/api/apps/nuvemshop/finalize", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ token: token }),
    })
        .then((response) => {
            if (response.ok) {
                showAlert("success", "Integração concluída com sucesso.");
                redirect();
            } else {
                showAlert("error", "Ocorreu um erro inesperado.");
                redirect();
            }
        })
        .catch((error) => {
            showAlert("error", "Ocorreu um erro inesperado.");
            redirect();
        });
});
