<?php

return [
    'plugin' => [
        'name' => 'ForumUpload',
        'description' => 'Manage users forum files upload',
    ],
    'settings' => [
        'menu' => 'Forum files upload',
        'menu_desc' => 'Handle forum files upload limitations',
        'file_extensions_label' => 'Allowed file extensions',
        'file_extensions_comment' => 'Separated by comma. Eg: jpg, jpeg, png, gif, webp',
        'max_filesize_label' => 'Max filesize',
        'max_filesize_comment' => 'In kilobytes, leave empty to enable PHP limits',
        'files_per_post_label' => 'Files per post',
        'files_per_post_comment' => 'Leave empty for no limits',
    ],
    'errors' => [
        'file_upload_attribute' => 'for file uploads',
        'files_per_post' => "You can't upload more than :count files per post"
    ]
];
