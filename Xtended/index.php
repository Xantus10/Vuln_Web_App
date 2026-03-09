<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Aplikace pro showcase a paktickou exploitaci určitých webových útoků">
  <meta name="author" content="Jroslav Žaba">
  <link rel="stylesheet" href="index.css">
  <title>Vuln | Xtended</title>
</head>
<body>
  <div class="cont">
    <header class="header">
      <?php
        include_once("components/header.php");
      ?>
    </header>

    <main class="main">
      <nav class="nav">
        <?php
          include_once("components/nav.php");
        ?>
      </nav>
      <article class="article">
        <?php
          include_once("pages/" . PAGES[$CURRENT_PAGE]["file"]);
        ?>
      </article>
    </main>
    
    <footer class="footer">
      <?php
        include_once("components/footer.php");
      ?>
    </footer>
  </div>
</body>
</html>