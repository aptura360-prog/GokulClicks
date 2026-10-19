<?php
require 'config.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM events WHERE slug = ?");
$stmt->execute([$slug]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);

    $pageTitle = "Event not found - $STUDIO_NAME";

    include 'includes/header.php';

    echo '
    <section class="page-hero">
        <div class="wrap">
            <h1>Event not found</h1>
            <p class="hero-sub light">
                This event may have been removed.
                <a href="gallery.php">Back to gallery</a>
            </p>
        </div>
    </section>';

    include 'includes/footer.php';
    exit;
}

$pageTitle = $event['title'] . " - $STUDIO_NAME";
$activePage = 'gallery';


$eventFolders = [
    1 => 'wedding',
    2 => 'birthday',
    3 => 'engagement',
    5 => 'prewedding',
    6 => 'fashion'
];

$folder = $eventFolders[$event['id']] ?? '';

$photos = [];

if ($folder !== '') {

    $folderPath = __DIR__ . '/assets/images/events/' . $folder;

    $webFolder = '/assets/images/events/' . $folder;

    if (is_dir($folderPath)) {

        $files = scandir($folderPath);

        foreach ($files as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif'])) {

                $photos[] = [
                    'name' => $file,
                    'url' => $webFolder . '/' . rawurlencode($file)
                ];
            }
        }
    }
}


include 'includes/header.php';
?>

<section class="page-hero event-hero">

    <div class="wrap">

        <a href="gallery.php" class="back-link">
            &larr; Back to gallery
        </a>

        <span class="event-tag light">
            <?= htmlspecialchars($event['category']) ?>
        </span>

        <h1>
            <?= htmlspecialchars($event['title']) ?>
        </h1>

    </div>

</section>


<section class="event-detail">

    <div class="wrap">

        <?php if (!empty($event['description'])): ?>

            <p class="event-description">
                <?= htmlspecialchars($event['description']) ?>
            </p>

        <?php endif; ?>


        <?php if (count($photos) > 0): ?>

            <div class="photo-grid">

                <?php foreach ($photos as $photo): ?>

                    <button type="button"
                            class="photo-thumb"
                            data-full="<?= htmlspecialchars($photo['url']) ?>"
                            data-caption="<?= htmlspecialchars($photo['name']) ?>">

                        <img src="<?= htmlspecialchars($photo['url']) ?>"
                             alt="<?= htmlspecialchars($event['title']) ?>"
                             loading="lazy">

                    </button>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p class="empty-state">
                No photos have been added to this event yet.
            </p>

        <?php endif; ?>

    </div>

</section>


<div class="lightbox" id="lightbox">

    <button class="lightbox-close"
            id="lightboxClose"
            aria-label="Close">
        &times;
    </button>

    <img src=""
         alt=""
         id="lightboxImg">

    <p id="lightboxCaption"></p>

</div>


<?php include 'includes/footer.php'; ?>