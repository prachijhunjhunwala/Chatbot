<?php
// header.php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>BCCL Clone</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">BCCL Clone</div>
    <nav class="navbar">
      <a href="index.php"    class="<?php echo ($current=='index.php') ? 'active' : ''; ?>">Home</a>
      <a href="about.php"    class="<?php echo ($current=='about.php') ? 'active' : ''; ?>">About</a>
      <a href="services.php" class="<?php echo ($current=='services.php') ? 'active' : ''; ?>">Services</a>
      <a href="contact.php"  class="<?php echo ($current=='contact.php') ? 'active' : ''; ?>">Contact</a>
    </nav>
  </header>
