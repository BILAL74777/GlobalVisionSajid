<template>
    <!-- Google Fonts -->
    <a href="https://fonts.gstatic.com" rel="preconnect" />
    <a
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet"
    />

    <main>
        <div class="container">
            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4"
            >
                <div class="container">
                    <div class="row justify-content-center">
                        <div
                            class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center"
                        >
                            <div class="d-flex justify-content-center py-4">
                                <a :href="route('/')"> 
                                    <img
                                        src="http://ttcglobal.test/backend/assets/img/logo.png"
                                        height="60"
                                        alt="logo"
                                    /> 
                                </a>
                            </div>
                            <!-- End Logo -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5
                                            class="card-title text-center pb-0 fs-4"
                                        >
                                            {{
                                                (
                                                    "Login to Your Account"
                                                )
                                            }}
                                        </h5>
                                    </div>

                                    <form
                                        class="row g-3"
                                        @submit.prevent="submit"
                                    >
                                        <div class="col-12">
                                            <label
                                                for="email"
                                                class="form-label"
                                                >{{ ("Email") }}
                                            </label>
                                            <input
                                                type="email"
                                                class="form-control"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.email,
                                                }"
                                                id="email"
                                                v-model="form.email"
                                                :placeholder="
                                                    ('Email')
                                                "
                                                autocomplete="current-email"
                                            />
                                            <div
                                                v-if="formErrors.email"
                                                class="invalid-feedback"
                                            >
                                                {{ formErrors.email[0] }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label
                                                for="password"
                                                class="form-label"
                                                >{{
                                                    ("Password")
                                                }}</label
                                            >
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="password"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.email,
                                                }"
                                                v-model="form.password"
                                                :placeholder="
                                                    ('Password')
                                                "
                                                autocomplete="current-password"
                                            />
                                            <div
                                                v-if="formErrors.password"
                                                class="invalid-feedback"
                                            >
                                                {{ formErrors.password[0] }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mt-2">
                                                <button
                                                    v-if="formStatus == 1"
                                                    class="btn btn-success w-100"
                                                    type="submit"
                                                >
                                                    {{ ("Login") }}
                                                </button>

                                                <button
                                                    class="btn btn-success w-100"
                                                    type="submit"
                                                    disabled
                                                    v-else
                                                >
                                                    {{ ("Login") }}
                                                    <span
                                                        class="spinner-border spinner-border-sm"
                                                        role="status"
                                                        aria-hidden="true"
                                                    ></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">
                                                <a
                                                    :href="
                                                        route('/')
                                                    "
                                                    class="theme-color-red"
                                                >
                                                    {{
                                                        (
                                                            "Forgot Password?"
                                                        )
                                                    }}
                                                </a>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">
                                                <a
                                                    :href="route('/')"
                                                    class="theme-color-red"
                                                >
                                                    {{ ("Register") }}
                                                </a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="credits">
                                <!-- All the links in the footer should remain intact. -->
                                <!-- You can delete the links only if you purchased the pro version. -->
                                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                                <!-- Designed by
                                <a href="https://bootstrapmade.com/"
                                    >BootstrapMade</a
                                > -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!-- End #main -->
</template>

<script>
import axios from "axios";
export default {
    props: ["message"],
    created() {
        this.messagePrint();
    },
    data() {
        return {
            form: this.$inertia.form({
                email: "",
                password: "",
                role: "admin",
                remember: false,
            }),
            formErrors: [],
            formStatus: 1,
        };
    },
    methods: {
        submit() {
            this.formStatus = 0;
            axios
                .post(route("api.login"), this.form)
                .then((response) => {
                    if (response.data.message) {
                        toastr.success(response.data.message);
                    }
                    this.form.reset();
                    this.$inertia.get(route("dashboard"));
                })
                .catch((error) => {
                    this.formStatus = 1;
                    this.form.reset("password");
                    toastr.error(error.response.data.message);
                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },
        messagePrint() {
            if (this.message) {
                toastr.success(this.message);
            }
        },
    },
};
</script>

<style scoped>
@import "http://ttcglobal.test/backend/assets/vendor/bootstrap/css/bootstrap.min.css";

@import "http://ttcglobal.test/backend/assets/vendor/bootstrap-icons/bootstrap-icons.css";
@import "http://ttcglobal.test/backend/assets/vendor/boxicons/css/boxicons.min.css";
@import "http://ttcglobal.test/backend/assets/vendor/remixicon/remixicon.css";
/* @import "http://ttcglobal.test/backend/assets/assets/vendor/quill/quill.snow.css"; */
@import "http://ttcglobal.test/backend/assets/vendor/simple-datatables/style.css";
@import "http://ttcglobal.test/backend/assets/css/style.css";
@import "http://ttcglobal.test/backend/assets/css/custom.css";

@import "toastr";

 
.c-file-padding {
    padding: 1rem 0.75rem !important;
}
.invalid-feedback {
    display: block !important;
}
.invalid-bg {
    border-color: #f8d4d4 !important;
    background-color: #f8d4d4 !important;
}

main {
    background: url("https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?auto=format&fit=crop&q=80&w=1974&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D") no-repeat
        center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}

.btn-success {
    background-color: rgb(65, 84, 241) !important;
    border-color: rgb(65, 84, 241) !important;
}

.btn-success:hover {
    background-color: rgb(13, 32, 201) !important;
    border-color: rgb(13, 32, 201) !important;
}

.btn-success:active {
    background-color: rgb(13, 32, 201) !important;
    border-color: rgb(13, 32, 201) !important;
}

.btn-success:focus {
    color: #fff !important;
    box-shadow: none !important;
}
.toast-success {
    background-color: rgb(65, 84, 241) !important;
}
.theme-text-color
{
    color: rgb(13, 32, 201) !important;
    font-weight: bold;
}
.theme-bg-color
{
    background-color: rgb(13, 32, 201) !important;
}
</style>
