<template>
    <button
        v-if="deleteId"
        type="button"
        class="btn btn-sm fs-6"
        data-bs-toggle="modal"
        :data-bs-target="'#deleteModal-' + deleteId"
        title="Delete"
    >
        <i class="bi bi-trash"></i>
    </button>

    <!-- delete Modal -->
    <div class="modal fade" :id="'deleteModal-' + deleteId" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="block-content text-center p-4">
                        <h2>
                            Are you sure?
                        </h2>

                        <button
                            v-if="deleteId"
                            type="button"
                            class="btn btn-sm btn-success me-2"
                            data-bs-dismiss="modal"
                            @click="$emit('deleteThis', deleteId)"
                        >
                            Yes, delete it!
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-dark"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["deleteId"],
    emits: ["deleteThis"],
};
</script>
