<div class="container">
    <h1>Gallery</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h1>Upload Image</h1>
        <form action="<?= Config::get('URL'); ?>gallery/upload" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <label>
                <input type="checkbox" name="is_public" value="1"> Make Public
            </label>
            <button type="submit">Upload</button>
        </form>


        <h1>Gallery</h1>
        <div class="gallery">
            <?php foreach ($this->images as $image): ?>
                <div class="image">
                    <img src="<?= $image['image_url']; ?>" alt="Image">

                    <p>Uploaded by User ID: <?= htmlspecialchars($image['uploaded_by']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>




    </div>
</div>
