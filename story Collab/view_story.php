<?php
include 'db.php';
if(!isset($_GET['id'])) {
  header('Location: stories.php');
  exit;
}

$id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT s.*, u.username FROM stories s JOIN users u ON s.user_id = u.id WHERE s.id = $id");

if(mysqli_num_rows($result) === 0) {
  header('Location: stories.php');
  exit;
}

$story = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo htmlspecialchars($story['title']); ?> - StoryCollab</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Font Awesome + jQuery CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f5f5;
    }

    header {
      background: #3a1c71;
      padding: 1rem 0;
      color: white;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
    }

    nav a {
      color: white;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }

    .story-hero {
      position: relative;
      height: 50vh;
      min-height: 300px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 2rem;
      overflow: hidden;
    }

    .story-hero-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('images/story_bg.jpg') center/cover no-repeat;
      filter: brightness(0.5);
      z-index: -1;
    }

    .story-hero-content {
      max-width: 800px;
      color: white;
      animation: fadeInDown 1s ease forwards;
    }

    .story-hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .story-meta-info {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
      font-size: 1rem;
    }

    .story-meta-info span {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .story-content-container {
      max-width: 800px;
      margin: -80px auto 0 auto;
      background: white;
      padding: 3rem 2rem;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.1);
      animation: fadeInUp 1s ease forwards;
      position: relative;
      z-index: 1;
    }

    .story-content p {
      line-height: 1.8;
      font-size: 1.1rem;
      color: #444;
      margin-bottom: 1.5rem;
    }

    .story-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 2rem;
      border-top: 1px solid #eee;
      padding-top: 1.5rem;
    }

    .story-share a {
      background: #eee;
      padding: 10px;
      border-radius: 50%;
      text-decoration: none;
      color: #555;
      transition: all 0.3s ease;
    }

    .story-share a:hover {
      background: #ddd;
      transform: scale(1.1);
    }

    .night-mode-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: white;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s;
    }

    .night-mode-toggle:hover {
      transform: scale(1.1);
    }

    .night-mode-toggle i {
      font-size: 1.5rem;
    }

    body.night-mode {
      background: #121212;
      color: #e0e0e0;
    }

    body.night-mode .story-content-container {
      background: #1e1e1e;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    body.night-mode .story-content p {
      color: #ddd;
    }

    body.night-mode .story-share a {
      background: #333;
      color: #eee;
    }

    body.night-mode .night-mode-toggle {
      background: #2e2e2e;
    }

    body.night-mode .night-mode-toggle i {
      color: #f1c40f;
    }

    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .story-hero h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1><i class="fas fa-book-open"></i> StoryCollab</h1>
    <nav>
      <a href="index.html"><i class="fas fa-home"></i> Home</a>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="create_story.php"><i class="fas fa-pen-fancy"></i> Create</a>
      <a href="stories.php"><i class="fas fa-book"></i> Explore</a>
      <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    </nav>
  </header>

  <div class="story-hero">
    <div class="story-hero-bg"></div>
    <div class="story-hero-content">
      <h1><?php echo htmlspecialchars($story['title']); ?></h1>
      <div class="story-meta-info">
        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($story['username']); ?></span>
        <span><i class="fas fa-clock"></i> Published</span>
      </div>
    </div>
  </div>

  <div class="story-content-container">
    <div class="story-content">
      <?php 
        $paragraphs = explode("\n", $story['content']);
        foreach ($paragraphs as $para) {
          echo "<p>" . nl2br(htmlspecialchars($para)) . "</p>";
        }
      ?>
    </div>

    <div class="story-actions">
      <div class="story-share">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fas fa-link"></i></a>
      </div>
    </div>
  </div>

  <div class="night-mode-toggle" onclick="document.body.classList.toggle('night-mode')">
    <i class="fas fa-moon"></i>
  </div>

</body>
</html>
