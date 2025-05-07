<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>{{ "Gmails" }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">Global Vision</a>
                        </li>
                        <!-- <li class="breadcrumb-item">Gmails</li> -->
                        <li class="breadcrumb-item active">Gmails</li>
                    </ol>
                </nav>
            </div>
            <!-- <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#usermodal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New User
                </button>
            </div> -->
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
                                    <th scope="col">{{ "Gmail" }}</th>
                                    <th scope="col">{{ "Password" }}</th>
                                 

                                    <!-- <th scope="col" class="text-center">
                                        {{ "Actions" }}
                                    </th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(user, index) in Gmails"
                                    :key="user.id"
                                >
                                    <th class="text-center">{{ index + 1 }}</th>
                                    <td>{{ user.gmail }}</td>
                                    <td>{{ user.gmail_password }}</td>
                               

                                    <!-- <td class="text-center">
                                        <div class="btn-group">
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
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table with stripped rows -->
                </div>
            </div>
        </section>
        
    </main>
    <!-- End #main -->
</template>

<script>
import Master from "../Layout/Master.vue";
 
import axios from "axios";
export default {
    layout: Master,
    created() {
        this.fetchUsers();
    },
   
    data() {
        return {
            Gmails: [],
             
 
        };
    },
    methods: {
        fetchUsers() {
            axios
                .get(route("api.gmails.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.Gmails = response.data; 
                     console.log(this.Gmails);
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
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
