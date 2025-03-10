<template>
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
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
                        <div class="col-lg-4 col-md-6 d-flex flex-column">
                            <div class="d-flex justify-content-center py-4">
                                <Link :href="route('home')"> 
                                    <img
                                        :src="$page.props.logo"
                                        height="60"
                                        alt="logo"
                                    /> 
                                </Link>
                            </div>
                            <!-- End Logo -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5
                                            class="card-title text-center pb-0 fs-4"
                                        >
                                            {{
                                                translate("Reset Your Password")
                                            }}
                                        </h5>
                                    </div>

                                    <form @submit.prevent="submit">
                                        <div class="col-12">
                                            <label
                                                for="password"
                                                class="form-label"
                                                >{{ translate("Password") }}
                                            </label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.password,
                                                }"
                                                id="password"
                                                v-model="form.password"
                                                :placeholder="
                                                    translate('Password')
                                                "
                                            />
                                            <div
                                                v-if="formErrors.password"
                                                class="invalid-feedback"
                                            >
                                                {{ formErrors.password[0] }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label
                                                for="confirm_password"
                                                class="form-label"
                                                >{{
                                                    translate(
                                                        "Confirm Password"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.confirm_password,
                                                }"
                                                id="confirm_password"
                                                v-model="form.confirm_password"
                                                :placeholder="
                                                    translate(
                                                        'Confirm Password'
                                                    )
                                                "
                                            />
                                            <div
                                                v-if="
                                                    formErrors.confirm_password
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    formErrors
                                                        .confirm_password[0]
                                                }}
                                            </div>
                                        </div>

                                        <br />
                                        <div class="col-12">
                                            <div class="mt-2">
                                                <button
                                                    v-if="formStatus == 'Yes'"
                                                    class="btn btn-success w-100"
                                                    type="submit"
                                                >
                                                    {{ translate("Reset Now") }}
                                                </button>

                                                <button
                                                    class="btn btn-success w-100"
                                                    type="submit"
                                                    disabled
                                                    v-else
                                                >
                                                    {{ translate("Reset Now") }}
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
                                                <br />

                                                <Link :href="route('login')"  class="theme-color-red">
                                                    {{
                                                        translate(
                                                            "Login If Already Registered"
                                                        )
                                                    }}
                                                </Link>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="credits"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!-- End #main -->
</template>

<script>
export default {
    props: ["user_id"],

    data() {
        return {
            form: {
                user_id: this.user_id,
                password: "",
                confirm_password: "",
            },
            formProfileErrors: [],
            formErrors: [],
            formStatus: "Yes",
        };
    },
    methods: {
        submit() {
            this.formStatus = "No";
            axios
                .post(route("api.reset-password"), this.form, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then(() => {
                    toastr.success(
                        "Password reset successfully now login with new password"
                    );

                    this.$inertia.get(route("login"));
                })
                .catch((error) => {
                    // console.log(error.response.data);
                    toastr.error(error.response.data.message);
                    this.formStatus = "Yes";
                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },
    },
};
</script>

<style scoped>
@import "~/backend/assets/vendor/bootstrap/css/bootstrap.min.css";
@import "~/backend/assets/vendor/bootstrap-icons/bootstrap-icons.css";
@import "~/backend/assets/vendor/remixicon/remixicon.css";
@import "~/backend/assets/vendor/boxicons/css/boxicons.min.css";
@import "~/backend/assets/vendor/quill/quill.snow.css";
@import "~/backend/assets/vendor/quill/quill.bubble.css";
@import "~/backend/assets/vendor/simple-datatables/style.css";
@import "~/backend/assets/css/style.css";
 
@import "toastr";

.invalid-feedback {
    display: block;
}
main {
    background: url("/images/login-reset-forgot-pages-back.jpg") no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
.btn-success, .btn-success:hover, .btn-success:active, .btn-success:visited {
    background-color: #ed2124 !important;
    border:#ed2124 !important;
}

.btn-success:focus  {
    box-shadow: 0 0 0 0.2rem rgba(255, 28, 3, 0.724) !important;
  }
.btn-success:active  {
    box-shadow: 0 0 0 0.2rem rgba(255, 28, 3, 0.724) !important;
  }
  .theme-color-red
  {
    color:#ed2124 !important;
  }
/* .invalid-bg {
    border-color: #f8d4d4 !important;
    background-color: #f8d4d4 !important;
} */
</style>
