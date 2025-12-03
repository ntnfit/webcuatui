<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $bank = $request->input('bank'); // Nhận giá trị bank từ request
        $regex = '%'.preg_quote($this->removeVietnameseTones($query)).'%';

        // Tìm kiếm trong bảng sao_ke với điều kiện bank
        $results = DB::table('saoke')
            ->where(function ($q) use ($regex) {
                $q->where('c', 'like', $regex)
                    ->orWhere('no', 'like', $regex)
                    ->orWhereRaw('LOWER(am) like ?', [strtolower($regex)])
                    ->orWhereRaw('LOWER(am) like ?', [strtolower($this->removeVietnameseTones($regex))]);
            })
            ->when($bank, function ($q) use ($bank) {
                return $q->where('bank', $bank); // Thêm điều kiện where cho cột bank nếu có giá trị
            })
            ->get();

        return response()->json($results);
    }

    private function removeVietnameseTones($str)
    {
        $str = preg_replace(
            [
                '/[àáạảãâầấậẩẫăằắặẳẵ]/u', '/[èéẹẻẽêềếệểễ]/u', '/[ìíịỉĩ]/u',
                '/[òóọỏõôồốộổỗơờớợởỡ]/u', '/[ùúụủũưừứựửữ]/u', '/[ỳýỵỷỹ]/u',
                '/[đ]/u', '/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u', '/[ÈÉẸẺẼÊỀẾỆỂỄ]/u', '/[ÌÍỊỈĨ]/u',
                '/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u', '/[ÙÚỤỦŨƯỪỨỰỬỮ]/u', '/[ỲÝỴỶỸ]/u', '/[Đ]/u',
            ],
            [
                'a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'u', 'Y', 'D',
            ],
            $str
        );

        return $str;
    }
}
