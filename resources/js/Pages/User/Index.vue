<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>{{ "User" }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">PIMMS</a>
                        </li>
                        <!-- <li class="breadcrumb-item">Users</li> -->
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#usermodal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New User
                </button>
            </div>
        </div>
        <!-- End Page Title -->
        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <!-- Table with stripped rows -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">#</th>
                                    <th scope="col">{{ "User name" }}</th>
                                    <th scope="col">{{ "User email" }}</th>
                                    <th scope="col">{{ "User Role" }}</th>

                                    <th scope="col" class="text-center">
                                        {{ "Actions" }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(user, index) in Users"
                                    :key="user.id"
                                >
                                    <th class="text-center">{{ index + 1 }}</th>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.role }}</td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            <!-- <Link  v-if="restrictAdmin(user.role,$page.props.user.role)" 
                                                type="button"
                                                class="btn btn-sm fs-6"
                                                :title="('Edit')"
                                                :href="route('users.edit', user.id)"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </Link> -->
                                            <!-- <DeleteModal v-if="restrictAdmin(user.role,$page.props.user.role)" :deleteId="user.id"  @deleteThis="deleteThis"></DeleteModal> -->
                                            <Link
                                                v-if="
                                                    user.role == 'admin' ||
                                                    user.role == 'super admin'
                                                "
                                                type="button"
                                                class="btn btn-sm fs-6"
                                                :title="'Edit'"
                                                data-bs-toggle="modal"
                                                data-bs-target="#usermodal"
                                                @click="
                                                    showUser(user.id),
                                                        clearFields()
                                                "
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </Link>
                                            <DeleteModal
                                                v-if="
                                                    user.role == 'admin' ||
                                                    user.role == 'super admin'
                                                "
                                                :deleteId="user.id"
                                                @deleteThis="deleteThis"
                                            ></DeleteModal>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table with stripped rows -->
                </div>
            </div>
        </section>
        <!-- student modal -->
        <div
            class="modal fade"
            id="usermodal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="section-title mt-1">
                            <h5 class="c-theme-red">User form</h5>
                        </div>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Vertical Form -->
                        <div class="card card-body p-3">
                            <form @submit.prevent="submit">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="user_name">{{
                                            "User name"
                                        }}</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="user_name"
                                            :class="{
                                                'invalid-bg':
                                                    formErrors.user_name,
                                            }"
                                            v-model="form.user_name"
                                        />

                                        <div
                                            class="invalid-feedback animated fadeIn"
                                            v-if="formErrors.user_name"
                                        >
                                            {{ formErrors.user_name[0] }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="email">{{
                                            "User email"
                                        }}</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            :class="{
                                                'invalid-bg': formErrors.email,
                                            }"
                                            v-model="form.email"
                                        />

                                        <div
                                            class="invalid-feedback animated fadeIn"
                                            v-if="formErrors.email"
                                        >
                                            {{ formErrors.email[0] }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12">
                                        <label>{{ "Role" }} </label>
                                        <Multiselect
                                            id=""
                                            v-model="form.role"
                                            :options="RoleOptions"
                                            :searchable="true"
                                            :class="{
                                                'invalid-bg': formErrors.role,
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback animated fadeIn"
                                            v-if="formErrors.role"
                                        >
                                            {{ formErrors.role[0] }}
                                        </div>
                                    </div>
                                    <div class="row g-3" v-if="!form.user_id">
                                        <div class="col-12 col-md-6">
                                            <label for="password">{{
                                                "User password"
                                            }}</label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="password"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.password,
                                                }"
                                                v-model="form.password"
                                            />

                                            <div
                                                class="invalid-feedback animated fadeIn"
                                                v-if="formErrors.password"
                                            >
                                                {{ formErrors.password[0] }}
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="confirm_password">{{
                                                "Confirm password"
                                            }}</label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="confirm_password"
                                                :class="{
                                                    'invalid-bg':
                                                        formErrors.confirm_password,
                                                }"
                                                v-model="form.confirm_password"
                                            />

                                            <div
                                                class="invalid-feedback animated fadeIn"
                                                v-if="
                                                    formErrors.confirm_password
                                                "
                                            >
                                                {{
                                                    formErrors
                                                        .confirm_password[0]
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                        v-if="formStatus == 1"
                                    >
                                        {{ "Save" }}
                                    </button>
                                    <button
                                        class="btn btn-success"
                                        type="button"
                                        disabled
                                        v-else
                                    >
                                        {{ "Save" }}
                                        <span
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                    </button>
                                </div>
                            </form>
                            <!-- Vertical Form -->
                        </div>
                    </div>
                </div>
            </div>
            <button
                hidden="hidden"
                data-bs-toggle="modal"
                data-bs-target="#usermodal"
                title="Delete"
                ref="closeModal"
            ></button>
        </div>
    </main>
    <!-- End #main -->
</template>

<script>
import Master from "../Layout/Master.vue";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";
export default {
    layout: Master,
    created() {
        this.fetchUsers();
    },
    components: {
        Multiselect,
    },
    data() {
        return {
            Users: [],
            deleteId: null,
            downloadBtnStatus: 1,
            Users_ids: "",
            form: {
                user_id: "",
                user_name: "",
                email: "",
                role: "",
                password: "",
                confirm_password: "",
            },

            RoleOptions: ["super admin", "admin", "accountant"],
            formErrors: [],
            formStatus: 1,
        };
    },
    methods: {
        fetchUsers() {
            axios
                .get(route("api.users.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.Users = response.data.users;
                    this.Users_ids = response.data.users_ids;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        deleteThis(id) {
            axios
                .delete(route("api.users.delete", id), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then(() => {
                    toastr.success("User deleted successfully.");
                    this.fetchUsers();
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        submit() {
            this.formStatus = 0;
            axios
                .post(route("api.users.store"), this.form, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then(() => {
                    if (this.form.user_id) {
                        toastr.success("User record updated.");
                    } else {
                        toastr.success("New user created.");
                    }
                    this.$refs.closeModal.click();
                    this.fetchUsers();
                    this.formStatus = 1;
                })
                .catch((error) => {
                    this.formStatus = 1;
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },
        clearFields() {
            this.form.user_id = "";
            this.form.user_name = "";
            this.form.email = "";
            this.form.role = "";
            this.form.password = "";
            this.form.confirm_password = "";
            this.formErrors = [];
        },
        showUser(usre_id) {
            axios
                .get(route("api.users.show", usre_id), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.form.user_id = response.data.id;
                    this.form.user_name = response.data.name;
                    this.form.email = response.data.email;
                    this.form.role = response.data.role;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        restrictAdmin(user_role, logged_user_role) {
            if (user_role == "super admin" && logged_user_role == "admin") {
                return false;
            }
            return true;
        },
    },
};
</script>

<style>
.table td,
.table th {
    vertical-align: middle;
}
</style>
