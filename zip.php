<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zipfile'])) {
    $zipFile = $_FILES['zipfile']['tmp_name'];
    $zipName = basename($_FILES['zipfile']['name']);
    $extractTo = __DIR__ ;

    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        mkdir($extractTo);
        $zip->extractTo($extractTo);
        $zip->close();
        $msg = "✅ Berhasil diekstrak ke: <code>$extractTo</code>";
    } else {
        $msg = "❌ Gagal membuka file ZIP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ZIP Unzipper - R07 Tools</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">🧩 ZIP Unzipper</h4>
    </div>
    <div class="card-body">
      <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Upload ZIP File</label>
          <input type="file" name="zipfile" accept=".zip" class="form-control" required>
        </div>
        <button class="btn btn-success" type="submit">Unzip Sekarang</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>