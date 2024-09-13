<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class ToolsController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $regex = '/' . preg_quote($this->removeVietnameseTones($query)) . '/i';

        $results = [];

        // Path to your content JSON files
        for ($i = 1; $i <= 11; $i++) {
            $filePath = resource_path("content/data-{$i}.json");

            if (File::exists($filePath)) {
                $jsonContent = json_decode(File::get($filePath), true);
                
                $filteredData = array_filter($jsonContent, function ($item) use ($regex) {
                    return preg_match($regex, $item['c']) || preg_match($regex, $item['no']) || preg_match($regex, $this->removeVietnameseTones($item['am']));
                });

                $results = array_merge($results, $filteredData);
            }
        }

        return response()->json($results);
    }

    private function removeVietnameseTones($str)
    {
        $str = preg_replace(
            [
                "/[àáạảãâầấậẩẫăằắặẳẵ]/u", "/[èéẹẻẽêềếệểễ]/u", "/[ìíịỉĩ]/u",
                "/[òóọỏõôồốộổỗơờớợởỡ]/u", "/[ùúụủũưừứựửữ]/u", "/[ỳýỵỷỹ]/u",
                "/[đ]/u", "/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u", "/[ÈÉẸẺẼÊỀẾỆỂỄ]/u", "/[ÌÍỊỈĨ]/u",
                "/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u", "/[ÙÚỤỦŨƯỪỨỰỬỮ]/u", "/[ỲÝỴỶỸ]/u", "/[Đ]/u"
            ], 
            [
                'a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D'
            ], 
            $str
        );

        return $str;
    }
}
