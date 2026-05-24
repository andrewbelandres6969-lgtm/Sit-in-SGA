<?php
$page_title = 'Feedback | Student';
require_once __DIR__ . '/includes/header.php';
?>

<main class="student-wrap">
    <section class="student-page-panel feedback-panel">
        <div class="student-panel-header">Feedback</div>
        <div class="student-page-body">
            <h2>Rate Your Sit-in Experience</h2>
            <p class="page-desc">Share your rating and comments so the admin can review your feedback.</p>

            <form class="student-form feedback-form" method="POST" action="<?php echo app_url('student/save_feedback.php'); ?>">
                <div class="feedback-rating-field">
                    <label>Rating</label>
                    <div class="star-rating" aria-label="Rating from 0 to 5 stars">
                        <?php for ($rating = 0; $rating <= 5; $rating++): ?>
                            <input type="radio" id="rating_<?php echo $rating; ?>" name="rating" value="<?php echo $rating; ?>" <?php echo $rating === 5 ? 'checked' : ''; ?>>
                            <label for="rating_<?php echo $rating; ?>" data-rating="<?php echo $rating; ?>" title="<?php echo $rating; ?> star rating"><?php echo $rating === 0 ? '0' : '★'; ?></label>
                        <?php endfor; ?>
                    </div>
                    <p class="field-note">5 is very good. Choose 0 if you do not want to give a star rating.</p>
                </div>

                <div>
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="General">General</option>
                        <option value="Laboratory">Laboratory</option>
                        <option value="Computer">Computer</option>
                        <option value="Staff">Staff</option>
                        <option value="Reservation">Reservation</option>
                    </select>
                </div>

                <div class="feedback-comment-field">
                    <label for="message">Comment</label>
                    <textarea id="message" name="message" placeholder="Write your feedback here..." required></textarea>
                </div>

                <button type="submit" class="btn-student-primary">Submit Feedback</button>
            </form>
        </div>
    </section>
</main>

<script>
(function() {
    const ratingWrap = document.querySelector('.star-rating');
    if (!ratingWrap) return;

    const inputs = ratingWrap.querySelectorAll('input[name="rating"]');
    const labels = ratingWrap.querySelectorAll('label[data-rating]');

    function paint(value) {
        labels.forEach(label => {
            const rating = Number(label.dataset.rating);
            label.classList.toggle('selected', rating > 0 && rating <= value);
            label.classList.toggle('zero-selected', rating === 0 && value === 0);
        });
    }

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            paint(Number(input.value));
        });
    });

    labels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            paint(Number(label.dataset.rating));
        });
    });

    ratingWrap.addEventListener('mouseleave', function() {
        const checked = ratingWrap.querySelector('input[name="rating"]:checked');
        paint(Number(checked ? checked.value : 0));
    });

    paint(5);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
