<?php include("header.php"); ?>

<?php
// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $price = (float) $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : 'NULL';
    $category = mysqli_real_escape_string($link, $_POST['category']);
    $stock = (int) $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
    $status = mysqli_real_escape_string($link, $_POST['status']);

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/img/shop/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = 'assets/img/shop/' . $filename;
        }
    }

    $sale_price_sql = ($sale_price === 'NULL') ? 'NULL' : $sale_price;
    $image_esc = mysqli_real_escape_string($link, $image);
    $sql = "INSERT INTO products (name, description, price, sale_price, image, category, stock, is_featured, is_best_seller, status) 
            VALUES ('$name', '$description', $price, $sale_price_sql, '$image_esc', '$category', $stock, $is_featured, $is_best_seller, '$status')";

    if (mysqli_query($link, $sql)) {
        echo "<script>window.location.href='products.php?success=Product Added Successfully'</script>";
    } else {
        echo "<script>window.location.href='products.php?error=Failed to Add Product'</script>";
    }
}

// Handle Edit Product
if (isset($_POST['edit_product'])) {
    $id = (int) $_POST['id'];
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $price = (float) $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : 'NULL';
    $category = mysqli_real_escape_string($link, $_POST['category']);
    $stock = (int) $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
    $status = mysqli_real_escape_string($link, $_POST['status']);

    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/img/shop/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_esc = mysqli_real_escape_string($link, 'assets/img/shop/' . $filename);
            $image_sql = ", image='$image_esc'";
        }
    }

    $sale_price_sql = ($sale_price === 'NULL') ? 'NULL' : $sale_price;
    $sql = "UPDATE products SET name='$name', description='$description', price=$price, sale_price=$sale_price_sql, 
            category='$category', stock=$stock, is_featured=$is_featured, is_best_seller=$is_best_seller, status='$status' $image_sql WHERE id=$id";

    if (mysqli_query($link, $sql)) {
        echo "<script>window.location.href='products.php?success=Product Updated Successfully'</script>";
    } else {
        echo "<script>window.location.href='products.php?error=Failed to Update Product'</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $del = mysqli_query($link, "DELETE FROM products WHERE id=$id");
    if ($del) {
        echo "<script>window.location.href='products.php?success=Product Deleted'</script>";
    } else {
        $err = mysqli_real_escape_string($link, mysqli_error($link));
        echo "<script>alert('Failed to delete: $err'); window.location.href='products.php';</script>";
    }

    exit;
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Products</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bx bx-plus"></i> Add Product
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($link, "SELECT * FROM products ORDER BY id DESC");
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <?php if ($row['image']): ?>
                                                <img src="../<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50"
                                                    style="object-fit:cover; border-radius:5px;">
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                                        <td><?php echo $row['sale_price'] ? '₹' . number_format($row['sale_price'], 2) : '-'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                                        <td><?php echo $row['stock']; ?></td>
                                        <td>
                                            <span
                                                class="badge <?php echo $row['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                data-description="<?php echo htmlspecialchars($row['description'] ?? ''); ?>"
                                                data-price="<?php echo $row['price']; ?>"
                                                data-sale_price="<?php echo $row['sale_price'] ?? ''; ?>"
                                                data-category="<?php echo htmlspecialchars($row['category'] ?? ''); ?>"
                                                data-stock="<?php echo $row['stock']; ?>"
                                                data-is_featured="<?php echo $row['is_featured']; ?>"
                                                data-is_best_seller="<?php echo $row['is_best_seller']; ?>"
                                                data-status="<?php echo $row['status']; ?>" data-bs-toggle="modal"
                                                data-bs-target="#editProductModal">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            <a href="delete_action.php?type=product&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No products found. Click 'Add Product' to create one.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category"
                                placeholder="e.g. Protein, Pre-Workout">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sale Price</label>
                            <input type="number" step="0.01" class="form-control" name="sale_price">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="addFeatured">
                                <label class="form-check-label" for="addFeatured">Featured</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_best_seller"
                                    id="addBestSeller">
                                <label class="form-check-label" for="addBestSeller">Best Seller</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" id="edit_category">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sale Price</label>
                            <input type="number" step="0.01" class="form-control" name="sale_price"
                                id="edit_sale_price">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="edit_stock">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image (leave blank to keep current)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="edit_featured">
                                <label class="form-check-label" for="edit_featured">Featured</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_best_seller"
                                    id="edit_best_seller">
                                <label class="form-check-label" for="edit_best_seller">Best Seller</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_description').value = this.dataset.description;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_sale_price').value = this.dataset.sale_price || '';
                document.getElementById('edit_category').value = this.dataset.category;
                document.getElementById('edit_stock').value = this.dataset.stock;
                document.getElementById('edit_status').value = this.dataset.status;
                document.getElementById('edit_featured').checked = this.dataset.is_featured == '1';
                document.getElementById('edit_best_seller').checked = this.dataset.is_best_seller == '1';
            });
        });
    });
</script>

<?php include("footer.php"); ?>
