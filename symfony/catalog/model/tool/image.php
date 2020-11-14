<?php

class ModelToolImage extends Model
{

    function resize($filename, $width, $height, $watermark = true, $transparent = false)
    {
    
//by NOVIKOV
//зачем постоянно ресайзить картинки, если они уже есть в кэше    
	$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . ($transparent ? '.png' : '.jpg');
	if (file_exists(DIR_IMAGE . $new_image)) return HTTP_IMAGE . $new_image;
// иначе пробуем таки сделать ресайз
//////////////////////////////////////////////////////////////

    
        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            return;
        }

        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . ($transparent ? '.png' : '.jpg');
        $old_image = $filename;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            $image = new Image(DIR_IMAGE . $old_image);
            $image->resize($width, $height, $transparent);
            $image->save(DIR_IMAGE . $new_image);
        }

        if ($watermark && $this->getWatermark()) {
            $wm_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '-wm.jpg';

            if (!file_exists(DIR_IMAGE . $wm_image)) {
                $this->createImageWatermark($new_image, $wm_image, $width, $height);
            }
            $new_image = $wm_image;
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return HTTPS_IMAGE . $new_image;
        } else {
            return HTTP_IMAGE . $new_image;
        }
    }

    public function getWatermark()
    {

        if ($this->watemark === null) {
            global $registry;
            $config = $registry->get('config');
            $this->watermark = $config->get('config_watermark_logo') ? $config->get('config_watermark_logo') : '';
        }
        return $this->watermark;
    }

    public function createImageWatermark($image, $filename, $width, $height)
    {
        // resized watermark filename 
        $wm_resize = 'cache/data/wm-' . $width . 'x' . $height . '.png';

        // if resized watermark not ezist - resize watermark
        if (!file_exists(DIR_IMAGE . $wm_resize)) {
            $watermark = new Image(DIR_IMAGE . $this->getWatermark());
            $watermark->resize($width, $height, true); // with transparency
            $watermark->save(DIR_IMAGE . $wm_resize);
        }

        $watermark = new Image(DIR_IMAGE . $wm_resize);

        $image = new Image(DIR_IMAGE . $image);

        $watermark_width = $watermark->getWidth();

        $watermark_height = $watermark->getHeight();
        $watermark_pos_x = 0;
        $watermark_pos_y = 0;

        imagecopy($image->getImage(), $watermark->getImage(), $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
        imagedestroy($watermark->getImage());
        $image->save(DIR_IMAGE . $filename);
    }

    // CROP IMAGE
    function crop($filename, $width, $height, $position = 'center')
    {
        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            return;
        }

        $info = pathinfo($filename);
        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '_crop.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            $image = new Image(DIR_IMAGE . $old_image);
            $image->resize_crop($width, $height, $position);
            $image->save(DIR_IMAGE . $new_image);
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return HTTPS_IMAGE . $new_image;
        } else {
            return HTTP_IMAGE . $new_image;
        }
    }

    function resize_width($filename, $size)
    {
        if (!file_exists(DIR_IMAGE . $filename)) {
            return;
        }
 $info = pathinfo($filename);
        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $size . '_resize_width.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {

            $image = new Image(DIR_IMAGE . $old_image);
            $image->resize_width($size);
            $image->save(DIR_IMAGE . $new_image);
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return HTTPS_IMAGE . $new_image;
        } else {
            return HTTP_IMAGE . $new_image;
        }
    }

}

?>