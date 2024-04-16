<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Application;
use Garden\Lib\LoginRequired;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;

use function Garden\buffer_var_dump;

class ImageController
{
    #[Filter('LoginRequired')]
    #[Route('post', '/upload')]
    public function upload_image(Request $request)
    {
        $data = $request->POST;

        if (\array_key_exists('image', $data)) {
            $filename = $this->upload_png_data($data['image']);
        } elseif (\array_key_exists('image_file', $_FILES)) {
            switch($_FILES['image_file']['error']) {
                case 0:
                    $filename = $this->upload_image_file($_FILES['image_file']);
                    break;
                case 1:
                    echo \json_encode([
                        'error' => 'Image is bigger than 4M'
                    ]);
                    break;
                case 2:
                    echo \json_encode([
                        'error' => 'Image is bigger than MAX_FILE_SIZE'
                    ]);
                    break;
                case 3:
                    echo \json_encode([
                        'error' => 'Image was only partially uploaded'
                    ]);
                    break;
                case 4:
                    echo \json_encode([
                        'error' => 'No file was uploaded'
                    ]);
                    break;
                case 6:
                    echo \json_encode([
                        'error' => 'Missing temporary folder'
                    ]);
                    break;
                case 7:
                    echo \json_encode([
                        'error' => 'Failed to write image to storage'
                    ]);
                    break;
                case 8:
                    echo \json_encode([
                        'error' => 'Something else went horribly wrong'
                    ]);
                    break;
            }
        } else {
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Bad image data'
            ]);
        }

        if ($filename !== '') {
            echo \json_encode([
                'error' => '',
                'filename' => "$filename.png",
            ]);
        }
    }

    private function upload_png_data($image_data): string
    {
        $parts = \explode(";", $image_data);
        $filetype = $parts[0];
        $filedata = $parts[1];

        if ($filetype !== 'data:image/png' || !\str_starts_with($filedata, "base64,")) {
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Bad image data'
            ]);
            return '';
        }

        $filedata = \substr($filedata, 7);

        $filename = \uniqid(\strval(\rand()), true);
        $filepath = "../uploads/$filename.png";

        \file_put_contents($filepath, \base64_decode($filedata));

        if (!$this->check_image_type($filepath)) {
            \unlink($filepath);
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Image file must be a PNG'
            ]);
            return '';
        }

        if (!$this->resize_image_file($filepath)) {
            \unlink($filepath);
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Failed to resize image'
            ]);
            return '';
        }

        return $filename;
    }

    private function convert_image_to_png(string $input, string $output): bool
    {
        $imagick_file = new \Imagick($input);
        return $imagick_file->writeImage($output);
    }

    private function upload_image_file(array $image_data): string
    {
        $filename = \uniqid(\strval(\rand()), true);
        $filepath = "../uploads/$filename.png";

        if ($image_data['type'] === 'image/png') {
            \move_uploaded_file($image_data['tmp_name'], $filepath);
        } else {
            if (\str_starts_with($image_data['type'], 'image/')) {
                $this->convert_image_to_png($image_data['tmp_name'], $filepath);
            } else {
                \http_response_code(400);
                echo \json_encode([
                    'error' => "Image file must be a PNG. Got {$image_data['type']}."
                ]);
                return '';
            }
        }

        if (!$this->check_image_type($filepath)) {
            \unlink($filepath);
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Image file must be a PNG.'
            ]);
            return '';
        }

        if (!$this->resize_image_file($filepath)) {
            \unlink($filepath);
            \http_response_code(400);
            echo \json_encode([
                'error' => 'Failed to resize image'
            ]);
            return '';
        }

        return $filename;
    }

    private function check_image_type(string $filepath): bool
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);
        $upload_finfo = $finfo->file($filepath);
        return $upload_finfo === 'image/png';
    }

    private function resize_image_file(string $filepath): bool
    {
        $image_size = \getimagesize($filepath);
        if ($image_size === false) {
            return false;
        }

        $image_width = $image_size[0];
        $image_height = $image_size[1];

        if ($image_width === 0 || $image_height === 0) {
            return false;
        }

        if ($image_width !== 720 || $image_height !== 540) {
            $imagick_file = new \Imagick($filepath);
            if (!$imagick_file->thumbnailImage(720, 540)) {
                return false;
            }
            $imagick_file->writeImage($filepath);
        }

        return true;
    }
}
