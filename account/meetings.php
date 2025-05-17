<?php
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $meetings_query = "SELECT * FROM meetings WHERE user_id = '$user_id' ORDER BY meeting_date ASC LIMIT 2";
    $meetings_result = mysqli_query($conn, $meetings_query);
    
    if(mysqli_num_rows($meetings_result) > 0): ?>
        <div class="list-group mt-3">
            <?php while($meeting = mysqli_fetch_assoc($meetings_result)): ?>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= htmlspecialchars($meeting['title']) ?></h5>
                        <small><?= date('D, M j g:i A', strtotime($meeting['meeting_date'])) ?></small>
                    </div>
                    <p class="mb-1"><?= htmlspecialchars($meeting['description']) ?></p>
                    <small>With: <?= htmlspecialchars($meeting['participants']) ?></small>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No upcoming meetings scheduled</p>
    <?php endif;
} else {
    echo "<p class='text-muted'>Please login to view your meetings</p>";
}
?>