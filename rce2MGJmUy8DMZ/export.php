<?php
function export_require_admin(mysqli $link): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $uuid = isset($_COOKIE['uuid']) ? (string) $_COOKIE['uuid'] : '';
    if ($uuid === '') {
        header("Location: login.php");
        exit;
    }

    $ok = false;
    $stmt = $link->prepare("SELECT id FROM ods WHERE uuid=? AND status='verified' ORDER BY id DESC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $stmt->bind_result($id);
        $ok = (bool) $stmt->fetch();
        $stmt->close();
    }

    if (!$ok) {
        header("Location: login.php");
        exit;
    }
}

function export_is_sensitive_key(string $key): bool
{
    return (bool) preg_match('/(password|pass|otp|token|salt|secret)/i', $key);
}

function export_row_filter(array $row): array
{
    $out = [];
    foreach ($row as $k => $v) {
        if (export_is_sensitive_key((string) $k)) {
            continue;
        }
        $out[(string) $k] = $v;
    }
    return $out;
}

function export_write_csv_from_sql(mysqli $link, string $sql, $outStream): void
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        fputcsv($outStream, ['error']);
        fputcsv($outStream, [mysqli_error($link)]);
        return;
    }

    $first = mysqli_fetch_assoc($res);
    if (!$first) {
        fputcsv($outStream, ['empty']);
        return;
    }

    $first = export_row_filter($first);
    $headers = array_keys($first);
    fputcsv($outStream, $headers);
    $line = [];
    foreach ($headers as $h) {
        $line[] = isset($first[$h]) ? (string) $first[$h] : '';
    }
    fputcsv($outStream, $line);

    while ($row = mysqli_fetch_assoc($res)) {
        $row = export_row_filter($row);
        $line = [];
        foreach ($headers as $h) {
            $line[] = array_key_exists($h, $row) ? (string) $row[$h] : '';
        }
        fputcsv($outStream, $line);
    }
}

function export_dataset_configs(): array
{
    return [
        'orders' => [
            'label' => 'Orders',
            'sql' => "SELECT * FROM orders ORDER BY id DESC",
            'filename' => 'orders.csv',
        ],
        'order_items' => [
            'label' => 'Order Items',
            'sql' => "SELECT * FROM order_items ORDER BY order_id DESC, id DESC",
            'filename' => 'order_items.csv',
        ],
        'products' => [
            'label' => 'Products',
            'sql' => "SELECT * FROM products ORDER BY id DESC",
            'filename' => 'products.csv',
        ],
        'users' => [
            'label' => 'Customers',
            'sql' => "SELECT * FROM users ORDER BY id DESC",
            'filename' => 'customers.csv',
        ],
        'blogs' => [
            'label' => 'Blog Posts',
            'sql' => "SELECT * FROM blogs ORDER BY id DESC",
            'filename' => 'blogs.csv',
        ],
        'blog_categories' => [
            'label' => 'Blog Categories',
            'sql' => "SELECT * FROM blog_categories ORDER BY id ASC",
            'filename' => 'blog_categories.csv',
        ],
        'blog_comments' => [
            'label' => 'Blog Comments',
            'sql' => "SELECT * FROM blog_comments ORDER BY id DESC",
            'filename' => 'blog_comments.csv',
        ],
        'blog_likes' => [
            'label' => 'Blog Likes',
            'sql' => "SELECT * FROM blog_likes ORDER BY id DESC",
            'filename' => 'blog_likes.csv',
        ],
        'contacts' => [
            'label' => 'Messages',
            'sql' => "SELECT * FROM contacts ORDER BY id DESC",
            'filename' => 'contacts.csv',
        ],
        'hero_settings' => [
            'label' => 'Hero Settings',
            'sql' => "SELECT * FROM hero_settings ORDER BY id ASC",
            'filename' => 'hero_settings.csv',
        ],
        'devices' => [
            'label' => 'Devices',
            'sql' => "SELECT * FROM devices ORDER BY id DESC",
            'filename' => 'devices.csv',
        ],
    ];
}

function export_send_csv(mysqli $link, string $filename, string $sql): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('X-Content-Type-Options: nosniff');

    $out = fopen('php://output', 'w');
    export_write_csv_from_sql($link, $sql, $out);
    fclose($out);
    exit;
}

function export_send_zip(mysqli $link, array $datasets, string $zipName): void
{
    if (!class_exists('ZipArchive')) {
        header("Location: export.php?error=" . urlencode("ZIP export not available on this server. Use CSV exports instead."));
        exit;
    }

    $zipPath = tempnam(sys_get_temp_dir(), 'protinut_export_');
    if ($zipPath === false) {
        header("Location: export.php?error=" . urlencode("Failed to create temp file for export."));
        exit;
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
        @unlink($zipPath);
        header("Location: export.php?error=" . urlencode("Failed to create ZIP file."));
        exit;
    }

    $tmpFiles = [];
    foreach ($datasets as $d) {
        $tmpCsv = tempnam(sys_get_temp_dir(), 'protinut_csv_');
        if ($tmpCsv === false) {
            continue;
        }
        $tmpFiles[] = $tmpCsv;
        $fh = fopen($tmpCsv, 'w');
        export_write_csv_from_sql($link, $d['sql'], $fh);
        fclose($fh);
        $zip->addFile($tmpCsv, $d['filename']);
    }

    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));
    header('X-Content-Type-Options: nosniff');
    readfile($zipPath);

    @unlink($zipPath);
    foreach ($tmpFiles as $f) {
        @unlink($f);
    }
    exit;
}

function export_print_dataset(mysqli $link, string $title, string $sql): void
{
    $res = mysqli_query($link, $sql);
    $rows = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = export_row_filter($row);
        }
    }

    $headers = [];
    if (count($rows) > 0) {
        $headers = array_keys($rows[0]);
    }

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($title); ?></title>
        <style>
            body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:0;padding:24px}
            .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;gap:12px}
            h1{font-size:18px;margin:0}
            .meta{font-size:12px;color:#666}
            .btn{display:inline-block;border:1px solid #111;padding:8px 10px;text-decoration:none;color:#111;font-size:12px}
            table{width:100%;border-collapse:collapse;font-size:12px}
            th,td{border:1px solid #ddd;padding:6px;vertical-align:top}
            th{background:#f6f6f6;text-align:left}
            @media print{.no-print{display:none}body{padding:0}}
        </style>
    </head>
    <body>
        <div class="top no-print">
            <div>
                <h1><?php echo htmlspecialchars($title); ?></h1>
                <div class="meta">Generated: <?php echo date('Y-m-d H:i'); ?></div>
            </div>
            <div>
                <a class="btn" href="#" onclick="window.print();return false;">Print / Save as PDF</a>
            </div>
        </div>

        <h1 class="no-print" style="display:none"></h1>
        <table>
            <thead>
            <tr>
                <?php foreach ($headers as $h) { ?>
                    <th><?php echo htmlspecialchars($h); ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0) { ?>
                <tr><td colspan="<?php echo max(1, count($headers)); ?>">No data</td></tr>
            <?php } else { ?>
                <?php foreach ($rows as $r) { ?>
                    <tr>
                        <?php foreach ($headers as $h) { ?>
                            <td><?php echo htmlspecialchars((string) ($r[$h] ?? '')); ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

function export_print_all(mysqli $link, array $datasets): void
{
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Protinut Admin Export</title>
        <style>
            body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:0;padding:24px}
            .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;gap:12px}
            h1{font-size:18px;margin:0}
            h2{font-size:14px;margin:18px 0 8px}
            .meta{font-size:12px;color:#666}
            .btn{display:inline-block;border:1px solid #111;padding:8px 10px;text-decoration:none;color:#111;font-size:12px}
            table{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:18px}
            th,td{border:1px solid #ddd;padding:5px;vertical-align:top}
            th{background:#f6f6f6;text-align:left}
            @media print{.no-print{display:none}body{padding:0}}
        </style>
    </head>
    <body>
        <div class="top no-print">
            <div>
                <h1>Protinut Admin Export (All)</h1>
                <div class="meta">Generated: <?php echo date('Y-m-d H:i'); ?></div>
            </div>
            <div>
                <a class="btn" href="#" onclick="window.print();return false;">Print / Save as PDF</a>
            </div>
        </div>

        <?php foreach ($datasets as $d) { ?>
            <?php
            $res = mysqli_query($link, $d['sql']);
            $rows = [];
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $rows[] = export_row_filter($row);
                }
            }
            $headers = [];
            if (count($rows) > 0) {
                $headers = array_keys($rows[0]);
            }
            ?>
            <h2><?php echo htmlspecialchars($d['label']); ?></h2>
            <table>
                <thead>
                <tr>
                    <?php foreach ($headers as $h) { ?>
                        <th><?php echo htmlspecialchars($h); ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($rows) === 0) { ?>
                    <tr><td colspan="<?php echo max(1, count($headers)); ?>">No data</td></tr>
                <?php } else { ?>
                    <?php foreach ($rows as $r) { ?>
                        <tr>
                            <?php foreach ($headers as $h) { ?>
                                <td><?php echo htmlspecialchars((string) ($r[$h] ?? '')); ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </body>
    </html>
    <?php
    exit;
}

$format = isset($_GET['format']) ? (string) $_GET['format'] : '';
$type = isset($_GET['type']) ? (string) $_GET['type'] : '';

if ($format !== '') {
    require_once "../connection.php";
    export_require_admin($link);
    $configs = export_dataset_configs();

    if ($format === 'zip' && $type === 'all') {
        $all = [];
        foreach ($configs as $k => $cfg) {
            if ($k === 'devices') {
                $all[] = $cfg;
                continue;
            }
            if (in_array($k, ['orders', 'order_items', 'products', 'users', 'blogs', 'blog_categories', 'blog_comments', 'blog_likes', 'contacts', 'hero_settings'], true)) {
                $all[] = $cfg;
            }
        }
        $zipName = 'protinut_export_' . date('Y-m-d_H-i') . '.zip';
        export_send_zip($link, $all, $zipName);
    }

    if ($format === 'print' && $type === 'all') {
        $all = [];
        foreach ($configs as $k => $cfg) {
            if (in_array($k, ['orders', 'order_items', 'products', 'users', 'blogs', 'blog_categories', 'contacts', 'hero_settings'], true)) {
                $all[] = $cfg;
            }
        }
        export_print_all($link, $all);
    }

    if (!isset($configs[$type])) {
        header("Location: export.php?error=" . urlencode("Invalid export type"));
        exit;
    }

    $cfg = $configs[$type];

    if ($format === 'csv') {
        export_send_csv($link, $cfg['filename'], $cfg['sql']);
    }

    if ($format === 'print') {
        export_print_dataset($link, $cfg['label'], $cfg['sql']);
    }

    if ($format === 'copy') {
        header('Content-Type: text/html; charset=utf-8');
        $buf = fopen('php://temp', 'w+');
        export_write_csv_from_sql($link, $cfg['sql'], $buf);
        rewind($buf);
        $csv = stream_get_contents($buf);
        fclose($buf);
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Copy Export - <?php echo htmlspecialchars($cfg['label']); ?></title>
            <style>
                body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:0;padding:24px}
                .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:12px}
                h1{font-size:16px;margin:0}
                .btn{display:inline-block;border:1px solid #111;padding:8px 10px;text-decoration:none;color:#111;font-size:12px}
                textarea{width:100%;height:70vh;font-size:12px;padding:10px;border:1px solid #ccc}
            </style>
        </head>
        <body>
            <div class="top">
                <h1>Copy CSV: <?php echo htmlspecialchars($cfg['label']); ?></h1>
                <div>
                    <a class="btn" href="export.php">Back</a>
                    <a class="btn" href="#" onclick="navigator.clipboard.writeText(document.getElementById('csv').value);return false;">Copy</a>
                </div>
            </div>
            <textarea id="csv" spellcheck="false"><?php echo htmlspecialchars($csv); ?></textarea>
        </body>
        </html>
        <?php
        exit;
    }

    header("Location: export.php?error=" . urlencode("Invalid export format"));
    exit;
}

include("header.php");
$configs = export_dataset_configs();
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Export / Share</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Export</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2">All Pages Data</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-primary" href="export.php?type=all&format=zip">Download ZIP (CSV)</a>
                            <a class="btn btn-outline-secondary" href="export.php?type=all&format=print" target="_blank">Print / Save as PDF</a>
                        </div>
                        <div class="mt-2 text-muted" style="font-size:12px;">
                            Excel: open the CSV files (inside ZIP) in Excel. PDF: use Print / Save as PDF.
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $cards = [
                'orders' => ['csv' => true, 'copy' => true, 'print' => true],
                'order_items' => ['csv' => true, 'copy' => true, 'print' => true],
                'products' => ['csv' => true, 'copy' => true, 'print' => true],
                'users' => ['csv' => true, 'copy' => true, 'print' => true],
                'blogs' => ['csv' => true, 'copy' => true, 'print' => true],
                'blog_categories' => ['csv' => true, 'copy' => true, 'print' => true],
                'contacts' => ['csv' => true, 'copy' => true, 'print' => true],
                'hero_settings' => ['csv' => true, 'copy' => true, 'print' => true],
                'devices' => ['csv' => true, 'copy' => true, 'print' => true],
            ];
            foreach ($cards as $k => $opts) {
                if (!isset($configs[$k])) {
                    continue;
                }
                $cfg = $configs[$k];
                ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2"><?php echo htmlspecialchars($cfg['label']); ?></h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($opts['csv'])) { ?>
                                    <a class="btn btn-sm btn-success" href="export.php?type=<?php echo urlencode($k); ?>&format=csv">Excel (CSV)</a>
                                <?php } ?>
                                <?php if (!empty($opts['copy'])) { ?>
                                    <a class="btn btn-sm btn-outline-primary" href="export.php?type=<?php echo urlencode($k); ?>&format=copy" target="_blank">Copy</a>
                                <?php } ?>
                                <?php if (!empty($opts['print'])) { ?>
                                    <a class="btn btn-sm btn-outline-secondary" href="export.php?type=<?php echo urlencode($k); ?>&format=print" target="_blank">PDF</a>
                                <?php } ?>
                            </div>
                            <div class="mt-2 text-muted" style="font-size:12px;">
                                Share: download and send the file, or open Copy and paste.
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
