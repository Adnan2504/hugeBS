<?php
require_once '../helpers.php';

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class GalleryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $images = GalleryModel::getImages(true);
        $this->View->render('gallery/index', ['images' => $images]);
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $uploadDir = basePath('public/img/');
            $fileName = basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            $userId = Session::get('user_id');
            $isPublic = isset($_POST['is_public']) ? 1 : 0;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                if (GalleryModel::saveImage($uploadFile, $userId, $isPublic)) {
                    Redirect::to('gallery/index');
                } else {
                    echo 'Failed to save image metadata in the database.';
                }
            } else {
                echo 'Failed to move the uploaded file.';
            }
        } else {
            $this->View->render('gallery/upload');
        }
    }

    public static function getImages($imageName)
    {
        return Config::get('URL') . $imageName;
    }

}
