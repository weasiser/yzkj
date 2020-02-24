<?php


namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CdnImage extends Image
{
    protected $cdnHost = ''; // <<== 立了一个Flag，标识是否支持上传新图返回cdn图片

    /**
     * Upload file and delete original file.
     *
     * @param UploadedFile $file
     *
     * @return mixed
     */
    protected function uploadAndDeleteOriginal(UploadedFile $file)
    {
        $this->renameIfExists($file);

        $path = null;

        if (!is_null($this->storagePermission)) {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
            if ($this->cdnHost){ // <<== 论 Flag 的正确用法 。。。
                $path = $this->cdnHost . '/' . $path;
            }
        } else {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name);
        }

        $this->destroy();

        return $path;
    }

    public function cdn()
    {
        $this->cdnHost = config('filesystems.disks.oss.cdnDomain');
        return $this;
    }
}
