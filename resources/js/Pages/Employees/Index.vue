<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">Employees</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">Global Vision</a>
                        </li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#employeeModal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New Employee
                </button>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">
                        Employee Records
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Commission</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(employee, index) in employees"
                                    :key="employee.id"
                                >
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>
                                        <a
                                            class="theme-text-color"
                                            :href="
                                                route(
                                                    'employee.details',
                                                    employee.id
                                                )
                                            "
                                        >
                                            {{ employee.name }}
                                        </a>
                                    </td>
                                    <td>{{ employee.email }}</td>
                                    <td>{{ employee.commission }}%</td>
                                    <td>{{ employee.phone || "N/A" }}</td>
                                    <td>{{ employee.address || "N/A" }}</td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-warning"
                                            @click="editEmployee(employee)"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div
                class="modal fade"
                id="employeeModal"
                tabindex="-1"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Employee Registration</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                ref="closeModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="submitEmployee">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label"
                                            >Name<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="employeeForm.name"
                                            :class="{
                                                'is-invalid':
                                                    employeeErrors.name,
                                            }"
                                        />
                                        <div
                                            v-if="employeeErrors.name"
                                            class="invalid-feedback"
                                        >
                                            {{ employeeErrors.name }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"
                                            >Commission<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="employeeForm.commission"
                                            :class="{
                                                'is-invalid':
                                                    employeeErrors.commission,
                                            }"
                                        />
                                        <div
                                            v-if="employeeErrors.commission"
                                            class="invalid-feedback"
                                        >
                                            {{ employeeErrors.commission }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label"
                                            >Phone<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="employeeForm.phone"
                                            :class="{
                                                'is-invalid':
                                                    employeeErrors.phone,
                                            }"
                                        />
                                        <div
                                            v-if="employeeErrors.phone"
                                            class="invalid-feedback"
                                        >
                                            {{ employeeErrors.phone }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label"
                                            >Email<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="email"
                                            class="form-control"
                                            v-model="employeeForm.email"
                                            :class="{
                                                'is-invalid':
                                                    employeeErrors.email,
                                            }"
                                        />
                                        <div
                                            v-if="employeeErrors.email"
                                            class="invalid-feedback"
                                        >
                                            {{ employeeErrors.email }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label"
                                            >Address (Optional)</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="employeeForm.address"
                                        />
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-success mt-3"
                                    :disabled="employeeStatus === 0"
                                >
                                    {{
                                        employeeStatus === 1
                                            ? "Save Employee"
                                            : "Saving..."
                                    }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import axios from "axios";
import Master from "../Layout/Master.vue";

export default {
    layout: Master,
    data() {
        return {
            employees: [],
            employeeForm: {
                name: "",
                email: "",
                phone: "",
                address: "",
                id: "",
                commission: "",
            },
            employeeErrors: {},
            employeeStatus: 1,
        };
    },
    created() {
        this.fetchEmployees();
    },
    methods: {
        openModal(employee = null) {
            if (employee) {
                this.employeeForm = { ...employee };
            } else {
                this.clearFields();
            }
            const modalElement = document.getElementById("employeeModal");
            const modalInstance = new bootstrap.Modal(modalElement);
            modalInstance.show();
        },
        clearFields() {
            this.employeeForm = {
                name: "",
                email: "",
                phone: "",
                address: "",
                commission: "",
                id: null,
            };
            this.employeeErrors = {};
        },
        editEmployee(employee) {
            this.openModal(employee);
        },
        fetchEmployees() {
            axios
                .get(route("api.employees.fetch"))
                .then((response) => {
                    this.employees = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        validateEmployeeForm() {
            this.employeeErrors = {};
            if (!this.employeeForm.name)
                this.employeeErrors.name = "Name is required.";
            if (!this.employeeForm.email)
                this.employeeErrors.email = "Email is required.";
            if (!this.employeeForm.commission)
                this.employeeErrors.commission = "Commission is required.";
            if (!this.employeeForm.phone)
                this.employeeErrors.phone = "Phone Number is required.";
        },
        submitEmployee() {
            this.validateEmployeeForm();
            if (Object.keys(this.employeeErrors).length > 0) return;

            this.employeeStatus = 0;
            const url = route("api.employee.store");
            const method = "post";

            axios({
                method,
                url,
                data: this.employeeForm,
            })
                .then(() => {
                    toastr.success("Employee saved successfully.");
                    this.fetchEmployees();
                    this.employeeStatus = 1;
                    this.clearFields();
                    this.$refs.closeModal.click();
                })
                .catch((error) => {
                    this.employeeStatus = 1;
                    toastr.error(error.response.data.message);
                    if (error.response.data.errors) {
                        this.employeeErrors = error.response.data.errors;
                    }
                });
        },
    },
};
</script>

<style lang="scss">
.invalid-feedback {
    display: block !important;
}
</style>
