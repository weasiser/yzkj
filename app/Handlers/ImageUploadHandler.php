<?php


namespace App\Handlers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
//use msonowal\LaravelTinify\Services\TinifyService;
use Str;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    public function save($file, $folder, $file_prefix, $file_processing_rule_name='', $max_width = false, $delete_path='')
    {
        // 构建存储的文件夹规则，值如：uploads/images/avatars/2017/09/21
        // 文件夹切割能让查找效率更高。
        $tmp_folder = "uploads/admin/images";
        $oss_folder = "images/$folder/" . date("Y/m/d", time());
        $folder_name = "uploads/admin/" . $oss_folder;

        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        if (config('filesystems.default') === 'oss') {
            $upload_path = public_path() . '/' . $tmp_folder;
        } else {
            $upload_path = public_path() . '/' . $folder_name;
        }

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';

        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        $file_name_without_extension = $file_prefix . '_' . time() . '_' . Str::random(10);
        $filename = $file_name_without_extension . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $file_path_name = $upload_path . '/' . $filename;

        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        if (config('filesystems.default') === 'oss') {
            $path = $oss_folder . '/' . $filename;
            $localFilePathName = $file_path_name;
            $oss_path = $this->uploadToOss($path, $localFilePathName);
            if ($delete_path) {
                $this->deleteFromOss($delete_path);
            }
            unlink($localFilePathName);
            return [
                'path' => $path,
                'full_path' => config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $path . $file_processing_rule_name : $oss_path . $file_processing_rule_name
            ];
        }

//        if ($extension === 'png') {
//            $this->transform_image($file_path_name, 'jpeg', $upload_path . '/' . $file_name_without_extension . '.jpeg');
//            $file_path_name = $upload_path . '/' . $file_name_without_extension . '.jpeg';
//        }

        $width = getimagesize($file_path_name)[0];

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $width > $max_width && $extension !== 'gif') {
//            if ($extension === 'png') {
//                $this->tinifyApi($upload_path . '/' . $filename, $max_width);
//            } else {
                //此类中封装的函数，用于裁剪图片
                $this->reduceSize($file_path_name, $max_width, $extension);
//            }
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width, $extension)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

//        if ($extension === 'png') {
//            $image->encode('jpg', 100);
//        }

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }

    protected function uploadToOss($path, $file)
    {
        $disk = Storage::disk('oss');
        $disk->put($path, file_get_contents($file));
        return $disk->getUrl($path);
    }

    protected function deleteFromOss($path)
    {
        $disk = Storage::disk('oss');
        return $disk->delete($path);
    }

//    protected function tinifyApi($file_path_name, $max_width)
//    {
//        $tinify = new TinifyService();
//        $tinify->fromFile($file_path_name)->resize(array(
//            "method" => "scale",
//            "width" => $max_width
//        ))->toFile($file_path_name);
//    }

    /**
     * 图片格式转换
     * @param string $image_path 文件路径或url
     * @param string $to_ext 待转格式，支持png,gif,jpeg,wbmp,webp,xbm
     * @param null|string $save_path 存储路径，null则返回二进制内容，string则返回true|false
     * @return boolean|string $save_path是null则返回二进制内容，是string则返回true|false
     * @throws Exception
     * @author klinson <klinson@163.com>
     */
//    protected function transform_image($image_path, $to_ext = 'jpeg', $save_path = null)
//    {
//        if (!in_array($to_ext, ['png', 'gif', 'jpeg', 'wbmp', 'webp', 'xbm'])) {
//            throw new \Exception('unsupport transform image to ' . $to_ext);
//        }
//        switch (exif_imagetype($image_path)) {
//            case IMAGETYPE_GIF :
//                $img = imagecreatefromgif($image_path);
//                break;
//            case IMAGETYPE_JPEG :
//            case IMAGETYPE_JPEG2000:
//                $img = imagecreatefromjpeg($image_path);
//                break;
//            case IMAGETYPE_PNG:
//                $img = imagecreatefrompng($image_path);
//                break;
//            case IMAGETYPE_BMP:
//            case IMAGETYPE_WBMP:
//                $img = imagecreatefromwbmp($image_path);
//                break;
//            case IMAGETYPE_XBM:
//                $img = imagecreatefromxbm($image_path);
//                break;
//            case IMAGETYPE_WEBP: //(从 PHP 7.1.0 开始支持)
//                $img = imagecreatefromwebp($image_path);
//                break;
//            default :
//                throw new \Exception('Invalid image type');
//        }
//        $function = 'image' . $to_ext;
//        if ($save_path) {
//            return $function($img, $save_path, 100);
//        } else {
//            $tmp = __DIR__ . '/' . uniqid() . '.' . $to_ext;
//            if ($function($img, $tmp)) {
//                $content = file_get_contents($tmp);
//                unlink($tmp);
//                return $content;
//            } else {
//                unlink($tmp);
//                throw new \Exception('the file ' . $tmp . ' can not write');
//            }
//        }
//    }
}
