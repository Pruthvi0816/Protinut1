<?php include("header.php"); ?>

<?php
// Handle Add Blog
if (isset($_POST['add_blog'])) {
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $content = mysqli_real_escape_string($link, $_POST['content']);
    $author = mysqli_real_escape_string($link, $_POST['author'] ?: 'Admin');

    $category_id = (int) $_POST['category_id'];
    $media_type = mysqli_real_escape_string($link, $_POST['media_type']);

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/img/blog/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = 'assets/img/blog/' . $filename;
        }
    }
    $image_esc = mysqli_real_escape_string($link, $image);

    $sql = "INSERT INTO blogs (category_id, title, content, author, image, media_type) VALUES ($category_id, '$title', '$content', '$author', '$image_esc', '$media_type')";
    if (mysqli_query($link, $sql)) {
        echo "<script>window.location.href='blogs.php?success=Blog Post Added'</script>";
    } else {
        echo "<script>window.location.href='blogs.php?error=Failed to Add Blog Post'</script>";
    }
}

// Handle Edit Blog
if (isset($_POST['edit_blog'])) {
    $id = (int) $_POST['id'];
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $content = mysqli_real_escape_string($link, $_POST['content']);
    $author = mysqli_real_escape_string($link, $_POST['author'] ?: 'Admin');

    $category_id = (int) $_POST['category_id'];
    $media_type = mysqli_real_escape_string($link, $_POST['media_type']);

    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/img/blog/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_esc = mysqli_real_escape_string($link, 'assets/img/blog/' . $filename);
            $image_sql = ", image='$image_esc'";
        }
    }

    $sql = "UPDATE blogs SET category_id=$category_id, title='$title', content='$content', author='$author', media_type='$media_type' $image_sql WHERE id=$id";
    if (mysqli_query($link, $sql)) {
        echo "<script>window.location.href='blogs.php?success=Blog Post Updated'</script>";
    } else {
        echo "<script>window.location.href='blogs.php?error=Failed to Update Blog Post'</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($link, "DELETE FROM blogs WHERE id=$id");
    echo "<script>window.location.href='blogs.php?success=Blog Post Deleted'</script>";
    exit;
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Blog Posts</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Blog Posts</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                    <i class="bx bx-plus"></i> Add Post
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Media</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($link, "SELECT * FROM blogs ORDER BY id DESC");
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $cat_id = (int)$row['category_id'];
                                            $cat_name = 'Uncategorized';
                                            if ($cat_id > 0) {
                                                $cat_q = mysqli_query($link, "SELECT name FROM blog_categories WHERE id = $cat_id");
                                                if ($cat_res = mysqli_fetch_assoc($cat_q)) {
                                                    $cat_name = $cat_res['name'];
                                                }
                                            }
                                            echo htmlspecialchars($cat_name);
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($row['image']): ?>
                                                <?php if ($row['media_type'] == 'video'): ?>
                                                    <video src="../<?php echo htmlspecialchars($row['image']); ?>" width="60" height="40" 
                                                        style="object-fit:cover;border-radius:4px;" muted></video>
                                                <?php else: ?>
                                                    <img src="../<?php echo htmlspecialchars($row['image']); ?>" width="60" height="40"
                                                        style="object-fit:cover;border-radius:4px;">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">No Media</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['title']); ?>">
                                                <?php echo htmlspecialchars($row['title']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-blog-btn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-category_id="<?php echo $row['category_id']; ?>"
                                                data-media_type="<?php echo $row['media_type']; ?>"
                                                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                data-content="<?php echo htmlspecialchars($row['content']); ?>"
                                                data-author="<?php echo htmlspecialchars($row['author']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#editBlogModal">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            <a href="delete_action.php?type=blog&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No blog posts found.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" name="author" value="Admin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content *</label>
                        <textarea class="form-control" name="content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" required>
                            <?php
                            $cats = mysqli_query($link, "SELECT * FROM blog_categories");
                            while ($c = mysqli_fetch_assoc($cats)) {
                                echo "<option value='{$c['id']}'>{$c['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Media Type *</label>
                        <select class="form-select" name="media_type" id="add_media_type" required>
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" id="media_label">Image/Video Upload</label>
                        <input type="file" class="form-control" name="image" accept="image/*,video/*" id="media_input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_blog" class="btn btn-primary">Add Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Blog Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editBlog_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" id="editBlog_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" name="author" id="editBlog_author">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content *</label>
                        <textarea class="form-control" name="content" id="editBlog_content" rows="6"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" id="editBlog_category" required>
                            <?php
                            $cats = mysqli_query($link, "SELECT * FROM blog_categories");
                            while ($c = mysqli_fetch_assoc($cats)) {
                                echo "<option value='{$c['id']}'>{$c['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Media Type *</label>
                        <select class="form-select" name="media_type" id="editBlog_media_type" required>
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Media (leave blank to keep current)</label>
                        <input type="file" class="form-control" name="image" accept="image/*,video/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_blog" class="btn btn-primary">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-blog-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('editBlog_id').value = this.dataset.id;
                document.getElementById('editBlog_title').value = this.dataset.title;
                document.getElementById('editBlog_content').value = this.dataset.content;
                document.getElementById('editBlog_author').value = this.dataset.author;
                document.getElementById('editBlog_category').value = this.dataset.category_id;
                document.getElementById('editBlog_media_type').value = this.dataset.media_type;
            });
        });
        // Media type label toggling
        const addMediaType = document.getElementById('add_media_type');
        const mediaLabel = document.getElementById('media_label');
        if(addMediaType && mediaLabel) {
            addMediaType.addEventListener('change', function() {
                mediaLabel.innerText = this.value === 'video' ? 'Video Upload' : 'Image Upload';
            });
        }

        const editMediaType = document.getElementById('editBlog_media_type');
        if(editMediaType) {
            editMediaType.addEventListener('change', function() {
                // You might want to update the edit label too, but it's usually clear
            });
        }
    });
</script>

<?php include("footer.php"); ?>
