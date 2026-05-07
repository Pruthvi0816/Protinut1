<?php require_once 'connection.php'; ?>
<?php include 'header.php'; ?>
<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">Blog</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">Blog</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- blog start -->
    <section class="blog pt-115 pb-385">
        <div class="container">
            <div class="row mt-none-30">
                <div class="col-lg-8 mt-30">
                    <div class="blog-post-wrapper">
                        <?php
                        $search_query = "";
                        $where_clause = " WHERE 1=1 ";
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search_query = mysqli_real_escape_string($link, $_GET['search']);
                            $where_clause .= " AND title LIKE '%$search_query%' ";
                        }
                        if (isset($_GET['cat']) && !empty($_GET['cat'])) {
                            $cat_id = (int)$_GET['cat'];
                            $where_clause .= " AND category_id = $cat_id ";
                            $cat_name_q = mysqli_query($link, "SELECT name FROM blog_categories WHERE id = $cat_id");
                            $cat_name_res = mysqli_fetch_assoc($cat_name_q);
                            echo "<h4 class='mb-40'>Category: '" . htmlspecialchars($cat_name_res['name'] ?? 'Unknown') . "'</h4>";
                        }
                        if ($search_query) {
                            echo "<h4 class='mb-40'>Search Results for: '" . htmlspecialchars($_GET['search']) . "'</h4>";
                        }
                        
                        $blog_res = mysqli_query($link, "SELECT * FROM blogs $where_clause ORDER BY id DESC");
                        if ($blog_res && mysqli_num_rows($blog_res) > 0) {
                            while ($blog = mysqli_fetch_assoc($blog_res)) {
                                $blog_image = $blog['image'] ?: 'assets/img/blog/post_01.jpg';
                                $blog_date = date('F d, Y', strtotime($blog['created_at']));
                                // Basic excerpt logic
                                $excerpt = substr(strip_tags($blog['content']), 0, 180) . '...';
                                ?>
                                <article class="single-post-item">
                                    <div class="post-thumbnail-wrapper">
                                        <a href="blog-single.php?id=<?php echo $blog['id']; ?>">
                                            <?php if ($blog['media_type'] == 'video'): ?>
                                                <div class="video-thumbnail-placeholder" style="position:relative;">
                                                    <img src="<?php echo htmlspecialchars($blog_image); ?>" alt="">
                                                    <div class="play-btn" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:40px;color:#fff;"><i class="fas fa-play-circle"></i></div>
                                                </div>
                                            <?php else: ?>
                                                <img src="<?php echo htmlspecialchars($blog_image); ?>" alt="">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="post-content-wrapper">
                                        <ul class="post-meta ul_li">
                                            <li><span><i class="far fa-user"></i><span
                                                        class="author vcard"><?php echo htmlspecialchars($blog['author']); ?></span></span>
                                            </li>
                                            <li><a href="#!"><i class="far fa-comments"></i> Comments (0)</a></li>
                                            <li><span class="posted-on"><i class="far fa-clock"></i> <a
                                                        href="#!"><?php echo $blog_date; ?></a></span></li>
                                        </ul>
                                        <h3 class="post-title border_effect"><a
                                                href="blog-single.php?id=<?php echo $blog['id']; ?>"><?php echo htmlspecialchars($blog['title']); ?></a>
                                        </h3>
                                        <div class="post-excerpt">
                                            <p><?php echo htmlspecialchars($excerpt); ?></p>
                                        </div>
                                        <div class="post-read-more">
                                            <a class="thm-btn thm-btn--black"
                                                href="blog-single.php?id=<?php echo $blog['id']; ?>">Read More</a>
                                        </div>
                                    </div>
                                </article>
                            <?php }
                        } else {
                            echo "<article class='single-post-item'><p class='text-center'>No blog posts found.</p></article>";
                        } ?>
                    </div>
                </div>
                <div class="col-lg-4 mt-30">
                    <div class="blog-sidebar sidebar-area mt-none-40">
                        <div class="widget mt-40">
                            <h2 class="widget__title">Search</h2>
                            <form class="widget__search" action="#">
                                <input type="text" placeholder="Search...">
                                <button><i class="far fa-search"></i></button>
                            </form>
                        </div>
                        <div class="widget mt-40">
                            <h2 class="widget__title">Related Posts</h2>
                            <div class="widget__post">
                                <?php
                                $related_res = mysqli_query($link, "SELECT * FROM blogs ORDER BY id DESC LIMIT 4");
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
                                    echo "<p>No recent posts.</p>";
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
                                    $active_cat = (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'active_cat' : '';
                                    ?>
                                    <li><a href="blog.php?cat=<?php echo $cat['id']; ?>" class="<?php echo $active_cat; ?>"><?php echo htmlspecialchars($cat['name']); ?> <span>(<?php echo $cat['blog_count']; ?>)</span></a></li>
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
    <!-- blog end -->
</main>
<!-- main area end  -->
<?php include 'footer.php'; ?>
