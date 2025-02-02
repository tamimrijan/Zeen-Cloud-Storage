<?php
session_start();
require_once 'db_config.php';

$is_guest = isset($_GET['guest']) && $_GET['guest'] == 1;

// Check if user is logged in or a guest
if (!isset($_SESSION['user_id']) && !$is_guest) {
    header("Location: auth.php");
    exit();
}

// Set the root directory to "zeen/Files"
$root = realpath(__DIR__ . '/root');
$current_dir = isset($_GET['dir']) ? trim($_GET['dir'], '/') : '';
$current_path = realpath($root . DIRECTORY_SEPARATOR . $current_dir);

// Prevent access outside the root folder
if ($current_path === false || strpos($current_path, $root) !== 0) {
    $current_path = $root;
    $current_dir = '';
}

// Handle folder creation and file upload (only for logged-in users)
if (!$is_guest && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_folder'])) {
        $new_folder = $current_path . DIRECTORY_SEPARATOR . $_POST['new_folder'];
        if (!file_exists($new_folder)) {
            mkdir($new_folder);
        }
    } elseif (isset($_FILES['file_upload'])) {
        $target_file = $current_path . DIRECTORY_SEPARATOR . basename($_FILES['file_upload']['name']);
        move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file);
    } elseif (isset($_POST['delete'])) {
        $item_to_delete = $current_path . DIRECTORY_SEPARATOR . $_POST['delete'];
        if (is_dir($item_to_delete)) {
            rmdir($item_to_delete);
        } elseif (is_file($item_to_delete)) {
            unlink($item_to_delete);
        }
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $file_to_download = $current_path . DIRECTORY_SEPARATOR . $_GET['download'];
    if (file_exists($file_to_download) && is_file($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file_to_download).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_to_download));
        readfile($file_to_download);
        exit;
    }
}

// List all files & directories
$items = scandir($current_path);
$items = array_diff($items, array('.', '..'));

// Function to get file size
function get_file_size($file) {
    if (is_file($file)) {
        return @filesize($file) ?: 'N/A';
    }
    return '-';
}

// Format file size
function format_size($size) {
    if ($size == '-' || $size == 'N/A') {
        return $size;
    }
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($size >= 1024 && $i < 4) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

// Get relative path
function get_relative_path($path, $root) {
    return str_replace($root . DIRECTORY_SEPARATOR, '', $path);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeen File Manager</title>
    <link rel="icon" type="image/x-icon" href="\assets\fevicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Zeen File Manager</h1>
        
        <?php if ($is_guest): ?>
            <p>You are browsing as a guest. <a href="auth.php" class="btn btn-sm btn-primary">Login</a></p>
        <?php else: ?>
            <p>Welcome, <?php echo $_SESSION['username']; ?>! 
                <a href="logout.php" class="btn btn-sm btn-secondary">Logout</a>
            </p>
        <?php endif; ?>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?dir=<?php echo $is_guest ? '&guest=1' : ''; ?>">Root</a></li>
                <?php
                $path_parts = explode(DIRECTORY_SEPARATOR, $current_dir);
                $breadcrumb_path = '';
                foreach ($path_parts as $part) {
                    if ($part) {
                        $breadcrumb_path .= $part . DIRECTORY_SEPARATOR;
                        echo "<li class='breadcrumb-item'><a href='?dir=" . urlencode($breadcrumb_path) . ($is_guest ? '&guest=1' : '') . "'>" . htmlspecialchars($part) . "</a></li>";
                    }
                }
                ?>
            </ol>
        </nav>

        <?php if (!$is_guest): ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="POST" class="input-group">
                        <input type="text" name="new_folder" class="form-control" placeholder="New Folder Name" required>
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="POST" enctype="multipart/form-data" class="input-group">
                        <input type="file" name="file_upload" class="form-control" required>
                        <button type="submit" class="btn btn-success">Upload File</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($items as $item): ?>
                <?php
                $item_path = $current_path . DIRECTORY_SEPARATOR . $item;
                $is_dir = is_dir($item_path);
                $size = get_file_size($item_path);
                $relative_path = get_relative_path($item_path, $root);
                ?>
                <div class="col-md-3 col-sm-4 col-6 mb-4">
                    <div class="file-card shadow-sm">
                        <?php if ($is_dir): ?>
                            <i class="fas fa-folder text-warning file-icon"></i>
                            <h6>
                                <a href="?dir=<?= urlencode($relative_path) . ($is_guest ? '&guest=1' : '') ?>" class="text-dark">
                                    <?= htmlspecialchars($item) ?>
                                </a>
                            </h6>
                        <?php else: ?>
                            <i class="fas fa-file text-primary file-icon"></i>
                            <h6><?= htmlspecialchars($item) ?></h6>
                        <?php endif; ?>
                        <p class="text-muted small"><?= $is_dir ? 'Folder' : format_size($size) ?></p>
                        <div class="file-actions">
                            <?php if (!$is_dir): ?>
                                <a href="?dir=<?= urlencode($current_dir) ?>&download=<?= urlencode($item) . ($is_guest ? '&guest=1' : '') ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!$is_guest): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="delete" value="<?= htmlspecialchars($item) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button id="darkModeToggle" class="btn btn-secondary position-fixed bottom-0 end-0 m-3">
        <i class="fas fa-moon"></i> Toggle Dark Mode
    </button>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

