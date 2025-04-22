<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">{{ country }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">TTC Global</a>
                        </li>
                        <li class="breadcrumb-item">{{ country }}</li>
                        <li class="breadcrumb-item active">Individual Visas</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#visarecordmodal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New Record
                </button>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">
                        Individual Records
                    </h5>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="ledgerTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>Name</th>
                                    <th>Phone #</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Tracking ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-white">
                                <tr
                                    v-for="(visarecord, index) in VisasRecords"
                                    :key="visarecord.id"
                                >
                                    <td :class="getRowClass(visarecord.status)">
                                        {{ index + 1 }}
                                    </td>

                                    <td :class="getRowClass(visarecord.status)">
                                        <a
                                            href="#"
                                            class="theme-text-color"
                                            :class="
                                                getRowClass(visarecord.status)
                                            "
                                            @click="openModal(visarecord)"
                                        >
                                            {{ visarecord.full_name }}
                                        </a>
                                    </td>
                                    <td :class="getRowClass(visarecord.status)">
                                        {{ visarecord.phone_number }}
                                    </td>
                                    <td :class="getRowClass(visarecord.status)">
                                        {{ visarecord.status }}
                                    </td>
                                    <td :class="getRowClass(visarecord.status)">
                                        {{ visarecord.amount }}
                                    </td>
                                    <td :class="getRowClass(visarecord.status)">
                                        {{ visarecord.tracking_id }}
                                    </td>
                                    <td :class="getRowClass(visarecord.status)">
                                        <button
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#visarecordmodal"
                                            @click="editRecord(visarecord)"
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

            <!-- Bootstrap Modal -->
            <div
                class="modal fade"
                id="visaModal"
                tabindex="-1"
                aria-labelledby="modalTitle"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">
                                {{ selectedVisa.full_name }}
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Phone Number</th>
                                            <td>
                                                {{ selectedVisa.phone_number }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Status</th>
                                            <td>{{ selectedVisa.status }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Amount</th>
                                            <td>{{ selectedVisa.amount }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Visa Fee</th>
                                            <td>{{ selectedVisa.visa_fee }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tracking ID</th>
                                            <td>
                                                {{ selectedVisa.tracking_id }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Email</th>
                                            <td>{{ selectedVisa.gmail }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Gender</th>
                                            <td>{{ selectedVisa.gender }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Date</th>
                                            <td>{{ selectedVisa.date }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                Pak Visa Password
                                            </th>
                                            <td>
                                                {{
                                                    selectedVisa.pak_visa_password
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Gmail Password</th>
                                            <td>
                                                {{
                                                    selectedVisa.gmail_password
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Applied By</th>
                                            <td>
                                                {{ selectedVisa.added_by_user }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="modal fade"
                id="visarecordmodal"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="section-title">
                                <h5 class="c-theme-red">
                                    Individual Apply Form
                                </h5>
                            </div>
                            <button
                                ref="closeThisModal"
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Single Form Tab -->

                                <div>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label"
                                                >{{ "Date"
                                                }}<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="individualForm.date"
                                                :max="today"
                                                id="date"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.date,
                                                }"
                                            />
                                            <div
                                                v-if="individualFormErrors.date"
                                                class="invalid-feedback"
                                            >
                                                {{ individualFormErrors.date }}
                                            </div>
                                        </div>

                                        <!-- Full Name -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_full_name"
                                                class="form-label"
                                                >Full Name<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.full_name
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.full_name,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.full_name
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.full_name
                                                }}
                                            </div>
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_phone_number"
                                                class="form-label"
                                                >Phone Number<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.phone_number
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.phone_number,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.phone_number
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.phone_number
                                                }}
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_status"
                                                class="form-label"
                                                >Status<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >

                                            <Multiselect
                                                v-model="individualForm.status"
                                                :options="statusOptions"
                                                :searchable="true"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.status,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.status
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.status
                                                }}
                                            </div>
                                        </div>

                                        <!-- Tracking ID -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_tracking_id"
                                                class="form-label"
                                                >Tracking ID<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.tracking_id
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.tracking_id,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.tracking_id
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.tracking_id
                                                }}
                                            </div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_amount"
                                                class="form-label"
                                                >Amount<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="individualForm.amount"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.amount,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.amount
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.amount
                                                }}
                                            </div>
                                        </div>

                                        <!-- Visa Fee -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="visa_fee"
                                                class="form-label"
                                                >Visa Fee<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="
                                                    individualForm.visa_fee
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.visa_fee,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.visa_fee
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.visa_fee
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gmail -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gmail"
                                                class="form-label"
                                                >Gmail<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="email"
                                                class="form-control"
                                                v-model="individualForm.gmail"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.gmail,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.gmail
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{ individualFormErrors.gmail }}
                                            </div>
                                        </div>

                                        <!-- Pak Visa Password -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_pak_visa_password"
                                                class="form-label"
                                                >Pak Visa Password<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.pak_visa_password
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.pak_visa_password,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.pak_visa_password
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.pak_visa_password
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gmail Password -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gmail_password"
                                                class="form-label"
                                                >Gmail Password<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="gmail"
                                                class="form-control"
                                                v-model="
                                                    individualForm.gmail_password
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.gmail_password,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.gmail_password
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.gmail_password
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gender"
                                                class="form-label"
                                                >Gender<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <Multiselect
                                                v-model="individualForm.gender"
                                                :options="genderOptions"
                                                :searchable="true"
                                                :class="{
                                                    'invalid-bg':
                                                        individualFormErrors.gender,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.gender
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.gender
                                                }}
                                            </div>
                                        </div>

                                        <!-- referral -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="referral"
                                                class="form-label"
                                                >Referrals
                                            </label>
                                            <Multiselect
                                                v-model="
                                                    individualForm.referral
                                                "
                                                :options="pluckedReferrals"
                                                :searchable="true"
                                                :class="{
                                                    'invalid-bg':
                                                        individualFormErrors.referral,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.referral
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.referral
                                                }}
                                            </div>
                                        </div>

                                        <!-- employee -->
                                    </div>
                                    <!-- Submit Button -->
                                    <button
                                        type="button"
                                        class="btn btn-success mt-3"
                                        @click="submitIndividualForm"
                                        :disabled="individualFormStatus === 0"
                                    >
                                        {{
                                            individualFormStatus === 1
                                                ? "Save"
                                                : "Saving..."
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import Master from "../Layout/Master.vue";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";

export default {
    layout: Master,
    components: {
        Multiselect,
        Datepicker,
    },

    props: ["country"],
    created() {
        this.fetchRecords();
        this.referralsPluck();
        this.employeesPluck();
    },
    data() {
        return {
            today: this.getPakistanDate(),
            dataTable: null,
            selectedVisa: null,
            selectedVisa: {},
            pluckedReferrals: [],
            pluckedEmployees: [],
            tab: "single",
            individualForm: {
                id: "",
                full_name: "",
                phone_number: "",
                status: "",
                amount: "",
                tracking_id: "",
                visa_fee: "",
                gmail: "",
                pak_visa_password: "",
                gmail_password: "",
                gender: "",
                date: "",
                family_members: 1,
                referral: "",
                referral_commission: "",
                employee: [],
            },

            individualFormErrors: {},

            formErrors: {},
            individualFormStatus: 1, // or 0 (1 = active, 0 = inactive)

            statusOptions: [
                "Initial",
                "Applied",
                "Refunded",
                "Rejected",
                "Approved",
            ], // Status options
            genderOptions: ["Male", "Female", "Other"],
            VisasRecords: [],
            filters: {
                full_name: "",
                tracking_id: "",
                gmail: "",
                phone_number: "",
            },
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.initializeDataTable();
        });
    },
    methods: {
        getPakistanDate() {
            // Get the current date in Pakistan Standard Time (Asia/Karachi)
            let formatter = new Intl.DateTimeFormat("en-CA", {
                timeZone: "Asia/Karachi",
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            });

            // Format it correctly for the <input type="date">
            let parts = formatter.formatToParts(new Date());

            let year = parts.find((p) => p.type === "year").value;
            let month = parts.find((p) => p.type === "month").value;
            let day = parts.find((p) => p.type === "day").value;

            return `${year}-${month}-${day}`; // YYYY-MM-DD format
        },
        submitIndividualForm() {
            this.validateIndividualForm();
            if (Object.keys(this.individualFormErrors).length > 0) {
                return; // Stop if there are errors
            }

            if (
                this.individualForm.referral == null ||
                this.individualForm.referral == "" ||
                this.individualForm.referral == undefined
            ) {
                this.individualForm.referral = "";
            } else {
                this.individualForm.referral = this.individualForm.referral;
            }
            this.individualFormStatus = 0; // Mark as saving

            axios
                .post(
                    route("api.individual.customer.store"),
                    this.individualForm,
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$page.props.auth_token,
                        },
                    }
                )
                .then(() => {
                    toastr.success("Record saved successfully.");
                    this.$refs.closeThisModal.click();
                    this.fetchRecords(); // Fetch records after the form submission
                    this.individualFormStatus = 1;
                })
                .catch((error) => {
                    this.individualFormStatus = 1;
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },

        fetchRecords() {
            axios
                .get(route("api.individual.customer.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.VisasRecords = response.data;

                    // Destroy DataTable if it already exists
                    if (this.dataTable) {
                        this.dataTable.destroy();
                        this.dataTable = null;
                    }

                    // Reinitialize the DataTable after DOM update
                    this.$nextTick(() => {
                        this.initializeDataTable();
                    });
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },

        initializeDataTable() {
            this.$nextTick(() => {
                // Initialize the DataTable after DOM is updated
                this.dataTable = $("#ledgerTable").DataTable({
                    responsive: true,
                    autoWidth: false,
                    paging: true,
                    searching: true,
                    ordering: true,
                    lengthMenu: [15, 30, 100], // Set the new limit options
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous",
                        },
                    },
                });
            });
        },
        getRowClass(status) {
            switch (status) {
                case "Applied":
                    return "bg-light-gray"; // Add a corresponding class in CSS
                case "Refunded":
                    return "bg-orange";
                case "Rejected":
                    return "bg-red";
                case "Approved":
                    return "bg-green";
                default:
                    return "";
            }
        },

        openModal(visa) {
            this.selectedVisa = visa;
            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },
        editRecord(visarecord) {
            this.clearFields();
            this.individualForm = { ...visarecord };
        },

        clearFields() {
            this.individualForm = {
                id: "",
                full_name: "",
                phone_number: "",
                status: "",
                amount: "",
                tracking_id: "",
                visa_fee: "",
                gmail: "",
                pak_visa_password: "",
                gmail_password: "",
                gender: "",
                date: "",
                family_members: 1,
                referral: "",
                referral_commission: "",
                employee: [],
            };

            // Reset the form errors
            this.individualFormErrors = {};
        },

        deleteThis(id) {
            axios
                .delete(route("api.customer.visa.record.delete", id))
                .then(() => {
                    this.fetchRecords();
                    toastr.success("Transaction entry deleted successfully.");
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        validateIndividualForm() {
            this.individualFormErrors = {}; // Reset errors

            let fields = [
                "full_name",
                "phone_number",
                "status",
                "amount",
                "visa_fee",
                "tracking_id",
                "gmail",
                "pak_visa_password",
                "gmail_password",
                "gender",
                "date",
            ];

            // Basic required field validation
            fields.forEach((field) => {
                if (!this.individualForm[field]) {
                    this.individualFormErrors[field] = `${field.replace(
                        /_/g,
                        " "
                    )} is required`;
                }
            });

            // Ensure amount is greater than visa_fee
            if (
                this.individualForm.amount &&
                this.individualForm.visa_fee &&
                parseFloat(this.individualForm.amount) <=
                    parseFloat(this.individualForm.visa_fee)
            ) {
                this.individualFormErrors.amount =
                    "Amount must be greater than Visa Fee";
            }
        },

        referralsPluck() {
            axios
                .get(route("api.referrals.pluck"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.pluckedReferrals = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        employeesPluck() {
            axios
                .get(route("api.employees.pluck"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.pluckedEmployees = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
    },
};
</script>

<style lang="scss">
@import "@vueform/multiselect/themes/default.css";
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
.border {
    border-radius: 0.25rem;
    border-color: #3c444d !important;
}
.bg-secondary {
    background-color: #6c757d !important; /* Gray for Initial */
}

.bg-light-gray {
    background-color: #f2f2f2 !important;
}
.bg-orange {
    background-color: #ff7900 !important;
    color: white !important; /* Optional: Make text readable */
}
.bg-red {
    background-color: #c00000 !important;
    color: white !important;
}
.bg-green {
    background-color: #00af50 !important;
    color: white !important;
}
</style>
