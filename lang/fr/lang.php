<?php

return [
    'plugin' => [
        'name' => 'ForumUpload',
        'description' => 'Gestion des uploads utilisateurs sur le forum',
    ],
    'settings' => [
        'menu' => 'Chargement de fichiers du forum',
        'menu_desc' => 'Gestion des limitations des fichiers téléchargés sur le forum.',
        'file_extensions_label' => 'Extensions de fichiers autorisés',
        'file_extensions_comment' => 'Séparé par des virgules. Exemple: jpg, jpeg, png, gif, webp',
        'max_filesize_label' => 'Taille maximale',
        'max_filesize_comment' => 'En kilobytes, laisser vide pour activer les limitations de PHP',
        'files_per_post_label' => 'Fichiers par message',
        'files_per_post_comment' => 'Laisser vide pour aucune limitation',
    ],
    'errors' => [
        'file_upload_attribute' => 'de chargement des fichiers',
        'files_per_post' => "Vous ne pouvez pas chargé plus de :count fichiers par message"
    ]
];
