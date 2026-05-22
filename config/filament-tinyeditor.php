<?php

return [
    'version' => '8.0.2',

    'darkMode' => 'auto', // 'auto'|'force'|'class'|'media'|false

    'profiles' => [
        'default' => [
            'plugins' => 'autoresize codesample directionality advlist link image lists searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | styles | bold italic underline | alignleft aligncenter alignright | numlist bullist outdent indent | forecolor backcolor | blockquote table | image link media codesample emoticons | wordcount fullscreen',
        ],

        'simple' => [
            'plugins' => 'autoresize link lists emoticons wordcount',
            'toolbar' => 'removeformat | bold italic | numlist bullist | link emoticons',
        ],

        'minimal' => [
            'plugins' => 'link lists',
            'toolbar' => 'bold italic link numlist bullist',
        ],

        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | styles | bold italic underline | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table | image link anchor media codesample emoticons | visualblocks wordcount fullscreen help',
        ],
    ],

    'upload_disk' => env('FILESYSTEM_DISK', 'public'),
    'upload_directory' => 'uploads',
];
