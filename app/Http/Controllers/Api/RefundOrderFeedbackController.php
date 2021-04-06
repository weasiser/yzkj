<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\RefundOrderFeedbackRequest;
use App\Models\Order;
use App\Models\RefundOrderFeedback;
use App\Transformers\RefundOrderFeedbackTransformer;
use Illuminate\Http\Request;

class RefundOrderFeedbackController extends Controller
{
    public function store(RefundOrderFeedbackRequest $refundOrderFeedbackRequest, RefundOrderFeedbackTransformer $refundOrderFeedbackTransformer)
    {
        $content = $refundOrderFeedbackRequest->input('content');
        $order = Order::find($refundOrderFeedbackRequest->input('order_id'));
        $refundOrderFeedback = new RefundOrderFeedback([
            'content' => $content
        ]);
        $refundOrderFeedback->order()->associate($order);
        $refundOrderFeedback->save();
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
        ]);
        return $this->response->item($refundOrderFeedback, $refundOrderFeedbackTransformer)->setStatusCode(201);
    }

    public function show(RefundOrderFeedback $refundOrderFeedback, RefundOrderFeedbackTransformer $refundOrderFeedbackTransformer)
    {
        return $this->response->item($refundOrderFeedback, $refundOrderFeedbackTransformer);
    }

    public function index(RefundOrderFeedback $refundOrderFeedback, RefundOrderFeedbackTransformer $refundOrderFeedbackTransformer)
    {
        $refundOrderFeedbackList = $refundOrderFeedback->recentUnhandled()->paginate(5);
        return $this->response->paginator($refundOrderFeedbackList, $refundOrderFeedbackTransformer);
    }

    public function update(RefundOrderFeedback $refundOrderFeedback, RefundOrderFeedbackRequest $refundOrderFeedbackRequest, RefundOrderFeedbackTransformer $refundOrderFeedbackTransformer)
    {
        $this->authorize('own', $refundOrderFeedback);
        $content = $refundOrderFeedbackRequest->input('content');
        $refundOrderFeedback->content = $content;
        $refundOrderFeedback->save();
        return $this->response->item($refundOrderFeedback, $refundOrderFeedbackTransformer);
    }

    public function destroy(RefundOrderFeedback $refundOrderFeedback, ImageUploadHandler $imageUploadHandler)
    {
        $this->authorize('own', $refundOrderFeedback);
        foreach ($refundOrderFeedback->picture as $value) {
            $imageUploadHandler->deleteFromOss($value);
        }
        $refundOrderFeedback->order->update([
            'refund_status' => Order::REFUND_STATUS_PENDING,
        ]);
        $refundOrderFeedback->delete();
        return $this->response->noContent();
    }

    public function handle(RefundOrderFeedback $refundOrderFeedback)
    {
        $refundOrderFeedback->is_handled = true;
        $refundOrderFeedback->save();
        return $this->response->array([
            'handleResult' => true
        ]);
    }

    public function uploadPicture(Request $request, ImageUploadHandler $imageUploadHandler)
    {
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload) {
            $id = $request->input('id');
            // 保存图片到本地
            $result = $imageUploadHandler->save($file, 'refund_order_feedback', $id);
            // 图片保存成功的话
            if ($path = $result['path']) {
                $refundOrderFeedback = RefundOrderFeedback::find($id);
                $pictures = $refundOrderFeedback->picture;
                if (!$pictures) {
                    $pictures = [];
                }
                array_push($pictures, $path);
                $refundOrderFeedback->picture = $pictures;
                $refundOrderFeedback->save();
                $res = 'success';
            } else {
                $res = 'fail';
            }
        }
        return $this->response->array([
            'uploadImageResult' => $res
        ]);
    }

    public function deleteImage(RefundOrderFeedback $refundOrderFeedback, Request $request, ImageUploadHandler $imageUploadHandler)
    {
        $pictures = $refundOrderFeedback->picture;
        $index = $request->input('index');
        $imagePath = $pictures[$index];
        $result = $imageUploadHandler->deleteFromOss($imagePath);
        if ($result) {
            array_splice($pictures, $index, 1);
            $refundOrderFeedback->picture = $pictures;
            $refundOrderFeedback->save();
        }
        return $this->response->array([
            'deleteImageResult' => $result
        ]);
    }
}
