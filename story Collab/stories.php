<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Explore Stories - StoryCollab</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <header class="header fade-in">
    <div class="container">
      <h1><i class="fas fa-book-open"></i> StoryCollab</h1>
      <nav>
        <a href="index.html"><i class="fas fa-home"></i> Home</a>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="create_story.php"><i class="fas fa-pen-fancy"></i> Create</a>
        <a href="stories.php" class="active"><i class="fas fa-book-open"></i> Explore</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
      </nav>
    </div>
  </header>

  <div class="page-banner">
    <div class="container">
      <h2>Explore Stories</h2>
      <p>Discover incredible stories from writers around the world</p>
    </div>
  </div>

  <main class="container slide-up">
    <div class="filter-bar">
      <div class="search-box">
        <input type="text" id="storySearch" placeholder="Search stories...">
        <button class="search-btn"><i class="fas fa-search"></i></button>
      </div>
      <div class="filter-options">
        <select id="filterCategory">
          <option value="">All Categories</option>
          <option value="fantasy">Fantasy</option>
          <option value="mystery">Mystery</option>
          <option value="romance">Romance</option>
          <option value="scifi">Science Fiction</option>
          <option value="thriller">Thriller</option>
        </select>
        <select id="filterSort">
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="az">A-Z</option>
          <option value="za">Z-A</option>
        </select>
      </div>
    </div>

    <div class="stories-grid">
      <?php
      $result = mysqli_query($conn, "SELECT s.*, u.username FROM stories s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
      
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          // Generate a random color for the story card accent
          $colors = array('#FF6B6B', '#4ECDC4', '#45B7D1', '#FCBF49', '#7B68EE', '#98D8C8');
          $randomColor = $colors[array_rand($colors)];
          
          // Get word count
          $wordCount = str_word_count($row['content']);
          
          // Get a random story cover image (in a real application, you'd use actual cover images)
          $coverNum = ($row['id'] % 3) + 1; // Use modulo to cycle through 3 images
          
          echo '<div class="story-card fade-in" style="border-top: 5px solid ' . $randomColor . '">
                  <div class="story-cover">
                    <div class="placeholder-img">
                      <i class="fas fa-book-open"></i>
                    </div>
                  </div>
                  <div class="story-card-content">
                    <h3>' . htmlspecialchars($row['title']) . '</h3>
                    <p class="story-excerpt">' . htmlspecialchars(substr($row['content'], 0, 150)) . '...</p>
                    <div class="story-meta">
                      <span><i class="fas fa-user"></i> ' . htmlspecialchars($row['username']) . '</span>
                      <span><i class="fas fa-file-alt"></i> ' . $wordCount . ' words</span>
                    </div>
                    <a href="view_story.php?id=' . $row['id'] . '" class="btn-subtle">Read Story</a>
                  </div>
                </div>';
        }
      } else {
        echo '<div class="no-stories">
                <i class="fas fa-book fa-3x"></i>
                <h3>No stories found</h3>
                <p>Be the first to add a story!</p>
                <a href="create_story.php" class="btn">Create Story</a>
              </div>';
      }
      ?>
    </div>

    <div class="pagination">
      <a href="#" class="page-link disabled"><i class="fas fa-chevron-left"></i></a>
      <a href="#" class="page-link active">1</a>
      <a href="#" class="page-link">2</a>
      <a href="#" class="page-link">3</a>
      <span class="page-dots">...</span>
      <a href="#" class="page-link">10</a>
      <a href="#" class="page-link"><i class="fas fa-chevron-right"></i></a>
    </div>
  </main>

  <section class="cta-create slide-up">
    <div class="container">
      <h2>Have a story to tell?</h2>
      <p>Share your creative writing with our community of storytellers.</p>
      <a href="create_story.php" class="btn"><i class="fas fa-pen"></i> Start Writing</a>
    </div>
  </section>

  <footer class="footer fade-in">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>StoryCollab</h3>
          <p>A collaborative platform for writers to create and share stories together.</p>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="stories.php">Explore Stories</a></li>
            <li><a href="register.php">Join Now</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Connect With Us</h3>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 StoryCollab. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    $(document).ready(function() {
      // Animate elements when they come into view
      $(window).scroll(function() {
        $('.fade-in').each(function() {
          let bottom_of_element = $(this).offset().top + $(this).outerHeight() / 2;
          let bottom_of_window = $(window).scrollTop() + $(window).height();
          
          if (bottom_of_window > bottom_of_element) {
            $(this).addClass('visible');
          }
        });
        
        $('.slide-up').each(function() {
          let bottom_of_element = $(this).offset().top + $(this).outerHeight() / 3;
          let bottom_of_window = $(window).scrollTop() + $(window).height();
          
          if (bottom_of_window > bottom_of_element) {
            $(this).addClass('visible');
          }
        });
      });
      
      // Search functionality
      $('#storySearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('.story-card').filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        
        // Show or hide "no results" message
        if ($('.story-card:visible').length === 0) {
          if ($('.no-results').length === 0) {
            $('.stories-grid').append('<div class="no-results"><h3>No stories found</h3><p>Try a different search term</p></div>');
          }
        } else {
          $('.no-results').remove();
        }
      });
      
      // Trigger scroll once to check for elements already in view on page load
      $(window).scroll();
    });
  </script>
</body>
</html>