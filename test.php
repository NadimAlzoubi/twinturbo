<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SweetAlert2 Icons Example</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <script>
    // Success icon example
    Swal.fire({
        icon: "success",
        title: "Operation Successful!",
        text: "Your operation was completed successfully.",
        showConfirmButton: true
    });

    // Error icon example
    Swal.fire({
        icon: "error",
        title: "Error Occurred!",
        text: "There was an error processing your request.",
        showConfirmButton: true
    });

    // Warning icon example
    Swal.fire({
        icon: "warning",
        title: "Warning!",
        text: "Are you sure you want to proceed?",
        showConfirmButton: true
    });

    // Info icon example
    Swal.fire({
        icon: "info",
        title: "Information",
        text: "This is an informational message.",
        showConfirmButton: true
    });

    //question
    Swal.fire({
        icon: "question",
        title: "Are you sure?",
        text: "Do you want to delete this item?",
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            // User clicked 'Yes'
            Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: "Your item has been deleted.",
                showConfirmButton: false,
                timer: 1500
            });
            // Execute the code for the 'true' case here
        } else if (result.isDismissed) {
            // User clicked 'No' or dismissed the dialog
            Swal.fire({
                icon: "info",
                title: "Cancelled",
                text: "Your item is safe.",
                showConfirmButton: false,
                timer: 1500
            });
            // Execute the code for the 'false' case here
        }
    });
    </script>

</body>

</html>




ALTER TABLE sau_offices
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN created_by VARCHAR(255),
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN updated_by VARCHAR(255);


<!-- TODO FIX JS FAILD DATE -->

<!-- TODO FIX JS FAILD DATE -->






<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_service', $lang); ?></h2>
    <form method="post">
        <div class="form-group">
            <label for="driver_name"><?php echo translate('driver_name', $lang); ?></label>
            <input type="text" class="form-control" id="driver_name" required>
        </div>
        <div class="form-group">
            <label for="vehicle_number"><?php echo translate('vehicle_number', $lang); ?></label>
            <input type="text" class="form-control" id="vehicle_number" required>
        </div>
        <div class="form-group">
            <label for="phone_number"><?php echo translate('phone_number', $lang); ?></label>
            <input type="text" class="form-control" id="phone_number">
        </div>
        <div class="form-group">
            <label for="notes"><?php echo translate('notes', $lang); ?></label>
            <textarea class="form-control" id="notes"></textarea>
        </div>
        <div class="form-group">
            <label for="added_by"><?php echo translate('added_by', $lang); ?></label>
            <input type="text" class="form-control" id="added_by" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
    </form>
</div>






<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_service_fee', $lang); ?></h2>
    <form method="post">
        <div class="form-group">
            <label for="service_id"><?php echo translate('service_id', $lang); ?></label>
            <input type="number" min="0" class="form-control" id="service_id" required>
        </div>
        <div class="form-group">
            <label for="description"><?php echo translate('description', $lang); ?></label>
            <textarea class="form-control" id="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="amount"><?php echo translate('amount', $lang); ?></label>
            <input type="number" min="0" step="0.01" class="form-control" id="amount" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
    </form>
</div>






<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_service_fee_type', $lang); ?></h2>
    <form method="post">
        <div class="form-group">
            <label for="fee_name"><?php echo translate('fee_name', $lang); ?></label>
            <input type="text" class="form-control" id="fee_name" required>
        </div>
        <div class="form-group">
            <label for="fee_amount"><?php echo translate('fee_amount', $lang); ?></label>
            <input type="number" min="0" step="0.01" class="form-control" id="fee_amount" required>
        </div>
        <div class="form-group">
            <label for="bank_deduction"><?php echo translate('bank_deduction', $lang); ?></label>
            <input type="number" min="0" step="0.01" class="form-control" id="bank_deduction" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
    </form>
</div>