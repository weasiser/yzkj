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
        $refundOrderFeedbackList = $refundOrderFeedback->recent()->paginate(5);
        return $this->response->paginator($refundOrderFeedbackList, $refundOrderFeedbackTransformer);
    }

    public function uploadPicture(Request $request, ImageUploadHandler $imageUploadHandler)
    {
        // 初始化返回数据，默认是失败的
        $data = 'fail';
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload) {
            $id = $request->input('id');
            // 保存图片到本地
            $result = $imageUploadHandler->save($file, 'refund_order_feedback', $id);
            // 图片保存成功的话
            if ($result) {
                $data = $result['path'];
            }
            $refundOrderFeedback = RefundOrderFeedback::find($id);
            $pictures = $refundOrderFeedback->picture;
            if (!$pictures) {
                $pictures = [];
            }
            array_push($pictures, $result['path']);
            $refundOrderFeedback->picture = $pictures;
            $refundOrderFeedback->save();
        }
        return $data;
    }
}
