<?php
require_once '../helpers.php';

const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => 0
    ],
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagegif'
    ]
];

class GalleryModel
{
    /**
     * Save image metadata to the database
     *
     * @param string $fileName The name of the uploaded file
     * @param int $uploadedBy The ID of the user who uploaded the image
     * @param bool $isPublic Whether the image is public or private
     * @return bool True on success, false on failure
     */
    public static function saveImage($fileName, $uploadedBy, $isPublic)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO images (image_url, uploaded_by, is_public) VALUES (:image_url, :uploaded_by, :is_public)";
        $query = $database->prepare($sql);

        return $query->execute([
            ':image_url' => $fileName,  // Save only the filename, no path
            ':uploaded_by' => $uploadedBy,
            ':is_public' => $isPublic
        ]);
    }

    /**
     * Retrieve all images
     *
     * @return array List of images with filenames
     */
    public static function getImages()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM images";
        $query = $database->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllImages($file)
    {
        $uploadDir = basePath('public/img/');
        $galleryFolder = $uploadDir . $file;

        return $galleryFolder;
    }

}



