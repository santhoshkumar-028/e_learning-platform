<?php
require 'db_connect.php';
if (!isset($_GET['id'])) { header('Location: courses.php'); exit; }
$course_id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM courses WHERE id=? LIMIT 1");
$stmt->bind_param('i',$course_id);
$stmt->execute();
$res = $stmt->get_result();
$course = $res->fetch_assoc();
$qs = $conn->prepare("SELECT * FROM quizzes WHERE course_id=?");
$qs->bind_param('i',$course_id);
$qs->execute();
$qres = $qs->get_result();
$questions = $qres->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><title><?php echo htmlspecialchars($course['title']); ?></title><link rel="stylesheet" href="styles.css"></head>
<body>
  <header class="site-header"><div class="brand">BRIGHT FUTURE</div><nav><a href="index.php">Home</a><a href="courses.php">Courses</a></nav></header>
  <main class="container">
    <h2><?php echo htmlspecialchars($course['title']); ?></h2>
    <div style="display:flex;gap:20px;align-items:flex-start">
      <div style="flex:1">
        <?php if(!empty($course['image'])): ?>
          <img src="<?php echo htmlspecialchars($course['image']); ?>" style="width:100%;height:240px;object-fit:cover;border-radius:8px"/>
        <?php endif; ?>
        <h3>Video</h3>
        <?php if(!empty($course['video']) && file_exists($course['video'])): ?>
          <video controls style="width:100%;max-height:420px">
            <source src="<?php echo htmlspecialchars($course['video']); ?>" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        <?php else: ?>
          <div style="background:#eee;padding:20px;border-radius:6px">No video uploaded yet for this course. Admin can upload from admin panel.</div>
        <?php endif; ?>
      </div>
      <div style="flex:1">
        <h3>About</h3>
        <p><?php echo nl2br(htmlspecialchars($course['content'])); ?></p>
      </div>
    </div>

    <section style="margin-top:24px">
      <h3>Quiz</h3>
      <?php if(count($questions) == 0): ?>
        <p>No quiz available yet for this course.</p>
      <?php else: ?>
        <form method="post" action="submit_quiz.php">
          <input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
          <div class="quiz-grid">
          <?php foreach($questions as $i => $q): $index = $i+1; ?>
            <div class="card">
              <strong>Q<?php echo $index; ?>. <?php echo htmlspecialchars($q['question']); ?></strong>
              <div>
                <label><input type="radio" name="ans[<?php echo $q['id']; ?>]" value="1"> <?php echo htmlspecialchars($q['opt1']); ?></label><br>
                <label><input type="radio" name="ans[<?php echo $q['id']; ?>]" value="2"> <?php echo htmlspecialchars($q['opt2']); ?></label><br>
                <label><input type="radio" name="ans[<?php echo $q['id']; ?>]" value="3"> <?php echo htmlspecialchars($q['opt3']); ?></label><br>
                <label><input type="radio" name="ans[<?php echo $q['id']; ?>]" value="4"> <?php echo htmlspecialchars($q['opt4']); ?></label>
              </div>
            </div>
          <?php endforeach; ?>
          </div>
          <div style="margin-top:12px">
            <button type="submit">Submit Quiz</button>
          </div>
        </form>
      <?php endif; ?>
    </section>

  </main>
</body>
</html>
