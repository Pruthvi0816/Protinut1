<?php
include("header.php");

// Fetch current settings
$current_settings = [
    'title' => '',
    'subtitle' => '',
    'button_text' => '',
    'button_link' => '',
    'contact_text' => '',
    'contact_number' => '',
    'bg_image' => '',
    'product_image' => ''
];

$res = mysqli_query($link, "SELECT * FROM hero_settings LIMIT 1");
if ($res && mysqli_num_rows($res) > 0) {
    $current_settings = mysqli_fetch_assoc($res);
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Settings</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Hero Section Configuration</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-9 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-4">
                        <h5 class="mb-4 text-uppercase">Configure Homepage Hero Section</h5>
                        <form action="settings_action.php" method="POST" enctype="multipart/form-data">

                            <div class="row mb-3">
                                <label for="title" class="col-sm-3 col-form-label">Main Title</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?php echo htmlspecialchars($current_settings['title']); ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="subtitle" class="col-sm-3 col-form-label">Subtitle</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="subtitle" name="subtitle"
                                        value="<?php echo htmlspecialchars($current_settings['subtitle']); ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="button_text" class="col-sm-3 col-form-label">Button Text</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="button_text" name="button_text"
                                        value="<?php echo htmlspecialchars($current_settings['button_text']); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="button_link" class="col-sm-3 col-form-label">Button Link</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="button_link" name="button_link"
                                        value="<?php echo htmlspecialchars($current_settings['button_link']); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="contact_text" class="col-sm-3 col-form-label">Contact Subtext</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="contact_text" name="contact_text"
                                        value="<?php echo htmlspecialchars($current_settings['contact_text']); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="contact_number" class="col-sm-3 col-form-label">Contact Number</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="contact_number" name="contact_number"
                                        value="<?php echo htmlspecialchars($current_settings['contact_number']); ?>"
                                        required>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Background Image</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="bg_image" accept="image/*">
                                    <small class="text-muted">Current:
                                        <?php echo htmlspecialchars($current_settings['bg_image']); ?></small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Product/Hero Image</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="product_image" accept="image/*">
                                    <small class="text-muted">Current:
                                        <?php echo htmlspecialchars($current_settings['product_image']); ?></small>
                                </div>
                            </div>

                            <div class="row">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="submit" class="btn btn-primary px-4">Update Settings</button>
                                        <button type="reset" class="btn btn-light px-4">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include("footer.php"); ?>