<?php session_start(); ?>
<?php include 'db.php'; ?>
<?php if(!isset($_GET['id'])) die('Story not found'); ?>
<?php
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM stories WHERE id = $id");
$story = mysqli_fetch_assoc($result);

// Check if user is the owner of this story
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $story['user_id']) {
  die('You do not have permission to edit this story');
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Story - <?php echo htmlspecialchars($story['title']); ?></title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <header class="header fade-in">
    <div class="container">
      <div class="logo">
        <img src="assets/icon.png" alt="StoryCollab" class="logo-img">
        <h1>Edit Story</h1>
      </div>
      <nav>
        <a href="index.html"><i class="fas fa-home"></i> Home</a>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="create_story.php"><i class="fas fa-pen-fancy"></i> Create</a>
        <a href="stories.php"><i class="fas fa-book-open"></i> Explore</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
      </nav>
    </div>
  </header>

  <main class="container slide-up">
    <div class="editor-header">
      <h2><?php echo htmlspecialchars($story['title']); ?></h2>
      <div class="editor-actions">
        <button id="fullscreenBtn" class="btn btn-icon"><i class="fas fa-expand"></i></button>
        <span id="status" class="status-msg"></span>
      </div>
    </div>
    
    <div class="editor-toolbar">
      <button class="toolbar-btn" data-format="bold"><i class="fas fa-bold"></i></button>
      <button class="toolbar-btn" data-format="italic"><i class="fas fa-italic"></i></button>
      <button class="toolbar-btn" data-format="underline"><i class="fas fa-underline"></i></button>
      <span class="toolbar-divider"></span>
      <button class="toolbar-btn" data-format="h1"><i class="fas fa-heading"></i>1</button>
      <button class="toolbar-btn" data-format="h2"><i class="fas fa-heading"></i>2</button>
      <button class="toolbar-btn" data-format="h3"><i class="fas fa-heading"></i>3</button>
      <span class="toolbar-divider"></span>
      <button class="toolbar-btn" data-format="ul"><i class="fas fa-list-ul"></i></button>
      <button class="toolbar-btn" data-format="ol"><i class="fas fa-list-ol"></i></button>
      <span class="toolbar-divider"></span>
      <button class="toolbar-btn" data-format="link"><i class="fas fa-link"></i></button>
      <button class="toolbar-btn" data-format="image"><i class="fas fa-image"></i></button>
    </div>

    <div class="editor-container">
      <input type="hidden" id="storyId" value="<?php echo $id; ?>">
      <textarea id="storyContent" class="textarea-field"><?php echo htmlspecialchars($story['content']); ?></textarea>
    </div>
    
    <div class="editor-footer">
      <span id="wordCount">0 words</span>
      <div class="save-indicator">
        <span id="saveIcon"><i class="fas fa-check"></i></span>
        <span id="lastSaved">Last saved: Just now</span>
      </div>
    </div>
  </main>

  <footer class="footer fade-in">
    <div class="container">
      <p>&copy; 2025 StoryCollab. All rights reserved.</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </footer>

  <script>
  $(document).ready(function(){
    // Word counter
    function updateWordCount() {
      const text = $('#storyContent').val();
      const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
      $('#wordCount').text(wordCount + ' words');
    }
    
    updateWordCount();
    
    // Auto-save functionality
    let typingTimer;
    const doneTypingInterval = 1000; // 1 second
    
    $('#storyContent').on('input', function() {
      updateWordCount();
      $('#saveIcon').html('<div class="loading"></div>');
      $('#lastSaved').text('Saving...');
      
      clearTimeout(typingTimer);
      typingTimer = setTimeout(saveContent, doneTypingInterval);
    });
    
    function saveContent() {
      const content = $('#storyContent').val();
      const storyId = $('#storyId').val();
      
      $.ajax({
        url: 'ajax_save.php',
        type: 'POST',
        data: { content: content, story_id: storyId },
        success: function(response) {
          $('#saveIcon').html('<i class="fas fa-check"></i>');
          const now = new Date();
          const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
          $('#lastSaved').text('Last saved: ' + timeStr);
        },
        error: function() {
          $('#saveIcon').html('<i class="fas fa-exclamation-triangle"></i>');
          $('#lastSaved').text('Save failed. Please try again.');
        }
      });
    }
    
    // Simple formatting toolbar
    $('.toolbar-btn').click(function() {
      const format = $(this).data('format');
      const textarea = document.getElementById('storyContent');
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const selectedText = textarea.value.substring(start, end);
      let formattedText = '';
      
      switch(format) {
        case 'bold':
          formattedText = '**' + selectedText + '**';
          break;
        case 'italic':
          formattedText = '*' + selectedText + '*';
          break;
        case 'underline':
          formattedText = '_' + selectedText + '_';
          break;
        case 'h1':
          formattedText = '# ' + selectedText;
          break;
        case 'h2':
          formattedText = '## ' + selectedText;
          break;
        case 'h3':
          formattedText = '### ' + selectedText;
          break;
        case 'ul':
          formattedText = '- ' + selectedText.split('\n').join('\n- ');
          break;
        case 'ol':
          lines = selectedText.split('\n');
          for (let i = 0; i < lines.length; i++) {
            formattedText += (i+1) + '. ' + lines[i] + '\n';
          }
          break;
        case 'link':
          formattedText = '[' + selectedText + '](url)';
          break;
        case 'image':
          formattedText = '![alt text](' + selectedText + ')';
          break;
      }
      
      textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
      updateWordCount();
      // Position cursor after the inserted text
      textarea.selectionStart = start + formattedText.length;
      textarea.selectionEnd = start + formattedText.length;
      textarea.focus();
    });
    
    // Full screen mode
    $('#fullscreenBtn').click(function() {
      $('.editor-container').toggleClass('fullscreen');
      $(this).find('i').toggleClass('fa-expand fa-compress');
    });
  });
  </script>
</body>
</html>