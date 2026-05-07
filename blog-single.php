<?php require_once 'connection.php'; ?>
<?php include 'header.php'; ?>
<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">blog details</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">blog details</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- blog single start -->
    <section class="blog-single pt-115 pb-385">
        <div class="container">
            <div class="row mt-none-30">
                <div class="col-lg-8 mt-30">
                    <div class="blog-post-wrapper">
                        <?php
                        $blog_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
                        $blog_res = mysqli_query($link, "SELECT * FROM blogs WHERE id = $blog_id");
                        if ($blog_res && mysqli_num_rows($blog_res) > 0) {
                            $blog = mysqli_fetch_assoc($blog_res);
                            $blog_image = $blog['image'] ?: 'assets/img/blog/post_02.jpg';
                            $blog_date = date('F d, Y', strtotime($blog['created_at']));
                            ?>
                            <article class="post-details">
                                <div class="post-thumb">
                                    <?php if ($blog['media_type'] == 'video'): ?>
                                        <div class="ratio ratio-16x9">
                                            <video controls style="width:100%; border-radius:10px;">
                                                <source src="<?php echo htmlspecialchars($blog_image); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($blog_image); ?>" alt="" style="border-radius:10px;">
                                    <?php endif; ?>
                                </div>
                                <ul class="post-meta ul_li">
                                    <li><span><i class="far fa-user"></i><span
                                                    class="author vcard"><?php echo htmlspecialchars($blog['author']); ?></span></span>
                                    </li>
                                    <li><a href="javascript:void(0)" id="toggle_comments"><i class="far fa-comments"></i> Comments (<span id="comment_count">0</span>)</a></li>
                                    <li><span class="posted-on"><i class="far fa-clock"></i> <a
                                                    href="#!"><?php echo $blog_date; ?></a></span></li>
                                    <li>
                                        <button id="like_btn" data-id="<?php echo $blog['id']; ?>" class="btn btn-link p-0 text-decoration-none" style="color:inherit;">
                                            <i class="far fa-heart" id="like_icon"></i> <span id="likes_text"><?php echo $blog['likes_count']; ?></span> Likes
                                        </button>
                                    </li>
                                </ul>
                                <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                                </div>
                            </article>
                        <?php } else {
                            echo "<div class='alert alert-warning'>Blog post not found. <a href='blog.php'>Return to Blog</a></div>";
                        } ?>
                        <div class="post-footer mt-10 mb-40 ul_li_between">
                            <div class="post-tags ul_li mt-20">
                                <h5 class="tag-title">Tags:</h5>
                                <span class="tags-links">
                                    <a href="#!" rel="tag">Health</a>
                                    <a href="#!" rel="tag">Nutrition</a>
                                    <a href="#!" rel="tag">Supplements</a>
                                </span>
                            </div>
                            <div class="social-share ul_li mt-20">
                                <h5 class="title">Share:</h5>
                                <ul class="post-share ul_li">
                                    <li>
                                        <a href="#">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#!">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#!">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#!">
                                            <i class="fab fa-pinterest"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row" id="comments_section" style="display: none;">
                            <div class="col-xl-12">
                                <div class="post-comments mt-50">
                                    <h2 class="title mb-25"><span id="comment_title_count">0</span> Comments</h2>
                                    <div class="latest__comments" id="comments_list">
                                        <!-- Comments will be loaded here via AJAX -->
                                        <p class="text-muted">Loading comments...</p>
                                    </div>
                                </div>
                                <div class="comments-form mt-50">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <div class="comment-heading">
                                            <h2 class="title">Post a Comment</h2>
                                            <p>Logged in as <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
                                        </div>
                                        <form class="xb-item--form" id="commentForm">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="xb-item--field">
                                                        <textarea name="comment" cols="30" rows="3" placeholder="Write Your Comment *" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-20">
                                                    <button class="thm-btn thm-btn--black" type="submit" id="submitComment">Post Comment</button>
                                                    <div id="comment_feedback" class="mt-2" style="display:none;"></div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="comment-heading text-center py-4" style="background: #f8f9fa; border-radius: 8px;">
                                            <h4 class="mb-3">Login to join the conversation</h4>
                                            <a href="login.php" class="thm-btn thm-btn--black">Login Now</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const blogId = <?php echo $blog['id']; ?>;
                            loadComments();

                            // Toggle Comments Section
                            document.getElementById('toggle_comments').addEventListener('click', function() {
                                const section = document.getElementById('comments_section');
                                if (section.style.display === 'none') {
                                    section.style.display = 'block';
                                    section.scrollIntoView({ behavior: 'smooth' });
                                } else {
                                    section.style.display = 'none';
                                }
                            });

                            // Load Comments
                            function loadComments() {
                                fetch('blog_action.php?action=get_comments&blog_id=' + blogId)
                                    .then(response => response.json())
                                    .then(data => {
                                        const list = document.getElementById('comments_list');
                                        document.getElementById('comment_count').innerText = data.length;
                                        document.getElementById('comment_title_count').innerText = data.length;
                                        
                                        if (data.length === 0) {
                                            list.innerHTML = '<p class="text-muted">No comments yet. Be the first to comment!</p>';
                                            return;
                                        }

                                        let html = '<ul class="list-unstyled mb-0">';
                                        data.forEach(c => {
                                            html += `<li>
                                                <div class="comments-box">
                                                    <div class="comments-avatar">
                                                        <img src="https://ui-avatars.com/api/?name=${c.user_name}&background=random" alt="">
                                                    </div>
                                                    <div class="comments-text">
                                                        <div class="avatar-name">
                                                            <h5>${c.user_name}</h5>
                                                            <span>${c.created_at}</span>
                                                        </div>
                                                        <p>${c.comment}</p>
                                                    </div>
                                                </div>
                                            </li>`;
                                        });
                                        html += '</ul>';
                                        list.innerHTML = html;
                                    });
                            }

                            // Handle Comment Post
                            const commentForm = document.getElementById('commentForm');
                            if (commentForm) {
                                commentForm.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    const btn = document.getElementById('submitComment');
                                    const feedback = document.getElementById('comment_feedback');
                                    btn.disabled = true;
                                    btn.innerText = 'Posting...';

                                    const formData = new FormData(this);
                                    formData.append('action', 'post_comment');

                                    fetch('blog_action.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        btn.disabled = false;
                                        btn.innerText = 'Post Comment';
                                        feedback.style.display = 'block';
                                        if (data.status === 'success') {
                                            feedback.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                                            this.reset();
                                            loadComments();
                                            setTimeout(() => feedback.style.display = 'none', 3000);
                                        } else {
                                            feedback.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                                        }
                                    });
                                });
                            }

                            // Handle Like
                            document.getElementById('like_btn').addEventListener('click', function() {
                                const id = this.dataset.id;
                                fetch('blog_action.php?action=like&blog_id=' + id)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            document.getElementById('likes_text').innerText = data.likes_count;
                                            const icon = document.getElementById('like_icon');
                                            icon.classList.remove('far');
                                            icon.classList.add('fas');
                                            icon.style.color = 'red';
                                        } else if (data.status === 'already_liked') {
                                            // Optional: Handle unlike logic?
                                        }
                                    });
                            });
                        });
                        </script>
                    <div class="blog-sidebar sidebar-area mt-none-40">
                        <div class="widget mt-40">
                            <h2 class="widget__title">Search</h2>
                            <form class="widget__search" action="blog.php" method="GET">
                                <input type="text" name="search" placeholder="Search...">
                                <button><i class="far fa-search"></i></button>
                            </form>
                        </div>
                        <div class="widget mt-40">
                            <h2 class="widget__title">Related Posts</h2>
                            <div class="widget__post">
                                <?php
                                $current_cat = $blog['category_id'];
                                $related_res = mysqli_query($link, "SELECT * FROM blogs WHERE id != $blog_id ORDER BY (category_id = $current_cat) DESC, id DESC LIMIT 4");
                                if ($related_res && mysqli_num_rows($related_res) > 0) {
                                    while ($related = mysqli_fetch_assoc($related_res)) {
                                        $rel_img = $related['image'] ?: 'assets/img/blog/post_03.jpg';
                                        $rel_date = date('d M/y', strtotime($related['created_at']));
                                        ?>
                                        <div class="widget__post-item ul_li">
                                            <div class="post-thumb">
                                                <a href="blog-single.php?id=<?php echo $related['id']; ?>"><img
                                                            src="<?php echo htmlspecialchars($rel_img); ?>" alt=""></a>
                                            </div>
                                            <div class="post-content">
                                                <div class="post-meta">
                                                    <a href="#!"><i
                                                                class="invite-text-gr-color far fa-user"></i>By <?php echo htmlspecialchars($related['author']); ?></a>
                                                    <a href="#!"><i
                                                                class="invite-text-gr-color far fa-calendar"></i><?php echo $rel_date; ?></a>
                                                </div>
                                                <h4 class="post-title border-effect-2"><a
                                                            href="blog-single.php?id=<?php echo $related['id']; ?>"><?php echo htmlspecialchars($related['title']); ?></a>
                                                </h4>
                                            </div>
                                        </div>
                                    <?php }
                                } else {
                                    echo "<p>No other posts.</p>";
                                } ?>
                            </div>
                        </div>
                        <div class="widget mt-40">
                            <h2 class="widget__title">
                                <span>Explore Categories</span>
                            </h2>
                            <ul class="widget__category list-unstyled">
                                <?php
                                $categories_q = mysqli_query($link, "SELECT c.*, (SELECT COUNT(*) FROM blogs WHERE category_id = c.id) as blog_count FROM blog_categories c");
                                while ($cat = mysqli_fetch_assoc($categories_q)) {
                                    ?>
                                    <li><a href="blog.php?cat=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?> <span>(<?php echo $cat['blog_count']; ?>)</span></a></li>
                                <?php } ?>
                                <li><a href="blog.php">All Categories</a></li>
                            </ul>
                        </div>

                        <div class="widget mt-40">
                            <h2 class="widget__title">
                                <span>Tags</span>
                            </h2>
                            <div class="tagcloud">
                                <a href="#!">energy</a>
                                <a href="#!">fitness</a>
                                <a href="#!">healthy</a>
                                <a href="#!">powders</a>
                                <a href="#!">nutrition</a>
                                <a href="#!">snacks</a>
                                <a href="#!">wellness</a>
                                <a href="#!">powders</a>
                                <a href="#!">diet</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- blog single end -->
</main>
<!-- main area end  -->
<?php include 'footer.php'; ?>
