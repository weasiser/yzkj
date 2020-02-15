<?php


namespace App\Handlers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use msonowal\LaravelTinify\Services\TinifyService;
use Str;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width = false)
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
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        $width = getimagesize($upload_path . '/' . $filename)[0];

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $width > $max_width && $extension !== 'gif') {
//            if ($extension === 'png') {
//                $this->tinifyApi($upload_path . '/' . $filename, $max_width);
//            } else {
                // 此类中封装的函数，用于裁剪图片
                $this->reduceSize($upload_path . '/' . $filename, $max_width, $extension);
//            }
        }

        if (config('filesystems.default') === 'oss') {
            $path = $oss_folder . '/' . $filename;
            $localFilePathName = $upload_path . '/' . $filename;
            $oss_path = $this->uploadToOss($path, $localFilePathName);
            unlink($localFilePathName);
            return [
                'path' => config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $path : $oss_path
            ];
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width, $extension)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        if ($extension === 'png') {
            $image = $image->encode('jpg', 100);
        }

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

    protected function tinifyApi($file_path_name, $max_width)
    {
        $tinify = new TinifyService();
        $tinify->fromFile($file_path_name)->resize(array(
            "method" => "scale",
            "width" => $max_width
        ))->toFile($file_path_name);
    }
}
